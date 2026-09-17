<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'cover_image', 'gallery_images', 'content', 'is_published', 'published_at'];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_published' => 'boolean',
            'gallery_images' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $slug = \Illuminate\Support\Str::slug($news->title);
                $news->slug = empty($slug) ? 'post-' . date('Ymd-His') . '-' . rand(10, 99) : $slug;
            }
        });

        static::updating(function ($news) {
            if (empty($news->slug)) {
                $slug = \Illuminate\Support\Str::slug($news->title);
                $news->slug = empty($slug) ? 'post-' . date('Ymd-His') . '-' . rand(10, 99) : $slug;
            }
        });
    }

    public function category() { return $this->belongsTo(Category::class); }
}
