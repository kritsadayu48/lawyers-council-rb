<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\LawDocument;
use App\Models\News;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // หมวดหมู่กฎหมาย
        $catLaw1 = Category::firstOrCreate(['name' => 'พระราชบัญญัติและกฎกระทรวง', 'slug' => 'act-and-regulations', 'type' => 'law_document']);
        $catLaw2 = Category::firstOrCreate(['name' => 'ข้อบังคับสภาทนายความ', 'slug' => 'lawyers-council-rules', 'type' => 'law_document']);
        $catLaw3 = Category::firstOrCreate(['name' => 'แบบฟอร์มคำขอและคดีความ', 'slug' => 'application-forms', 'type' => 'law_document']);

        // หมวดหมู่ข่าวและประกาศ
        $catNews1 = Category::firstOrCreate(['name' => 'ข่าวประชาสัมพันธ์', 'slug' => 'announcements', 'type' => 'news']);
        $catNews2 = Category::firstOrCreate(['name' => 'กิจกรรมสภาทนายความ', 'slug' => 'activities', 'type' => 'news']);
        $catNews3 = Category::firstOrCreate(['name' => 'ประกาศและหนังสือเวียน', 'slug' => 'official-announcements', 'type' => 'news']);

        // ตัวอย่างเอกสารกฎหมาย
        $docs = [
            ['title' => 'พระราชบัญญัติทนายความ พ.ศ. 2528', 'document_no' => 'ฉบับที่ 1', 'year_be' => '2528', 'category_id' => $catLaw1->id],
            ['title' => 'ข้อบังคับสภาทนายความ ว่าด้วยมรรยาททนายความ พ.ศ. 2529', 'document_no' => 'หมวด 1-4', 'year_be' => '2529', 'category_id' => $catLaw2->id],
            ['title' => 'ข้อบังคับว่าด้วยการฝึกอบรมวิชาว่าความและการทดสอบ พ.ศ. 2565', 'document_no' => 'ฉบับปรับปรุง', 'year_be' => '2565', 'category_id' => $catLaw2->id],
            ['title' => 'แบบฟอร์มคำขอขึ้นทะเบียนและรับใบอนุญาตให้เป็นทนายความ', 'document_no' => 'ท.1', 'year_be' => '2567', 'category_id' => $catLaw3->id],
            ['title' => 'แบบฟอร์มขอต่ออายุใบอนุญาตให้เป็นทนายความ', 'document_no' => 'ท.2', 'year_be' => '2567', 'category_id' => $catLaw3->id],
            ['title' => 'แบบฟอร์มขอรับความช่วยเหลือทางกฎหมายสำหรับประชาชน', 'document_no' => 'สคป.01', 'year_be' => '2568', 'category_id' => $catLaw3->id],
        ];

        foreach ($docs as $doc) {
            LawDocument::firstOrCreate(['title' => $doc['title']], array_merge($doc, [
                'file_path' => 'demo/sample.pdf',
                'download_count' => rand(5, 50)
            ]));
        }

        // ข่าวจริงอย่างเป็นทางการ: กิจกรรมวันรพี
        $realNews = [
            [
                'title' => 'สภาทนายความจังหวัดราชบุรี เข้าร่วมกิจกรรม “วันรพี” ประจำปี 2569 ณ ศาลจังหวัดราชบุรี',
                'slug' => 'rapee-day-2026',
                'category_id' => $catNews2->id,
                'cover_image' => 'news-covers/rapee-day-2026.jpg',
                'gallery_images' => null,
                'content' => '<p><strong>วันศุกร์ที่ 7 สิงหาคม พ.ศ. 2569</strong></p><p>นายมนตรี  อิ่มจิตร ประธานสภาทนายความจังหวัดราชบุรี พร้อมด้วยรองประธานสภาทนายความจังหวัดราชบุรี คณะกรรมการ อนุกรรมการสภาทนายความจังหวัดราชบุรี และทนายความจังหวัดราชบุรี เข้าร่วมกิจกรรม “วันรพี” ประจำปี 2569 ซึ่งจัดขึ้นเพื่อรำลึกถึงพระกรุณาธิคุณและน้อมสำนึกในพระปรีชาสามารถของ สมเด็จพระเจ้าบรมวงศ์เธอ พระองค์เจ้ารพีพัฒนศักดิ์ กรมหลวงราชบุรีดิเรกฤทธิ์ พระบิดาแห่งกฎหมายไทย ณ บริเวณศาลจังหวัดราชบุรี อำเภอเมืองราชบุรี จังหวัดราชบุรี</p><div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-3"><a href="https://www.facebook.com/share/p/1J8kTcyFBZ/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition"><i class="fa-brands fa-facebook text-white"></i> ดูโพสต์ต้นฉบับบนเพจ Facebook สภาทนายความจังหวัดราชบุรี</a></div>',
                'is_published' => true,
                'published_at' => '2026-08-07',
            ],
        ];

        foreach ($realNews as $item) {
            News::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}