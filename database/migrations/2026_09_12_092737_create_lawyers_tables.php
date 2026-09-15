<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ตารางหมวดหมู่ (ใช้ร่วมกันทั้ง ข่าวสาร และ เอกสารกฎหมาย)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // เช่น ข่าวประชาสัมพันธ์, ข้อบังคับสภาทนายความ
            $table->string('slug')->unique();
            $table->enum('type', ['news', 'law_document']); // ระบุประเภทหมวดหมู่
            $table->timestamps();
        });

        // 2. ตารางข่าวสาร / ประชาสัมพันธ์
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // หัวข้อข่าว
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable(); // รูปภาพปกข่าว
            $table->longText('content'); // รายละเอียดเนื้อหาข่าว
            $table->boolean('is_published')->default(true); // สถานะเผยแพร่
            $table->date('published_at')->nullable(); // วันที่ประกาศ
            $table->timestamps();
        });

        // 3. ตารางคลังไฟล์เอกสารกฎหมาย / แบบฟอร์มคำขอ
        Schema::create('law_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // ชื่อเอกสาร เช่น พระราชบัญญัติทนายความ พ.ศ. 2528
            $table->string('document_no')->nullable(); // เลขที่หนังสือ หรือ เล่มที่
            $table->string('year_be', 4)->nullable(); // ปี พ.ศ. เอาไว้ทำ Filter ค้นหา
            $table->string('file_path'); // ที่อยู่จัดเก็บไฟล์ PDF
            $table->unsignedBigInteger('download_count')->default(0); // นับจำนวนครั้งที่ดาวน์โหลด
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_documents');
        Schema::dropIfExists('news');
        Schema::dropIfExists('categories');
    }
};