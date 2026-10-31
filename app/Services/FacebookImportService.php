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
     * วิเคราะห์ข้อความเพื่อแยกหัวข้อ (Title) และเนื้อหาในรูปแบบ HTML สำหรับ RichEditor
     */
    public static function parseText(string $rawText): array
    {
        $rawText = str_replace(["\r\n", "\r"], "\n", trim($rawText));
        if (empty($rawText)) {
            return [
                'title' => 'ข่าวสารสภาทนายความ (' . date('d/m/Y H:i') . ')',
                'content' => '<p>ไม่มีรายละเอียดข้อความ</p>',
            ];
        }

        $lines = explode("\n", $rawText);
        $nonEmptyLines = array_values(array_filter(array_map('trim', $lines), fn ($l) => $l !== ''));

        // บรรทัดแรกเป็นหัวข้อ (จำกัดความยาวไม่เกิน 240 ตัวอักษรตามขนาดฟิลด์ VARCHAR 255)
        $firstLine = $nonEmptyLines[0] ?? 'ข่าวสารและประกาศสภาทนายความ';
        $title = Str::limit($firstLine, 240, '...');

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
        ];
    }

    /**
     * ดาวน์โหลดรูปภาพจาก URL ของ Facebook CDN มาเก็บลงใน Storage เครื่องโดยตรง
     */
    public static function downloadImage(string $url, string $subfolder = 'news-covers'): ?string
    {
        $url = html_entity_decode(trim($url));
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer' => 'https://www.facebook.com/',
            ])->timeout(25)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $body = $response->body();
            if (empty($body)) {
                return null;
            }

            // ตรวจสอบนามสกุลไฟล์จาก Content-Type
            $contentType = strtolower($response->header('Content-Type') ?? '');
            $extension = 'jpg';
            if (str_contains($contentType, 'png')) {
                $extension = 'png';
            } elseif (str_contains($contentType, 'webp')) {
                $extension = 'webp';
            } elseif (str_contains($contentType, 'gif')) {
                $extension = 'gif';
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
                // ลองสำรองด้วย Mobile User-Agent
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

            // 1. ดึง og:title
            if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/is', $html, $mTitle)) {
                $result['title'] = html_entity_decode(trim($mTitle[1]), ENT_QUOTES, 'UTF-8');
            }

            // 2. ดึง og:description
            if (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\']/is', $html, $mDesc)) {
                $result['content'] = html_entity_decode(trim($mDesc[1]), ENT_QUOTES, 'UTF-8');
            }

            // 3. ดึง og:image หรือรูปภาพ Facebook CDN
            $imageUrls = [];
            if (preg_match_all('/<meta[^>]+property=["\']og:image(?::secure_url)?["\'][^>]+content=["\'](.*?)["\']/is', $html, $mImages)) {
                foreach ($mImages[1] as $img) {
                    $cleanImg = html_entity_decode(trim($img));
                    if (!empty($cleanImg) && !in_array($cleanImg, $imageUrls)) {
                        $imageUrls[] = $cleanImg;
                    }
                }
            }

            // ค้นหารูป CDN เพิ่มเติมที่ซ่อนอยู่ใน HTML
            if (preg_match_all('/https?:\\\\?\/\\\\?\/[^\s"\'<>]*?(?:fbcdn\.net|scontent[a-z0-9\.-]*?\.net)[^\s"\'<>]*/i', $html, $mCdn)) {
                foreach ($mCdn[0] as $rawUrl) {
                    $cleaned = stripslashes(html_entity_decode($rawUrl));
                    // กรองรูปขนาดเล็ก ไอคอน หรือรูปโปรไฟล์ที่ไม่เกี่ยวข้อง
                    if (str_contains($cleaned, 'emoji') || str_contains($cleaned, 'rsrc.php') || str_contains($cleaned, 'p50x50')) {
                        continue;
                    }
                    if (!in_array($cleaned, $imageUrls)) {
                        $imageUrls[] = $cleaned;
                    }
                }
            }

            $result['images'] = array_slice($imageUrls, 0, 15);

            // หาก og:title เป็นชื่อเพจ ให้พยายามตัดหัวข้อจาก og:description
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
     * ดำเนินการสร้างข่าวสารและดาวน์โหลดรูปภาพทั้งหมดจากข้อมูลที่ได้รับ
     */
    public static function processImport(
        string $text,
        array $imageUrls = [],
        ?int $categoryId = null,
        ?Carbon $publishedAt = null,
        bool $isPublished = true
    ): News {
        // 1. แยก Title และ Content
        $parsed = self::parseText($text);
        $title = $parsed['title'];
        $content = $parsed['content'];

        // 2. หา Category เริ่มต้นหากไม่ได้ระบุ
        if (!$categoryId) {
            $defaultCat = Category::where('type', 'news')->where('name', 'like', '%ประชาสัมพันธ์%')->first()
                ?? Category::where('type', 'news')->first();
            $categoryId = $defaultCat ? $defaultCat->id : 7;
        }

        // 3. กรองและดาวน์โหลดรูปภาพ
        $coverImage = null;
        $galleryImages = [];

        // ลบ URL รูปภาพที่ซ้ำกัน
        $uniqueImageUrls = array_values(array_unique(array_filter($imageUrls)));

        foreach ($uniqueImageUrls as $index => $imageUrl) {
            if ($index === 0) {
                // รูปแรกตั้งเป็นรูปปกข่าว
                $downloaded = self::downloadImage($imageUrl, 'news-covers');
                if ($downloaded) {
                    $coverImage = $downloaded;
                }
            } else {
                // รูปถัดไปตั้งเป็นคลังภาพกิจกรรม
                $downloaded = self::downloadImage($imageUrl, 'news-galleries');
                if ($downloaded) {
                    $galleryImages[] = $downloaded;
                }
            }
        }

        // 4. บันทึกข้อมูลข่าวสารลงฐานข้อมูล
        return News::create([
            'category_id' => $categoryId,
            'title' => $title,
            'content' => $content,
            'cover_image' => $coverImage,
            'gallery_images' => $galleryImages,
            'published_at' => $publishedAt ?? now(),
            'is_published' => $isPublished,
        ]);
    }

    /**
     * สร้างโค้ด Bookmarklet JavaScript สำหรับให้แอดมินลากไปวางบนเบราว์เซอร์
     */
    public static function generateBookmarkletJs(string $targetUrl): string
    {
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
            alert('ไม่พบข้อความโพสต์ กรุณาคลิกเลือกข้อความบนโพสต์ที่ต้องการ แล้วกดปุ่มนี้อีกครั้ง');
            return;
        }

        var post = document.querySelector('div[role="article"]') || document;
        var imgs = [];
        var allImgs = post.querySelectorAll('img');
        for (var j = 0; j < allImgs.length; j++) {
            var img = allImgs[j];
            var src = img.src || '';
            if ((src.indexOf('scontent') !== -1 || src.indexOf('fbcdn') !== -1) && src.indexOf('emoji') === -1 && src.indexOf('rsrc.php') === -1) {
                var w = img.naturalWidth || img.width || 0;
                var h = img.naturalHeight || img.height || 0;
                if ((w >= 120 && h >= 120) || (w === 0 && h === 0)) {
                    if (imgs.indexOf(src) === -1) {
                        imgs.push(src);
                    }
                }
            }
        }

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{$targetUrl}';
        form.target = '_blank';

        var inText = document.createElement('input');
        inText.type = 'hidden';
        inText.name = 'text';
        inText.value = text;
        form.appendChild(inText);

        var inImgs = document.createElement('input');
        inImgs.type = 'hidden';
        inImgs.name = 'images_json';
        inImgs.value = JSON.stringify(imgs);
        form.appendChild(inImgs);

        var inUrl = document.createElement('input');
        inUrl.type = 'hidden';
        inUrl.name = 'source_url';
        inUrl.value = window.location.href;
        form.appendChild(inUrl);

        document.body.appendChild(form);
        form.submit();
        setTimeout(function(){
            document.body.removeChild(form);
        }, 1000);
    }, 400);
})();
JS;

        // บีบอัดและแปลงเป็น bookmarklet link
        $compactJs = preg_replace('/\s+/', ' ', trim($rawJs));
        return $compactJs;
    }
}

