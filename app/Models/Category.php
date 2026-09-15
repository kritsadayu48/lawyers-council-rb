<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'type'];

    public function news() { return $this->hasMany(News::class); }
    public function lawDocuments() { return $this->hasMany(LawDocument::class); }
}
