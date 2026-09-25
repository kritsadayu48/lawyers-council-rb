<?php

namespace App\Filament\Resources\YoutubeVideoResource\Pages;

use App\Filament\Resources\YoutubeVideoResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListYoutubeVideos extends ListRecords
{
    protected static string $resource = YoutubeVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('เพิ่มคลิปวิดีโอใหม่')
                ->icon('heroicon-o-plus'),

            Actions\Action::make('setChannelUrl')
                ->label('ตั้งค่าลิงก์ช่อง YouTube')
                ->icon('heroicon-o-link')
                ->color('danger')
                ->modalHeading('ตั้งค่าลิงก์ช่อง YouTube ทางการของสภาทนายความ')
                ->modalDescription('กำหนด URL ช่อง YouTube ที่จะนำไปแสดงบนแถบเมนูและหน้าเว็บไซต์ทั้งหมด')
                ->form([
                    Forms\Components\TextInput::make('channel_url')
                        ->label('URL ช่อง YouTube ทางการ')
                        ->placeholder('เช่น https://www.youtube.com/@lawyerscouncilrb')
                        ->default(fn () => SiteSetting::get('youtube_channel_url', 'https://www.youtube.com/@lawyerscouncilrb'))
                        ->url()
                        ->required()
                        ->helperText('สามารถคัดลอกลิงก์หน้าหลักของช่อง YouTube มาวางที่นี่ได้ทันที'),
                ])
                ->action(function (array $data): void {
                    SiteSetting::set('youtube_channel_url', $data['channel_url'], 'ลิงก์ช่อง YouTube ทางการ');

                    Notification::make()
                        ->title('บันทึกลิงก์ช่อง YouTube สำเร็จ')
                        ->body('ลิงก์ช่อง YouTube บนหน้าเว็บไซต์ได้รับการอัปเดตเรียบร้อยแล้ว')
                        ->success()
                        ->send();
                }),
        ];
    }
}
