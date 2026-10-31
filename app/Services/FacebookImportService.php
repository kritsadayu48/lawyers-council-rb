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
                'title' => 'ข่าวสารสภาทนายความ (' . date('d/m/Y H:i') . ')',
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
        $firstLine = $nonEmptyLines[0] ?? 'ข่าวสารและประกาศสภาทนายความ';
        $title = $firstLine;

        $isDateOnly = preg_match('/^(?:วัน(?:ที่|จันทร์|อังคาร|พุธ|พฤหัสบดี|ศุกร์|เสาร์|อาทิตย์)?\s*\d+|เมื่อวัน(?:ที่)?\s*\d+)/u', $firstLine)
            || (mb_strlen($firstLine) < 35 && preg_match('/(มกราคม|กุมภาพันธ์|มีนาคม|เมษายน|พฤษภาคม|มิถุนายน|กรกฎาคม|สิงหาคม|กันยายน|ตุลาคม|พฤศจิกายน|ธันวาคม|\d{4})/u', $firstLine));

        if ($isDateOnly && isset($nonEmptyLines[1])) {
            // บรรทัดแรกเป็นแค่วันที่ ให้ใช้บรรทัดที่ 2 เป็นหัวข้อข่าว
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
     * มีระบบกรองขนาดไฟล์: หากเล็กกว่า 10KB (รูปเงา silhouette, รูปโปรไฟล์จิ๋ว หรือ 388B tracking pixel) จะปฏิเสธทันที
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
            // ตรวจสอบขนาดไฟล์: ภาพถ่ายข่าวกิจกรรมจริงต้องมีขนาดอย่างน้อย 10KB (10,000 bytes) ขึ้นไป
            // หากน้อยกว่านี้มักเป็น 388 bytes tracking gif หรือภาพ silhouette ว่างเปล่า
            if (strlen($body) < 10000) {
                return null;
            }

            // ตรวจสอบนามสกุลไฟล์จาก Content-Type
            $contentType = strtolower($response->header('Content-Type') ?? '');
            $extension = 'jpg';
            if (str_contains($contentType, 'png')) {
                $extension = 'png';
            } elseif (str_contains($contentType, 'webp')) {
                $extension = 'webp';
            }

            $filename = 'fb_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $extension;
            $path = $subfolder . '/' . $filename;

            // บันทึกไฟล์ลงใน public disk
            Storage::disk('public')->put($path, $body);

            return $path;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * พยายามอ่านข้อมูล Open Graph จากลิงก์โพสต์ Facebook
     */
    public static function scrapeFacebookUrl(string $url): array
    {
        $result = [
            'title' => '',
            'content' => '',
            'images' => [],
            'error' => null,
        ];

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'th-TH,th;q=0.9,en;q=0.8',
            ])->timeout(15)->get($url);

            if (!$response->successful()) {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
                    'Accept-Language' => 'th-TH,th;q=0.9,en;q=0.8',
                ])->timeout(15)->get($url);
            }

            if (!$response->successful()) {
                $result['error'] = 'Facebook ปฏิเสธการเข้าถึงจากเซิร์ฟเวอร์ (HTTP ' . $response->status() . ')';
                return $result;
            }

            $html = $response->body();

            if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/is', $html, $mTitle)) {
                $result['title'] = html_entity_decode(trim($mTitle[1]), ENT_QUOTES, 'UTF-8');
            }

            if (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\']/is', $html, $mDesc)) {
                $result['content'] = html_entity_decode(trim($mDesc[1]), ENT_QUOTES, 'UTF-8');
            }

            $imageUrls = [];
            if (preg_match_all('/<meta[^>]+property=["\']og:image(?::secure_url)?["\'][^>]+content=["\'](.*?)["\']/is', $html, $mImages)) {
                foreach ($mImages[1] as $img) {
                    $cleanImg = html_entity_decode(trim($img));
                    if (!empty($cleanImg) && !in_array($cleanImg, $imageUrls)) {
                        $imageUrls[] = $cleanImg;
                    }
                }
            }

            if (preg_match_all('/https?:\\\\?\/\\\\?\/[^\s"\'<>]*?(?:fbcdn\.net|scontent[a-z0-9\.-]*?\.net)[^\s"\'<>]*/i', $html, $mCdn)) {
                foreach ($mCdn[0] as $rawUrl) {
                    $cleaned = stripslashes(html_entity_decode($rawUrl));
                    if (str_contains($cleaned, 'emoji') || str_contains($cleaned, 'rsrc.php') || str_contains($cleaned, 'p50x50') || str_contains($cleaned, 'silhouette')) {
                        continue;
                    }
                    if (!in_array($cleaned, $imageUrls)) {
                        $imageUrls[] = $cleaned;
                    }
                }
            }

            $result['images'] = array_slice($imageUrls, 0, 20);

            if (empty($result['title']) || str_contains($result['title'], 'Facebook') || str_contains($result['title'], 'สภาทนายความ')) {
                if (!empty($result['content'])) {
                    $parsed = self::parseText($result['content']);
                    $result['title'] = $parsed['title'];
                }
            }
        } catch (\Throwable $e) {
            $result['error'] = 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * ดำเนินการสร้างข่าวสารและดาวน์โหลดรูปภาพทั้งหมด
     */
    public static function processImport(
        string $text,
        array $imageUrls = [],
        ?int $categoryId = null,
        ?Carbon $publishedAt = null,
        bool $isPublished = true
    ): News {
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

        // 3. กรองและดาวน์โหลดเฉพาะรูปภาพจริง (> 10KB)
        $coverImage = null;
        $galleryImages = [];

        $uniqueImageUrls = array_values(array_unique(array_filter($imageUrls)));

        foreach ($uniqueImageUrls as $imageUrl) {
            if (!$coverImage) {
                // พยายามดาวน์โหลดรูปแรกที่ผ่านเกณฑ์เป็นรูปปก
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
     * สร้างโค้ด Bookmarklet JavaScript ขั้นสูง ที่ส่งข้อมูลผ่าน URL Hash
     * ป้องกันปัญหา SameSite Cookies และป้องกันการเด้งไปหน้า Login/Admin
     */
    public static function generateBookmarkletJs(string $targetUrl): string
    {
        // บังคับใช้ HTTPS หากไม่ใช่ localhost
        if (!str_contains($targetUrl, 'localhost') && !str_contains($targetUrl, '127.0.0.1')) {
            $targetUrl = preg_replace('/^http:/i', 'https:', $targetUrl);
        }

        $rawJs = <<<JS
javascript:(function(){
    try {
        var buttons = document.querySelectorAll('div[role="button"]');
        for (var i = 0; i < buttons.length; i++) {
            var txt = buttons[i].innerText || '';
            if (txt.indexOf('ดูเพิ่มเติม') !== -1 || txt.indexOf('See more') !== -1) {
                buttons[i].click();
            }
        }
    } catch(e) {}

    setTimeout(function(){
        var text = '';
        var sel = window.getSelection().toString().trim();
        if (sel) {
            text = sel;
        } else {
            var post = document.querySelector('div[role="article"]') || document.querySelector('div[data-pagelet*="FeedUnit"]') || document.body;
            var msgEl = post.querySelector('div[data-ad-preview="message"]') || post.querySelector('div[dir="auto"]');
            text = msgEl ? msgEl.innerText.trim() : (post.innerText || '');
        }

        if (!text) {
            alert('❌ ไม่พบข้อความโพสต์ กรุณาคลิกเลือกคลุมข้อความบนโพสต์ที่ต้องการ แล้วกดปุ่มนี้อีกครั้งครับ');
            return;
        }

        var post = document.querySelector('div[role="article"]') || document;
        var imgs = [];
        var allImgs = post.querySelectorAll('img');
        for (var j = 0; j < allImgs.length; j++) {
            var img = allImgs[j];
            var src = img.src || '';
            if (!src || (src.indexOf('scontent') === -1 && src.indexOf('fbcdn') === -1)) continue;
            if (src.indexOf('emoji') !== -1 || src.indexOf('rsrc.php') !== -1 || src.indexOf('p50x50') !== -1 || src.indexOf('p100x100') !== -1 || src.indexOf('p160x160') !== -1 || src.indexOf('silhouette') !== -1) continue;
            var alt = (img.alt || '').toLowerCase();
            if (alt.indexOf('profile') !== -1 || alt.indexOf('โปรไฟล์') !== -1) continue;
            var w = img.naturalWidth || img.width || 0;
            var h = img.naturalHeight || img.height || 0;
            if (w >= 200 && h >= 160 && imgs.indexOf(src) === -1) {
                imgs.push(src);
            }
        }

        var payload = {
            text: text,
            images: imgs,
            source_url: window.location.href,
            time: new Date().getTime()
        };

        var hashData = encodeURIComponent(JSON.stringify(payload));
        var destUrl = '{$targetUrl}#data=' + hashData;
        window.open(destUrl, '_blank');
    }, 400);
})();
JS;

        return preg_replace('/\s+/', ' ', trim($rawJs));
    }
}
