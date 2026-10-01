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

    /**
     * ดึงรูปถ่าย โดยถ้าเรคคอร์ดนี้ยังไม่มีรูป จะค้นหารูปจากเรคคอร์ดของบุคคลเดียวกัน (ชื่อเดียวกัน) มาแสดงให้อัตโนมัติ
     */
    public function getDisplayImageAttribute(): ?string
    {
        if (!empty($this->image_path)) {
            return $this->image_path;
        }

        $existing = static::findMatchingPersonWithImage($this->name, $this->id);
        return $existing ? $existing->image_path : null;
    }

    /**
     * ตัดคำนำหน้าชื่อภาษาไทยเพื่อใช้เปรียบเทียบว่าเป็นบุคคลเดียวกันหรือไม่
     */
    public static function normalizeName(?string $name): string
    {
        if (!$name) {
            return '';
        }

        $name = trim(preg_replace('/\s+/', ' ', $name));

        $prefixes = [
            'ว่าที่ร.ต.หญิง', 'ว่าที่ ร.ต. หญิง', 'ว่าที่ร้อยตรีหญิง',
            'ว่าที่ร.ต.', 'ว่าที่ ร.ต.', 'ว่าที่ร้อยตรี',
            'นางสาว', 'น.ส.', 'นาง', 'นาย',
            'ทนายความ', 'ทนาย',
        ];

        foreach ($prefixes as $prefix) {
            if (mb_strpos($name, $prefix) === 0) {
                return trim(mb_substr($name, mb_strlen($prefix)));
            }
        }

        return $name;
    }

    /**
     * ตรวจสอบว่าชื่อทั้งสองเป็นบุคคลเดียวกันหรือไม่ (เปรียบเทียบทั้งแบบตรงตัว และแบบตัดคำนำหน้า)
     */
    public static function isSamePerson(?string $name1, ?string $name2): bool
    {
        if (empty($name1) || empty($name2)) {
            return false;
        }

        $n1 = trim(preg_replace('/\s+/', ' ', $name1));
        $n2 = trim(preg_replace('/\s+/', ' ', $name2));

        if ($n1 === $n2) {
            return true;
        }

        $norm1 = self::normalizeName($n1);
        $norm2 = self::normalizeName($n2);

        return !empty($norm1) && $norm1 === $norm2;
    }

    /**
     * ค้นหาเรคคอร์ดของบุคคลเดียวกันที่มีรูปถ่าย
     */
    public static function findMatchingPersonWithImage(?string $name, ?int $exceptId = null): ?self
    {
        if (empty($name) || empty(trim($name))) {
            return null;
        }

        $query = static::whereNotNull('image_path')
            ->where('image_path', '!=', '');

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        $candidates = $query->get();
        foreach ($candidates as $candidate) {
            if (static::isSamePerson($name, $candidate->name)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * ซิงค์รูปถ่ายไปยังเรคคอร์ดอื่นๆ ที่เป็นบุคคลเดียวกันทั้งหมด (คณะกรรมการ, ทำเนียบประธาน, ทนายความ)
     */
    public static function syncPhotoToMatchingPersons(self $source): int
    {
        if (empty(trim($source->name))) {
            return 0;
        }

        $updatedCount = 0;
        $others = static::where('id', '!=', $source->id)->get();

        foreach ($others as $other) {
            if (static::isSamePerson($source->name, $other->name)) {
                if ($other->image_path !== $source->image_path) {
                    $other->updateQuietly([
                        'image_path' => $source->image_path,
                    ]);
                    $updatedCount++;
                }
            }
        }

        return $updatedCount;
    }

    /**
     * ตรวจสอบและซิงค์รูปถ่ายของบุคคลที่มีชื่อตรงกันทั่วทั้งระบบอัตโนมัติ
     */
    public static function autoSyncAllPhotos(): int
    {
        $updatedCount = 0;
        $recordsWithImage = static::whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->get();

        foreach ($recordsWithImage as $source) {
            $targets = static::where('id', '!=', $source->id)
                ->where(function ($q) {
                    $q->whereNull('image_path')->orWhere('image_path', '');
                })
                ->get();

            foreach ($targets as $target) {
                if (static::isSamePerson($source->name, $target->name)) {
                    $target->updateQuietly([
                        'image_path' => $source->image_path,
                    ]);
                    $updatedCount++;
                }
            }
        }

        return $updatedCount;
    }

    protected static function booted(): void
    {
        // ขณะสร้างหรือบันทึก ถ้ายังไม่ได้อัปโหลดรูป ให้ดึงรูปจากเรคคอร์ดเดิมของคนนี้มาใส่ให้อัตโนมัติ
        static::saving(function (Personnel $personnel) {
            if (empty($personnel->image_path) && !empty($personnel->name)) {
                $existing = static::findMatchingPersonWithImage($personnel->name, $personnel->id);
                if ($existing && !empty($existing->image_path)) {
                    $personnel->image_path = $existing->image_path;
                }
            }
        });

        // เมื่อบันทึกเสร็จ ให้ซิงค์รูปไปยังทุกหมวดหมู่ที่มีชื่อเดียวกัน
        static::saved(function (Personnel $personnel) {
            if (!empty($personnel->name)) {
                static::syncPhotoToMatchingPersons($personnel);
            }
        });
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
