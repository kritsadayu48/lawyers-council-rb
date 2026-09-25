<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    use HasFactory;

    protected $table = 'youtube_videos';

    protected $fillable = [
        'title',
        'youtube_url',
        'youtube_id',
        'description',
        'is_featured',
        'is_active',
        'order_column',
        'published_date',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order_column' => 'integer',
        'published_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (YoutubeVideo $video) {
            if ($video->youtube_url) {
                $video->youtube_id = self::extractYoutubeId($video->youtube_url);
            }
        });
    }

    /**
     * ดึงรหัส YouTube ID (11 ตัวอักษร) จากลิงก์ทุกรูปแบบ
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $url = trim($url);

        // กรณีผู้ใช้กรอกเฉพาะ Video ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $patterns = [
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * ดึง URL ภาพหน้าปกของคลิป YouTube อัตโนมัติ
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }

        return asset('images/logo.png');
    }

    /**
     * ดึง URL สำหรับ Embed เล่นบนหน้าเว็บแบบปลอดภัย
     */
    public function getEmbedUrlAttribute(): string
    {
        return $this->youtube_id 
            ? "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?rel=0" 
            : '';
    }

    /**
     * ดึงลิงก์ไปเปิดดูบน YouTube โดยตรง
     */
    public function getWatchUrlAttribute(): string
    {
        return $this->youtube_id 
            ? "https://www.youtube.com/watch?v={$this->youtube_id}" 
            : $this->youtube_url;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_column')->latest('published_date');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_active', true)->where('is_featured', true);
    }
}
