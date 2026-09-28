<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * กำหนดสิทธิ์ให้ผู้ใช้สามารถเข้าถึง Filament Admin Panel ได้ในโหมด Production
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * ป้องกันการลบบัญชีผู้พัฒนาหลัก (Master Developer Account)
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if (in_array(strtolower($user->email), ['feemubankru48@gmail.com'])) {
                return false;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
