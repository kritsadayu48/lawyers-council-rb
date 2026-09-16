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

// Dynamic Sitemap & Robots.txt สำหรับ Google Search Console และ SEO
Route::get('/sitemap.xml', function () {
    $baseUrl = config('app.url', 'https://ratchaburilawyerscouncil.or.th');
    $newsList = \App\Models\News::where('is_published', true)->latest()->get();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // หน้าหลัก
    $staticPages = [
        ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => $baseUrl . '/news', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/documents', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => $baseUrl . '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ];

    foreach ($staticPages as $page) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($page['loc']) . '</loc>';
        $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
        $xml .= '<priority>' . $page['priority'] . '</priority>';
        $xml .= '</url>';
    }

    // หน้าข่าวสารแต่ละโพสต์
    foreach ($newsList as $news) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($baseUrl . '/news/' . $news->slug) . '</loc>';
        $xml .= '<lastmod>' . ($news->updated_at ? $news->updated_at->format('Y-m-d') : date('Y-m-d')) . '</lastmod>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.7</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
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

// กลุ่มเครื่องมือผู้ดูแลระบบและซ่อมบำรุง (ต้องมีสิทธิ์ล็อกอิน Admin หรือใส่ token ลับ ?secret=...)
Route::group(['middleware' => function ($request, $next) {
    $secret = env('MAINTENANCE_SECRET', 'LawyersRbAdmin2026!');
    if ($request->query('secret') === $secret || auth()->check()) {
        return $next($request);
    }
    abort(403, 'ขออภัย เฉพาะผู้ดูแลระบบที่ได้รับอนุญาตเท่านั้น');
}], function () {
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

    Route::get('/check-upload', function () {
        $results = [];
        $results['upload_max_filesize'] = ini_get('upload_max_filesize');
        $results['post_max_size'] = ini_get('post_max_size');
        $results['memory_limit'] = ini_get('memory_limit');
        $results['upload_tmp_dir'] = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
        $results['upload_tmp_dir_writable'] = is_writable($results['upload_tmp_dir']);

        $disk = \Livewire\Features\SupportFileUploads\FileUploadConfiguration::disk();
        $results['livewire_disk'] = $disk;
        $targetDir = \Illuminate\Support\Facades\Storage::disk($disk)->path('livewire-tmp');
        $results['livewire_target_dir'] = $targetDir;

        $storagePaths = [
            storage_path('app'),
            storage_path('app/private'),
            storage_path('app/private/livewire-tmp'),
            storage_path('app/public'),
            storage_path('app/public/livewire-tmp'),
            storage_path('app/public/law-documents'),
            storage_path('app/public/news-covers'),
            storage_path('app/public/news-galleries'),
        ];

        foreach ($storagePaths as $p) {
            if (!is_dir($p)) {
                @mkdir($p, 0777, true);
            }
            @chmod($p, 0777);
            $results['paths'][$p] = [
                'exists' => is_dir($p),
                'writable' => is_writable($p),
                'perms' => is_dir($p) ? substr(sprintf('%o', fileperms($p)), -4) : 'none',
            ];
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('cache')) {
                \Illuminate\Support\Facades\DB::statement("
                    CREATE TABLE IF NOT EXISTS `cache` (
                      `key` varchar(255) NOT NULL,
                      `value` mediumtext NOT NULL,
                      `expiration` bigint NOT NULL,
                      PRIMARY KEY (`key`),
                      KEY `cache_expiration_index` (`expiration`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
                $results['cache_table_created'] = 'SUCCESS';
            } else {
                $results['cache_table_created'] = 'ALREADY_EXISTS';
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('cache_locks')) {
                \Illuminate\Support\Facades\DB::statement("
                    CREATE TABLE IF NOT EXISTS `cache_locks` (
                      `key` varchar(255) NOT NULL,
                      `owner` varchar(255) NOT NULL,
                      `expiration` bigint NOT NULL,
                      PRIMARY KEY (`key`),
                      KEY `cache_locks_expiration_index` (`expiration`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
                $results['cache_locks_table_created'] = 'SUCCESS';
            } else {
                $results['cache_locks_table_created'] = 'ALREADY_EXISTS';
            }
        } catch (\Throwable $e) {
            $results['cache_table_error'] = $e->getMessage();
        }

        try {
            $testFile = $targetDir . '/test_' . time() . '.txt';
            file_put_contents($testFile, 'test');
            $results['write_test'] = file_exists($testFile) ? 'SUCCESS' : 'FAILED';
            @unlink($testFile);
        } catch (\Throwable $e) {
            $results['write_test_error'] = $e->getMessage();
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
});