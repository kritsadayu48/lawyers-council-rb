<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\News;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $announcementCat = Category::where('slug', 'official-announcements')
            ->orWhere('name', 'like', '%ประกาศ%')
            ->first();

        $catId = $announcementCat ? $announcementCat->id : 9;

        // อัปเดตประกาศที่ 1: กำหนดการยื่นคำขอต่ออายุใบอนุญาตให้เป็นทนายความ
        News::where('slug', 'announcement-lawyer-license-renewal-2026')
            ->update([
                'cover_image' => 'news-covers/announcement-meeting-2026.jpg',
                'gallery_images' => [
                    'news-galleries/announcement-g1.jpg',
                    'news-galleries/announcement-g2.jpg',
                    'news-galleries/seminar-g1.jpg',
                ],
                'is_published' => true,
            ]);

        // อัปเดตประกาศที่ 2: แนวทางปฏิบัติของทนายความขอแรงและทนายความอาสา
        News::where('slug', 'circular-duty-lawyers-guidelines-ratchaburi')
            ->update([
                'cover_image' => 'news-covers/announcement-clinic-2026.jpg',
                'gallery_images' => [
                    'news-galleries/announcement-g1.jpg',
                    'news-galleries/legal-aid-gallery.jpg',
                    'news-galleries/seminar-g2.jpg',
                ],
                'is_published' => true,
            ]);

        // อัปเดตประกาศที่ 3: ประกาศรายชื่อทนายความผู้ผ่านการอบรม
        News::where('slug', 'notice-certified-lawyers-juvenile-family-2026')
            ->update([
                'cover_image' => 'news-covers/seminar-2026.jpg',
                'gallery_images' => [
                    'news-galleries/announcement-g2.jpg',
                    'news-galleries/seminar-g1.jpg',
                    'news-galleries/seminar-gallery-3.jpg',
                ],
                'is_published' => true,
            ]);

        // เพิ่มประกาศใหม่พร้อมรูปภาพหลายรูป
        $newAnnouncement = News::firstOrCreate(
            ['slug' => 'duty-lawyers-schedule-sep-oct-2026'],
            [
                'title' => 'ประกาศสภาทนายความจังหวัดราชบุรี เรื่อง ตารางเวรปฏิบัติหน้าที่และการให้บริการทนายความอาสา ณ ศาลจังหวัดราชบุรี ประจำเดือนกันยายน - ตุลาคม ๒๕๖๙',
                'category_id' => $catId,
                'cover_image' => 'news-covers/announcement-clinic-2026.jpg',
                'gallery_images' => [
                    'news-galleries/announcement-g1.jpg',
                    'news-galleries/announcement-g2.jpg',
                    'news-galleries/legal-aid-gallery.jpg',
                    'news-galleries/seminar-g2.jpg',
                ],
                'content' => '<p>สภาทนายความจังหวัดราชบุรี ขอประกาศตารางเวรการปฏิบัติหน้าที่ของทนายความอาสา เพื่อให้บริการปรึกษาอรรถคดีและกฎหมายแก่ประชาชนผู้ยากไร้โดยไม่มีค่าใช้จ่าย ณ ศูนย์ให้คำปรึกษาทางกฎหมาย ศาลจังหวัดราชบุรี ประจำเดือนกันยายน ถึง ตุลาคม พ.ศ. ๒๕๖๙</p>
<p><strong>รายละเอียดการให้บริการ:</strong></p>
<ul>
<li><strong>วันและเวลาทำการ:</strong> ทุกวันจันทร์ - ศุกร์ (เว้นวันหยุดราชการและวันหยุดนักขัตฤกษ์) เวลา ๐๘.๓๐ - ๑๖.๓๐ น.</li>
<li><strong>สถานที่:</strong> อาคารศาลจังหวัดราชบุรี ชั้น ๑ ห้องศูนย์ให้คำปรึกษาทางกฎหมายสภาทนายความ</li>
<li><strong>ขอบเขตบริการ:</strong> ให้คำปรึกษากฎหมายแพ่ง กฎหมายอาญา คดีครอบครัวและมรดก รวมถึงการแนะนำการขอรับความช่วยเหลือจากกองทุนยุติธรรม</li>
</ul>
<p>ติดต่อสอบถามเพิ่มเติมได้ที่ โทร. ๐๙๗-๑๙๕-๒๐๒๙ หรือสำนักงานสภาทนายความจังหวัดราชบุรี</p>',
                'is_published' => true,
                'published_at' => '2026-09-17',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
