<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    const TYPE_COMMITTEE = 'current_committee';
    const TYPE_PRESIDENT = 'past_president';
    const TYPE_LAWYER = 'ratchaburi_lawyer';

    protected $fillable = [
        'type',
        'name',
        'position',
        'term',
        'phone',
        'email',
        'license_no',
        'office_name',
        'image_path',
        'bio',
        'order_column',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_column' => 'integer',
        ];
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_COMMITTEE => 'คณะกรรมการสภาทนายความ (ชุดปัจจุบัน)',
            self::TYPE_PRESIDENT => 'ทำเนียบประธานสภาทนายความ (อดีต-ปัจจุบัน)',
            self::TYPE_LAWYER => 'ทนายความจังหวัดราชบุรี',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCommittee($query)
    {
        return $query->where('type', self::TYPE_COMMITTEE);
    }

    public function scopePresident($query)
    {
        return $query->where('type', self::TYPE_PRESIDENT);
    }

    public function scopeLawyer($query)
    {
        return $query->where('type', self::TYPE_LAWYER);
    }
}
