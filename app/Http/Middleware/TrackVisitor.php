<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Schema;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // บันทึกเฉพาะคำขอ GET ปกติ ไม่นับ AJAX / API / โฟลเดอร์ Admin / บอตเบื้องต้น
        if ($request->isMethod('GET') && !$request->expectsJson()) {
            $path = $request->path();
            if (!str_starts_with($path, 'admin') && !str_starts_with($path, 'livewire') && !str_starts_with($path, 'storage')) {
                try {
                    $ip = $request->ip();
                    $today = now()->toDateString();
                    $sessionKey = 'visited_' . $today;

                    // นับ 1 สิทธิ์การเข้าชมต่อ 1 Session ต่อวัน (เพื่อไม่ให้ยิง Refresh แล้วนับพุ่งเกินจริง)
                    if (!session()->has($sessionKey)) {
                        session()->put($sessionKey, true);

                        if (Schema::hasTable('visit_logs')) {
                            VisitLog::create([
                                'ip_address' => $ip,
                                'url' => $request->fullUrl(),
                                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                                'visited_date' => $today,
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    // ป้องกันไม่ให้ error ขัดขวางการโหลดหน้าเว็บ
                }
            }
        }

        return $response;
    }
}
