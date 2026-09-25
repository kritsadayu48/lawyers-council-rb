<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'label',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting && !is_null($setting->value) && $setting->value !== '' ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value, ?string $label = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $label]
        );
    }
}
