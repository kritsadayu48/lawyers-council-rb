<?php

namespace App\Services;

use App\Models\News;
use App\Models\LawDocument;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class SitemapService
{
    /**
     * สร้างเนื้อหา XML ของ Sitemap ตามมาตรฐาน sitemaps.org และ Google Search Console
     */
    public static function generateXml(): string
    {
        $baseUrl = 'https://ratchaburilawyerscouncil.or.th';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        $today = date('Y-m-d');

        // 1. หน้าหลักและหน้าสำคัญระดับโครงสร้าง
        $staticPages = [
            ['url' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/about', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => $baseUrl . '/news', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/documents', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => $baseUrl . '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $xml .= self::renderUrlNode($page['url'], $today, $page['changefreq'], $page['priority']);
        }

        // 2. ข่าวสารและภาพกิจกรรมทุกบทความที่เผยแพร่
        try {
            $newsList = News::where('is_published', true)->latest()->get();
            foreach ($newsList as $item) {
                $url = $baseUrl . '/news/' . $item->slug;
                $modDate = $item->updated_at ? $item->updated_at->format('Y-m-d') : $today;
                $xml .= self::renderUrlNode($url, $modDate, 'weekly', '0.8');
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // 3. หมวดหมู่ข่าวสาร
        try {
            $newsCategories = Category::where('type', 'news')->get();
            foreach ($newsCategories as $cat) {
                $url = $baseUrl . '/news?category=' . $cat->slug;
                $xml .= self::renderUrlNode($url, $today, 'weekly', '0.7');
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // 4. หมวดหมู่คลังกฎหมายและแบบฟอร์ม
        try {
            $docCategories = Category::where('type', 'law_document')->get();
            foreach ($docCategories as $cat) {
                $url = $baseUrl . '/documents?category=' . $cat->slug;
                $xml .= self::renderUrlNode($url, $today, 'weekly', '0.7');
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $xml .= '</urlset>' . "\n";

        return $xml;
    }

    /**
     * เขียนไฟล์ sitemap.xml ลงในโฟลเดอร์ public โดยตรง
     */
    public static function writeToFile(): bool
    {
        $xml = self::generateXml();
        $filePath = public_path('sitemap.xml');
        return File::put($filePath, $xml) !== false;
    }

    /**
     * ช่วยจัดรูปแบบ XML Node แต่ละรายการ
     */
    private static function renderUrlNode(string $url, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n" .
               "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n" .
               "    <lastmod>" . $lastmod . "</lastmod>\n" .
               "    <changefreq>" . $changefreq . "</changefreq>\n" .
               "    <priority>" . $priority . "</priority>\n" .
               "  </url>\n";
    }
}
