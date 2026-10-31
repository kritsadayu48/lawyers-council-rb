<?php

namespace App\Services;

use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FacebookImportService
{
    /**
     * รายชื่อเดือนภาษาไทยสำหรับแปลงวันที่
     */
    protected static array $thaiMonths = [
        'มกราคม' => 1, 'กุมภาพันธ์' => 2, 'มีนาคม' => 3, 'เมษายน' => 4,
        'พฤษภาคม' => 5, 'มิถุนายน' => 6, 'กรกฎาคม' => 7, 'สิงหาคม' => 8,
        'กันยายน' => 9, 'ตุลาคม' => 10, 'พฤศจิกายน' => 11, 'ธันวาคม' => 12,
        'ม.ค.' => 1, 'ก.พ.' => 2, 'มี.ค.' => 3, 'เม.ย.' => 4,
        'พ.ค.' => 5, 'มิ.ย.' => 6, 'ก.ค.' => 7, 'ส.ค.' => 8,
        'ก.ย.' => 9, 'ต.ค.' => 10, 'พ.ย.' => 11, 'ธ.ค.' => 12,
    ];

    /**
     * วิเคราะห์ข้อความเพื่อแยกหัวข้อ (Title), วันที่ (Published Date), และเนื้อหาสำหรับ RichEditor
     */
    public static function parseText(string $rawText): array
    {
        $rawText = str_replace(["\r\n", "\r"], "\n", trim($rawText));
        if (empty($rawText)) {
            return [
                'title' => 'ข่าวสารและกิจกรรมสภาทนายความ (' . date('d/m/Y') . ')',
                'content' => '<p>ไม่มีรายละเอียดข้อความ</p>',
                'published_at' => now(),
            ];
        }

        $lines = explode("\n", $rawText);
        $nonEmptyLines = array_values(array_filter(array_map('trim', $lines), fn ($l) => $l !== ''));

        // ตรวจสอบและดึงวันที่จากข้อความ (เช่น "วันที่ 24 ตุลาคม 2569" หรือ "24 ต.ค. 2569")
        $detectedDate = null;
        if (preg_match('/(?:วัน(?:ที่)?\s*)?(\d{1,2})\s+([ก-๙\.]+)\s+(\d{4})/u', $rawText, $dateMatches)) {
            $day = (int) $dateMatches[1];
            $monthName = $dateMatches[2];
            $year = (int) $dateMatches[3];
            if ($year > 2400) {
                $year -= 543; // แปลง พ.ศ. เป็น ค.ศ.
            }
            $month = self::$thaiMonths[$monthName] ?? null;
            if ($month && checkdate($month, $day, $year)) {
                $detectedDate = Carbon::create($year, $month, $day, 9, 0, 0);
            }
        }

        // วิเคราะห์หาหัวข้อข่าวที่แท้จริง (หลีกเลี่ยงการนำบรรทัดที่เป็นแค่วันที่มาเป็นหัวข้อ)
        $firstLine = $nonEmptyLines[0] ?? 'ข่าวสารและกิจกรรมสภาทนายความ';
        $title = $firstLine;

        $isDateOnly = preg_match('/^(?:วัน(?:ที่|จันทร์|อังคาร|พุธ|พฤหัสบดี|ศุกร์|เสาร์|อาทิตย์)?\s*\d+|เมื่อวัน(?:ที่)?\s*\d+)/u', $firstLine)
            || (mb_strlen($firstLine) < 35 && preg_match('/(มกราคม|กุมภาพันธ์|มีนาคม|เมษายน|พฤษภาคม|มิถุนายน|กรกฎาคม|สิงหาคม|กันยายน|ตุลาคม|พฤศจิกายน|ธันวาคม|\d{4})/u', $firstLine));

        if ($isDateOnly && isset($nonEmptyLines[1])) {
            $title = $nonEmptyLines[1];
        }

        $title = Str::limit($title, 220, '...');

        // จัดฟอร์แมตเนื้อหาให้อยู่ในรูปแท็ก <p>...</p> สำหรับ RichEditor
        $paragraphs = [];
        $currentPara = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if (!empty($currentPara)) {
                    $paragraphs[] = '<p>' . implode('<br>', $currentPara) . '</p>';
                    $currentPara = [];
                }
            } else {
                $currentPara[] = htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!empty($currentPara)) {
            $paragraphs[] = '<p>' . implode('<br>', $currentPara) . '</p>';
        }

        $contentHtml = implode("\n", $paragraphs);
        if (empty($contentHtml)) {
            $contentHtml = '<p>' . htmlspecialchars($rawText, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        return [
            'title' => $title,
            'content' => $contentHtml,
            'published_at' => $detectedDate ?? now(),
        ];
    }

    /**
     * ดาวน์โหลดรูปภาพจาก URL ของ Facebook CDN มาเก็บลงใน Storage เครื่องโดยตรง
     * มีการตรวจสอบ Magic Bytes (รูปภาพจริง) และบันทึกลงทั้ง storage/app/public และ public/storage
     */
    public static function downloadImage(string $url, string $subfolder = 'news-covers'): ?string
    {
        $url = html_entity_decode(trim($url));
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // กรอง URL ที่เป็นรูปโปรไฟล์, ไอคอน, สติกเกอร์ หรือภาพเงา silhouette ออกทันที
        $urlLower = strtolower($url);
        if (
            str_contains($urlLower, 'p50x50') ||
            str_contains($urlLower, 'p100x100') ||
            str_contains($urlLower, 'p160x160') ||
            str_contains($urlLower, 'p200x200') ||
            str_contains($urlLower, 'emoji') ||
            str_contains($urlLower, 'rsrc.php') ||
            str_contains($urlLower, 'silhouette') ||
            str_contains($urlLower, 'profile_pic')
        ) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer' => 'https://www.facebook.com/',
            ])->timeout(25)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $body = $response->body();
            // ตรวจสอบขนาดไฟล์: ภาพถ่ายข่าวกิจกรรมจริงต้องมีขนาดอย่างน้อย 8KB ขึ้นไป
            if (strlen($body) < 8000) {
                return null;
            }

            // ตรวจสอบ Magic Bytes ว่าเป็นรูปภาพจริง (JPEG, PNG, WEBP, GIF) ป้องกันไฟล์ HTML Error 403 ถูกบันทึกเป็นรูป
            $isRealImage = str_starts_with($body, "\xFF\xD8\xFF") // JPEG
                || str_starts_with($body, "\x89PNG\r\n\x1a\n") // PNG
                || str_starts_with($body, "RIFF") // WEBP
                || str_starts_with($body, "GIF8"); // GIF

            if (!$isRealImage) {
                return null;
            }

            $extension = 'jpg';
            if (str_starts_with($body, "\x89PNG")) {
                $extension = 'png';
            } elseif (str_starts_with($body, "RIFF")) {
                $extension = 'webp';
            }

            $filename = 'fb_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $extension;
            $path = $subfolder . '/' . $filename;

            // 1. บันทึกใน storage/app/public
            $appPath = storage_path('app/public/' . $path);
            @mkdir(dirname($appPath), 0777, true);
            @file_put_contents($appPath, $body);

            // 2. หาก public/storage เป็นโฟลเดอร์จริง (ไม่ใช่ symlink) ให้บันทึกคู่ขนานเพื่อความชัวร์ 100%
            $pubPath = public_path('storage/' . $path);
            if (file_exists(public_path('storage')) && !is_link(public_path('storage'))) {
                @mkdir(dirname($pubPath), 0777, true);
                @file_put_contents($pubPath, $body);
            }

            return $path;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * ดึงข้อมูลโพสต์และรูปภาพทั้งหมดจากลิงก์ Facebook (รองรับ Google Cloud Run Scraper)
     */
    public static function scrapeFacebookUrl(string $url): array
    {
        $result = [
            'title' => '',
            'content' => '',
            'images' => [],
            'error' => null,
        ];

        // 1. เรียกใช้งาน Google Cloud Run Scraper หากมีการตั้งค่าใน .env
        $scraperUrl = config('services.facebook_scraper.url');
        if (!empty($scraperUrl)) {
            try {
                $response = Http::timeout(75)->post(rtrim($scraperUrl, '/') . '/scrape', [
                    'url' => $url,
                    'token' => config('services.facebook_scraper.token'),
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['success'])) {
                        $rawText = $json['text'] ?? '';
                        $parsed = self::parseText($rawText);

                        return [
                            'title' => $parsed['title'],
                            'content' => $parsed['content'],
                            'raw_text' => $rawText,
                            'published_at' => $parsed['published_at'],
                            'images' => $json['images'] ?? [],
                            'error' => null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // 2. สำรอง: หากยังไม่ได้ตั้ง Cloud Scraper หรือ Scraper ไม่พร้อม ให้ใช้วิธีดึงผ่าน Open Graph
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'th-TH,th;q=0.9,en;q=0.8',
            ])->timeout(15)->get($url);

            if ($response->successful()) {
                $html = $response->body();
                if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/is', $html, $mTitle)) {
                    $result['title'] = html_entity_decode(trim($mTitle[1]), ENT_QUOTES, 'UTF-8');
                }
                if (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\']/is', $html, $mDesc)) {
                    $result['content'] = html_entity_decode(trim($mDesc[1]), ENT_QUOTES, 'UTF-8');
                }
                if (preg_match('/<meta[^>]+(?:property=["\']og:image["\']|name=["\']twitter:image["\'])[^>]+content=["\'](.*?)["\']/is', $html, $mImg)) {
                    $imgUrl = html_entity_decode(trim($mImg[1]), ENT_QUOTES, 'UTF-8');
                    if (!empty($imgUrl)) {
                        $result['images'][] = $imgUrl;
                    }
                }
            }
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * ดำเนินการสร้างข่าวสารและดาวน์โหลดรูปภาพเบื้องต้น (สร้าง Record พร้อมรูปปกและรูปแรกๆ)
     */
    public static function processImport(
        string $text,
        array $imageUrls = [],
        ?int $categoryId = null,
        ?Carbon $publishedAt = null,
        bool $isPublished = true
    ): News {
        @set_time_limit(180);
        // 1. แยก Title, Content และตรวจสอบวันที่
        $parsed = self::parseText($text);
        $title = $parsed['title'];
        $content = $parsed['content'];
        $finalPublishedAt = $publishedAt ?? $parsed['published_at'];

        // 2. หมวดหมู่เริ่มต้น
        if (!$categoryId) {
            $defaultCat = Category::where('type', 'news')->where('name', 'like', '%ประชาสัมพันธ์%')->first()
                ?? Category::where('type', 'news')->first();
            $categoryId = $defaultCat ? $defaultCat->id : 7;
        }

        // 3. กรองและดาวน์โหลดเฉพาะรูปภาพจริง (> 8KB)
        $coverImage = null;
        $galleryImages = [];

        $uniqueImageUrls = array_values(array_unique(array_filter($imageUrls)));

        foreach ($uniqueImageUrls as $imageUrl) {
            if (!$coverImage) {
                // รูปแรกที่ผ่านเกณฑ์ บันทึกเป็นรูปปกข่าว
                $downloaded = self::downloadImage($imageUrl, 'news-covers');
                if ($downloaded) {
                    $coverImage = $downloaded;
                    continue;
                }
            }

            // รูปถัดมาที่ผ่านเกณฑ์ บันทึกเป็นคลังภาพกิจกรรม
            $downloaded = self::downloadImage($imageUrl, 'news-galleries');
            if ($downloaded) {
                $galleryImages[] = $downloaded;
            }
        }

        // 4. บันทึกข้อมูลข่าวสารลงฐานข้อมูล
        return News::create([
            'category_id' => $categoryId,
            'title' => $title,
            'content' => $content,
            'cover_image' => $coverImage,
            'gallery_images' => $galleryImages,
            'published_at' => $finalPublishedAt,
            'is_published' => $isPublished,
        ]);
    }

    /**
     * ดาวน์โหลดรูปภาพเพิ่มเติมแบบ Batch และเพิ่มเข้าสู่ gallery_images ของข่าวที่มีอยู่แล้ว
     */
    public static function appendGalleryImages(News $news, array $imageUrls): int
    {
        $existing = $news->gallery_images ?? [];
        if (!is_array($existing)) {
            $existing = [];
        }

        $addedCount = 0;
        foreach ($imageUrls as $imageUrl) {
            $downloaded = self::downloadImage($imageUrl, 'news-galleries');
            if ($downloaded) {
                $existing[] = $downloaded;
                $addedCount++;
            }
        }

        $news->gallery_images = array_values(array_unique($existing));

        // หากยังไม่มีรูปปก ให้เอารูปแรกที่ดาวน์โหลดได้มาเป็นรูปปก
        if (empty($news->cover_image) && count($news->gallery_images) > 0) {
            $first = array_shift($news->gallery_images);
            // คัดลอกไป news-covers
            $newCover = str_replace('news-galleries/', 'news-covers/', $first);
            @copy(storage_path('app/public/' . $first), storage_path('app/public/' . $newCover));
            $news->cover_image = $newCover;
        }

        $news->save();
        return $addedCount;
    }

    /**
     * สร้างโค้ด Bookmarklet JavaScript ขั้นสูง ที่มีระบบ Auto-Scroll
     * เลื่อนหน้าจออัตโนมัติเพื่อสแกนและดึงรูปภาพทั้งหมดในอัลบั้ม (แม้มี 70+ รูป)
     */
    public static function generateBookmarkletJs(string $targetUrl): string
    {
        if (!str_contains($targetUrl, 'localhost') && !str_contains($targetUrl, '127.0.0.1')) {
            $targetUrl = preg_replace('/^http:/i', 'https:', $targetUrl);
        }

        $rawJs = <<<JS
javascript:(function(){
    if (window._fbImportRunning) return;
    window._fbImportRunning = true;

    // สร้าง UI Banner ลอยบนหน้าจอ Facebook
    var bar = document.createElement('div');
    bar.id = 'fb-import-bar';
    bar.style.cssText = 'position:fixed;top:15px;left:50%;transform:translateX(-50%);z-index:9999999;background:#0f172a;color:#fff;padding:12px 22px;border-radius:18px;box-shadow:0 15px 35px rgba(0,0,0,0.6);font-family:sans-serif;font-size:13px;display:flex;align-items:center;gap:15px;border:2px solid #3b82f6;';
    bar.innerHTML = '<div style="display:flex;align-items:center;gap:10px;"><div style="width:14px;height:14px;border:2px solid #3b82f6;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;"></div><div><div style="font-weight:bold;color:#fff;">📸 กำลังสแกนรูปภาพในโพสต์/อัลบั้ม...</div><div id="fb-import-cnt" style="font-size:11px;color:#94a3b8;">พบแล้ว 0 รูป (กำลังเลื่อนหน้าจออัตโนมัติ)</div></div></div><button id="fb-import-stop" style="background:#2563eb;color:#fff;border:none;padding:6px 14px;border-radius:10px;font-weight:bold;font-size:12px;cursor:pointer;">ส่งข้อมูลทันที</button>';
    
    var style = document.createElement('style');
    style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
    document.head.appendChild(style);
    document.body.appendChild(bar);

    // ขยายข้อความ ดูเพิ่มเติม
    try {
        var buttons = document.querySelectorAll('div[role="button"]');
        for (var i = 0; i < buttons.length; i++) {
            var txt = buttons[i].innerText || '';
            if (txt.indexOf('ดูเพิ่มเติม') !== -1 || txt.indexOf('See more') !== -1) {
                buttons[i].click();
            }
        }
    } catch(e) {}

    // ดึงข้อความโพสต์
    var text = window.getSelection().toString().trim();
    if (!text) {
        var post = document.querySelector('div[role="article"]') || document.querySelector('div[data-pagelet*="FeedUnit"]') || document.body;
        var msgEl = post.querySelector('div[data-ad-preview="message"]') || post.querySelector('div[dir="auto"]');
        text = msgEl ? msgEl.innerText.trim() : (post.innerText || '');
    }

    var imgs = [];
    function scanImages() {
        var all = document.querySelectorAll('img');
        for (var j = 0; j < all.length; j++) {
            var img = all[j];
            var src = img.src || '';
            if (!src || (src.indexOf('scontent') === -1 && src.indexOf('fbcdn') === -1)) continue;
            if (src.indexOf('emoji') !== -1 || src.indexOf('rsrc.php') !== -1 || src.indexOf('p50x50') !== -1 || src.indexOf('p100x100') !== -1 || src.indexOf('p160x160') !== -1 || src.indexOf('silhouette') !== -1) continue;
            var alt = (img.alt || '').toLowerCase();
            if (alt.indexOf('profile') !== -1 || alt.indexOf('โปรไฟล์') !== -1) continue;
            var w = img.naturalWidth || img.width || 0;
            var h = img.naturalHeight || img.height || 0;
            if (w >= 160 && h >= 140) {
                if (imgs.indexOf(src) === -1) {
                    imgs.push(src);
                }
            }
        }
        var cntEl = document.getElementById('fb-import-cnt');
        if (cntEl) cntEl.innerText = 'พบแล้ว ' + imgs.length + ' รูป (กำลังเลื่อนหน้าจออัตโนมัติ)';
    }

    // สแกนรอบแรกทันที
    scanImages();

    var scrollTicks = 0;
    var maxTicks = 18; // เลื่อนประมาณ 4-5 วินาที
    var prevCount = 0;
    var stableTicks = 0;

    var timer = setInterval(function() {
        window.scrollBy(0, 900);
        scanImages();
        scrollTicks++;

        if (imgs.length === prevCount) {
            stableTicks++;
        } else {
            stableTicks = 0;
            prevCount = imgs.length;
        }

        // หากถึงด้านล่างหรือไม่มีรูปใหม่เพิ่มขึ้นแล้ว ให้ส่งข้อมูล
        if (scrollTicks >= maxTicks || stableTicks >= 5) {
            finishAndSend();
        }
    }, 280);

    document.getElementById('fb-import-stop').onclick = function() {
        finishAndSend();
    };

    function finishAndSend() {
        if (!window._fbImportRunning) return;
        window._fbImportRunning = false;
        clearInterval(timer);

        var cntEl = document.getElementById('fb-import-cnt');
        if (cntEl) cntEl.innerText = '✅ สแกนสำเร็จพบ ' + imgs.length + ' รูป! กำลังเปิดหน้าเว็บสภา...';

        setTimeout(function() {
            if (bar && bar.parentNode) bar.parentNode.removeChild(bar);

            var payload = {
                text: text,
                images: imgs,
                source_url: window.location.href,
                time: new Date().getTime()
            };

            var hashData = encodeURIComponent(JSON.stringify(payload));
            var destUrl = '{$targetUrl}#data=' + hashData;
            window.open(destUrl, '_blank');
        }, 700);
    }
})();
JS;

        return preg_replace('/\s+/', ' ', trim($rawJs));
    }
}
