<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $fillable = [
        'ip_address',
        'url',
        'user_agent',
        'visited_date',
    ];

    public $timestamps = true;
}
