<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\VisitLog;
use App\Models\News;
use App\Models\LawDocument;
use Illuminate\Support\Facades\Schema;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayVisits = 0;
        $monthVisits = 0;
        $totalVisits = 0;

        if (Schema::hasTable('visit_logs')) {
            $todayVisits = VisitLog::where('visited_date', now()->toDateString())->count();
            $monthVisits = VisitLog::where('visited_date', '>=', now()->startOfMonth()->toDateString())->count();
            $totalVisits = VisitLog::count();
        }

        $newsCount = News::where('is_published', true)->count();
        $docCount = LawDocument::count();

        return [
            Stat::make('ผู้เข้าชมวันนี้', number_format($todayVisits) . ' ครั้ง')
                ->description('สถิติการเข้าชมวันนี้')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('ผู้เข้าชมเดือนนี้', number_format($monthVisits) . ' ครั้ง')
                ->description('รวมตลอดเดือนปัจจุบัน')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('ผู้เข้าชมสะสมทั้งหมด', number_format($totalVisits) . ' ครั้ง')
                ->description('สถิติผู้เข้าชมเว็บไซต์รวม')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('warning'),

            Stat::make('ข่าวสารและกิจกรรม', number_format($newsCount) . ' รายการ')
                ->description('เผยแพร่อยู่บนเว็บไซต์')
                ->descriptionIcon('heroicon-m-newspaper'),

            Stat::make('คลังเอกสารกฎหมาย', number_format($docCount) . ' ฉบับ')
                ->description('แบบฟอร์มและข้อบังคับ')
                ->descriptionIcon('heroicon-m-document-text'),
        ];
    }
}
