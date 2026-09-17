<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'type'];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $slug = \Illuminate\Support\Str::slug($category->name);
                $category->slug = empty($slug) ? ($category->type ?? 'cat') . '-' . date('YmdHis') . '-' . rand(10, 99) : $slug;
            }
        });
    }

    public function news() { return $this->hasMany(News::class); }
    public function lawDocuments() { return $this->hasMany(LawDocument::class); }
}
