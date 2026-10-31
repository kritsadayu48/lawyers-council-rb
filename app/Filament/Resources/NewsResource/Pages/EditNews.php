<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Services\FacebookImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_facebook_images')
                ->label('นำเข้ารูปภาพเพิ่มเติมจาก Facebook')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->modalHeading('ดึงรูปภาพเพิ่มเติมเข้าสู่คลังภาพของข่าวนี้')
                ->modalDescription('สามารถวางลิงก์โพสต์ Facebook หรือวาง URL รูปภาพโดยตรงเพื่อดาวน์โหลดเข้าคลังภาพของข่าวนี้ทันที')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('ดาวน์โหลดรูปภาพเพิ่ม')
                ->form([
                    Forms\Components\TextInput::make('facebook_url')
                        ->label('ลิงก์โพสต์ Facebook หรือ อัลบั้ม')
                        ->placeholder('https://www.facebook.com/...'),
                    Forms\Components\Textarea::make('image_urls')
                        ->label('หรือวาง URL รูปภาพ Facebook โดยตรง (บรรทัดละ 1 ลิงก์)')
                        ->rows(5)
                        ->placeholder("https://scontent...\nhttps://scontent..."),
                ])
                ->action(function (array $data) {
                    $fbUrl = trim($data['facebook_url'] ?? '');
                    $urlsText = trim($data['image_urls'] ?? '');

                    $urls = [];
                    if (!empty($urlsText)) {
                        $urls = array_values(array_filter(array_map('trim', explode("\n", $urlsText))));
                    }

                    if (!empty($fbUrl)) {
                        $scraped = FacebookImportService::scrapeFacebookUrl($fbUrl);
                        if (!empty($scraped['images'])) {
                            $urls = array_merge($urls, $scraped['images']);
                        }
                    }

                    if (empty($urls)) {
                        Notification::make()
                            ->title('ไม่พบรูปภาพที่จะนำเข้า')
                            ->warning()
                            ->send();
                        return;
                    }

                    $added = FacebookImportService::appendGalleryImages($this->record, $urls);

                    Notification::make()
                        ->title("นำเข้ารูปภาพเพิ่มเติมสำเร็จ {$added} รูป!")
                        ->success()
                        ->send();

                    $this->fillForm();
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
