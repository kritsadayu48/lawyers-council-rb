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

    public function category() { return $this->belongsTo(Category::class); }
}
