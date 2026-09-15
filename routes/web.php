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
    $link = public_path('storage');
    if (file_exists($link) && is_link($link)) {
        unlink($link);
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2> เชื่อมต่อ Storage Symlink สำเร็จเรียบร้อย!</h2><p>ไฟล์ PDF และรูปภาพพร้อมเปิดใช้งานแล้ว</p><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;">กลับหน้าหลัก</a></div>';
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2> ล้างแคชระบบ (Optimize Clear) สำเร็จ!</h2><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#0f172a;color:#fff;text-decoration:none;border-radius:6px;">กลับหน้าหลัก</a></div>';
});