<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // กำหนด Rate Limiting ป้องกัน Brute Force ในหน้า Login (สูงสุด 5 ครั้งต่อ 1 นาทีต่อ 1 IP)
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            $email = (string) $request->input('email', $request->input('data.email', ''));
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($email . $request->ip())->response(function () {
                return response('คุณพยายามเข้าสู่ระบบมากเกินไป โปรดลองใหม่อีกครั้งในอีก 1 นาที (Too Many Requests - Rate Limit Exceeded)', 429);
            });
        });

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $stats = [
                'today' => 0,
                'this_month' => 0,
                'total' => 0,
            ];

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('visit_logs')) {
                    $todayDate = now()->toDateString();
                    $startOfMonth = now()->startOfMonth()->toDateString();

                    $stats['today'] = \App\Models\VisitLog::where('visited_date', $todayDate)->count();
                    $stats['this_month'] = \App\Models\VisitLog::where('visited_date', '>=', $startOfMonth)->count();
                    $stats['total'] = \App\Models\VisitLog::count();
                }
            } catch (\Throwable $e) {
                // Ignore DB read errors
            }

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
                    $youtubeChannelUrl = \App\Models\SiteSetting::get('youtube_channel_url', 'https://www.youtube.com/@lawyerscouncilrb');
                } else {
                    $youtubeChannelUrl = 'https://www.youtube.com/@lawyerscouncilrb';
                }
            } catch (\Throwable $e) {
                $youtubeChannelUrl = 'https://www.youtube.com/@lawyerscouncilrb';
            }

            $view->with('visitorStats', $stats)
                 ->with('youtubeChannelUrl', $youtubeChannelUrl);
        });
    }
}
