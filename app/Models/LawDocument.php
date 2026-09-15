<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawDocument extends Model
{
    protected $fillable = ['category_id', 'title', 'document_no', 'year_be', 'file_path', 'download_count'];

    public function category() { return $this->belongsTo(Category::class); }
}
