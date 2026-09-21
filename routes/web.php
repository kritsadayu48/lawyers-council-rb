<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// คลังเอกสารกฎหมาย
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

// หน้าข้อมูลองค์กรทั่วไป
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

// ข่าวสารและกิจกรรม
Route::get('/news', [HomeController::class, 'newsIndex'])->name('news.index');
Route::get('/news/{news:slug}', [HomeController::class, 'showNews'])->name('news.show');

// Dynamic Sitemap & Robots.txt สำหรับ Google Search Console และ SEO
Route::get('/sitemap.xml', function () {
    $xml = \App\Services\SitemapService::generateXml();

    return response($xml, 200)
        ->header('Content-Type', 'application/xml; charset=utf-8')
        ->header('Cache-Control', 'public, max-age=3600');
});

Route::get('/robots.txt', function () {
    $baseUrl = config('app.url', 'https://ratchaburilawyerscouncil.or.th');
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /admin\n";
    $content .= "Disallow: /repair-symlink\n";
    $content .= "Disallow: /clear-cache\n";
    $content .= "Disallow: /debug-log\n";
    $content .= "Disallow: /check-upload\n";
    $content .= "Disallow: /migrate-db\n\n";
    $content .= "Sitemap: " . $baseUrl . "/sitemap.xml\n";

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// เครื่องมือสำหรับติดตั้ง/อัปเดตตารางฐานข้อมูลและหมวดหมู่ (รันเมื่อมีการอัปเดตระบบ)
Route::get('/init-stats-table', function () {
    $secret = env('MAINTENANCE_SECRET', 'LawyersRbAdmin2026!');
    if (request('secret') !== $secret && !auth()->check()) {
        abort(403, 'Forbidden: Invalid secret key.');
    }

    $log = [];

    // 1. Ensure 'personnels' table exists directly and robustly
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('personnels')) {
            \Illuminate\Support\Facades\Schema::create('personnels', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('type')->default('ratchaburi_lawyer')->index();
                $table->string('name');
                $table->string('position')->nullable();
                $table->string('term')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('license_no')->nullable();
                $table->string('office_name')->nullable();
                $table->string('image_path')->nullable();
                $table->text('bio')->nullable();
                $table->integer('order_column')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
            $log[] = "Table 'personnels' created directly via Schema builder.";
        } else {
            $log[] = "Table 'personnels' already exists.";
        }

        // Run PersonnelSeeder if empty
        if (\App\Models\Personnel::count() === 0) {
            $seeder = new \Database\Seeders\PersonnelSeeder();
            $seeder->run();
            $log[] = "PersonnelSeeder executed successfully: 15 committee members + president + lawyer directory added.";
        } else {
            $count = \App\Models\Personnel::count();
            $log[] = "Personnel table already has {$count} records.";
        }
    } catch (\Throwable $e) {
        $log[] = "Personnel setup error: " . $e->getMessage();
    }

    // 1.1 Run any other pending migrations safely
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $log[] = "Artisan migrate output:\n" . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $log[] = "Artisan migrate notice (safe to ignore if tables pre-existed): " . $e->getMessage();
    }

    // 2. Direct guarantee for categories
    try {
        $categories = [
            ['name' => 'แบบฟอร์มศาลและคำร้องทั่วไป', 'slug' => 'court-forms', 'type' => 'law_document'],
            ['name' => 'ระเบียบและแนวปฏิบัติด้านคดีความ', 'slug' => 'legal-procedures', 'type' => 'law_document'],
            ['name' => 'มรรยาททนายความและจริยธรรมวิชาชีพ', 'slug' => 'lawyer-ethics', 'type' => 'law_document'],
            ['name' => 'คำวินิจฉัยและมติสภาทนายความ', 'slug' => 'board-resolutions', 'type' => 'law_document'],
            ['name' => 'เอกสารเผยแพร่ความรู้ทางกฎหมาย', 'slug' => 'legal-knowledge', 'type' => 'law_document'],
            ['name' => 'ข่าวอบรมและสัมมนาวิชาการ', 'slug' => 'academic-trainings', 'type' => 'news'],
            ['name' => 'กิจกรรมเพื่อสังคมและทนายความอาสา', 'slug' => 'probono-activities', 'type' => 'news'],
            ['name' => 'การประชุมคณะกรรมการและสมาชิก', 'slug' => 'board-meetings', 'type' => 'news'],
            ['name' => 'ประกาศรับสมัครและผลการสอบ', 'slug' => 'examinations-recruitment', 'type' => 'news'],
        ];

        $added = 0;
        foreach ($categories as $cat) {
            $exists = \Illuminate\Support\Facades\DB::table('categories')->where('slug', $cat['slug'])->exists();
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('categories')->insert([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'type' => $cat['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $added++;
            }
        }
        $log[] = "Categories checked: {$added} new categories added.";
    } catch (\Throwable $e) {
        $log[] = "Categories error: " . $e->getMessage();
    }

    // 3. Generate physical sitemap.xml for Google Search Console
    try {
        if (\App\Services\SitemapService::writeToFile()) {
            $log[] = "sitemap.xml successfully generated and saved to public/sitemap.xml";
        }
    } catch (\Throwable $e) {
        $log[] = "Sitemap generation error: " . $e->getMessage();
    }

    return '<div style="font-family: sans-serif; padding: 20px; line-height: 1.6;">'
        . '<h2 style="color: green;">✅ อัปเดตฐานข้อมูลและหมวดหมู่เรียบร้อยแล้ว</h2>'
        . '<pre style="background: #f4f4f4; padding: 15px; border-radius: 6px;">' . htmlspecialchars(implode("\n\n", $log)) . '</pre>'
        . '<p><a href="/admin" style="display: inline-block; padding: 8px 16px; background: #d97706; color: white; text-decoration: none; border-radius: 6px;">ไปยังระบบแอดมิน</a> &nbsp; '
        . '<a href="/">กลับหน้าหลัก</a></p>'
        . '</div>';
});

// กลุ่มเครื่องมือผู้ดูแลระบบและซ่อมบำรุงขั้นสูง (ต้องมีสิทธิ์ล็อกอิน Admin หรือใส่ token ลับ ?secret=...)
Route::group(['middleware' => function ($request, $next) {
    $secret = env('MAINTENANCE_SECRET', 'LawyersRbAdmin2026!');
    if ($request->query('secret') === $secret || auth()->check()) {
        return $next($request);
    }
    abort(403, 'ขออภัย เฉพาะผู้ดูแลระบบที่ได้รับอนุญาตเท่านั้น');
}], function () {
    Route::get('/generate-sitemap', function () {
        $success = \App\Services\SitemapService::writeToFile();
        return response()->json([
            'success' => $success,
            'message' => $success ? 'sitemap.xml regenerated successfully!' : 'Failed to write sitemap.xml',
            'sitemap_url' => url('/sitemap.xml'),
        ]);
    });

    // เครื่องมือฉุกเฉินสำหรับสร้างหรือรีเซ็ตรหัสผ่านแอดมิน (กรณีลืมรหัสผ่าน)
    Route::get('/reset-admin-password', function () {
        $email = request('email', 'admin@ratchaburilawyerscouncil.or.th');
        $newPassword = request('password', 'AdminRb2026!');

        $user = \App\Models\User::firstOrNew(['email' => $email]);
        $user->name = request('name', $user->name ?: 'ผู้ดูแลระบบ สภาทนายความ');
        $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => "ตั้งรหัสผ่านใหม่สำหรับ {$email} สำเร็จเรียบร้อย",
            'email' => $email,
            'password' => $newPassword,
            'login_url' => url('/admin/login'),
        ]);
    });

    Route::get('/repair-symlink', function () {
        $publicStorage = public_path('storage');
        $target = storage_path('app/public');
        $output = [];

        foreach ([
            $target,
            $target . '/news-covers',
            $target . '/news-galleries',
            $target . '/law-documents',
            $target . '/demo',
        ] as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            @chmod($dir, 0777);
        }

        if (file_exists($publicStorage)) {
            if (is_link($publicStorage)) {
                @unlink($publicStorage);
                $output[] = "ลบ Symlink เดิมเรียบร้อย";
            } elseif (is_dir($publicStorage)) {
                $output[] = "พบ public/storage เป็นโฟลเดอร์จริง กำลังซิงค์ไฟล์ไปยัง storage/app/public...";
                try {
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($publicStorage, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::SELF_FIRST
                    );
                    foreach ($iterator as $item) {
                        $subPath = substr($item->getPathname(), strlen($publicStorage));
                        $destPath = $target . $subPath;
                        if ($item->isDir()) {
                            if (!is_dir($destPath)) {
                                @mkdir($destPath, 0777, true);
                            }
                        } else {
                            if (!file_exists($destPath)) {
                                @copy($item->getPathname(), $destPath);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $output[] = "Sync error: " . $e->getMessage();
                }

                exec("rm -rf " . escapeshellarg($publicStorage));
                $output[] = "ลบโฟลเดอร์จริง public/storage เพื่อเตรียมสร้าง Symlink เรียบร้อย";
            }
        }

        if (@symlink($target, $publicStorage)) {
            $output[] = "✅ สร้าง Symlink [public/storage -> storage/app/public] สำเร็จสมบูรณ์ 100%!";
        } else {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $output[] = "Artisan storage:link output: " . \Illuminate\Support\Facades\Artisan::output();
        }

        return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2>🎉 ซ่อมแซมระบบแสดงรูปภาพและเอกสาร PDF สำเร็จแล้ว!</h2><pre style="text-align:left;background:#f1f5f9;padding:15px;display:inline-block;border-radius:8px;font-size:13px;">' . htmlspecialchars(implode("\n", $output)) . '</pre><br><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;">กลับไปดูหน้าแรก</a></div>';
    });

    Route::get('/install-storage-link', function () {
        return redirect('/repair-symlink?secret=' . request('secret'));
    });

    Route::get('/clear-cache', function () {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return '<div style="font-family:sans-serif;padding:30px;text-align:center;"><h2>ล้างแคชระบบ (Optimize Clear) สำเร็จ!</h2><a href="/" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#0f172a;color:#fff;text-decoration:none;border-radius:6px;">กลับหน้าหลัก</a></div>';
    });

    Route::get('/debug-log', function () {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return 'No log file found at: ' . $logFile;
        }
        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);
        $lastLines = array_slice($lines, -150);
        return '<pre style="background:#0f172a;color:#f8fafc;padding:20px;font-size:12px;overflow:auto;white-space:pre-wrap;">' . htmlspecialchars(implode("\n", $lastLines)) . '</pre>';
    });
});