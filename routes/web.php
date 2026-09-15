<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// คลังเอกสารกฎหมาย
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

// หน้าข้อมูลองค์กรทั่วไป
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

// ข่าวสารและกิจกรรม
Route::get('/news', [HomeController::class, 'newsIndex'])->name('news.index');
Route::get('/news/{news:slug}', [HomeController::class, 'showNews'])->name('news.show');

// เครื่องมือสำหรับติดตั้งบนโฮสติ้ง cPanel (เรียกใช้งานผ่าน Browser)
Route::get('/install-storage-link', function () {
    $dirs = [
        storage_path('framework/views'),
        storage_path('framework/sessions'),
        storage_path('framework/cache/data'),
        storage_path('logs'),
        storage_path('app/private/livewire-tmp'),
        storage_path('app/public/livewire-tmp'),
        storage_path('app/public/law-documents'),
        storage_path('app/public/news-covers'),
        storage_path('app/public/news-galleries'),
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @chmod($dir, 0775);
    }

    $link = public_path('storage');
    if (file_exists($link) && is_link($link)) {
        unlink($link);
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2>🎉 เชื่อมต่อ Storage และสร้างโฟลเดอร์สำหรับอัปโหลดสำเร็จเรียบร้อย!</h2><p>โฟลเดอร์สำหรับไฟล์ PDF, รูปภาพ และ Livewire Upload พร้อมใช้งานแล้ว</p><a href="/admin/law-documents" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;">กลับไปหน้าอัปโหลดเอกสาร</a></div>';
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2> ล้างแคชระบบ (Optimize Clear) สำเร็จ!</h2><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#0f172a;color:#fff;text-decoration:none;border-radius:6px;">กลับหน้าหลัก</a></div>';
});

Route::get('/migrate-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate --force');
        \Illuminate\Support\Facades\Artisan::call('db:seed --class=DemoDataSeeder --force');
        \App\Models\User::firstOrCreate(
            ['email' => 'feemubankru48@gmail.com'],
            [
                'name' => 'Fee',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2>🎉 ติดตั้งฐานข้อมูล MySQL สำเร็จสมบูรณ์ 100%!</h2><p>ระบบสร้างตาราง นำเข้าหมวดหมู่ ข่าวสาร ประกาศ เอกสารกฎหมาย และบัญชีแอดมินลง MySQL เรียบร้อยแล้ว</p><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;">กลับสู่หน้าแรก</a></div>';
    } catch (\Throwable $e) {
        return '<div style="font-family:sans-serif;padding:30px;color:#b91c1c;text-align:center;"><h2>❌ เกิดข้อผิดพลาดในการเชื่อมต่อ MySQL</h2><p style="background:#fee2e2;padding:15px;border-radius:6px;display:inline-block;text-align:left;">' . htmlspecialchars($e->getMessage()) . '</p><p>โปรดตรวจสอบ DB_DATABASE, DB_USERNAME และ DB_PASSWORD ในไฟล์ .env ให้ถูกต้อง</p></div>';
    }
});