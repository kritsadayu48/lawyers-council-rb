<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            // เอกสารกฎหมาย
            ['name' => 'แบบฟอร์มศาลและคำร้องทั่วไป', 'slug' => 'court-forms', 'type' => 'law_document'],
            ['name' => 'ระเบียบและแนวปฏิบัติด้านคดีความ', 'slug' => 'legal-procedures', 'type' => 'law_document'],
            ['name' => 'มรรยาททนายความและจริยธรรมวิชาชีพ', 'slug' => 'lawyer-ethics', 'type' => 'law_document'],
            ['name' => 'คำวินิจฉัยและมติสภาทนายความ', 'slug' => 'board-resolutions', 'type' => 'law_document'],
            ['name' => 'เอกสารเผยแพร่ความรู้ทางกฎหมาย', 'slug' => 'legal-knowledge', 'type' => 'law_document'],
            
            // ข่าวสารและกิจกรรม
            ['name' => 'ข่าวอบรมและสัมมนาวิชาการ', 'slug' => 'academic-trainings', 'type' => 'news'],
            ['name' => 'กิจกรรมเพื่อสังคมและทนายความอาสา', 'slug' => 'probono-activities', 'type' => 'news'],
            ['name' => 'การประชุมคณะกรรมการและสมาชิก', 'slug' => 'board-meetings', 'type' => 'news'],
            ['name' => 'ประกาศรับสมัครและผลการสอบ', 'slug' => 'examinations-recruitment', 'type' => 'news'],
        ];

        foreach ($categories as $cat) {
            $exists = DB::table('categories')->where('slug', $cat['slug'])->exists();
            if (!$exists) {
                DB::table('categories')->insert([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'type' => $cat['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugs = [
            'court-forms',
            'legal-procedures',
            'lawyer-ethics',
            'board-resolutions',
            'legal-knowledge',
            'academic-trainings',
            'probono-activities',
            'board-meetings',
            'examinations-recruitment',
        ];

        DB::table('categories')->whereIn('slug', $slugs)->delete();
    }
};
