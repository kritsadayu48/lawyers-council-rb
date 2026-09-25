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
        // 1. ตารางเก็บข้อมูลวิดีโอ YouTube
        if (!Schema::hasTable('youtube_videos')) {
            Schema::create('youtube_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('youtube_url');
                $table->string('youtube_id')->nullable()->index();
                $table->text('description')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->integer('order_column')->default(0)->index();
                $table->date('published_date')->nullable();
                $table->timestamps();
            });
        }

        // 2. ตารางเก็บค่าการตั้งค่าทั่วไปของเว็บไซต์ (รวมถึงลิงก์ช่อง YouTube)
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_videos');
        Schema::dropIfExists('site_settings');
    }
};
