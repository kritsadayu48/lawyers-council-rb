<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\Category;
use App\Services\FacebookImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_facebook')
                ->label('นำเข้าจาก Facebook')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->modalHeading('นำเข้าข่าวสารและรูปภาพจาก Facebook')
                ->modalDescription('เพียงแค่วางลิงก์โพสต์ Facebook ระบบจะดึงเนื้อหาและรูปภาพทั้งหมดมาสร้างข่าวให้เสร็จสรรพ')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('เริ่มนำเข้าข้อมูล')
                ->form([
                    Forms\Components\Section::make('วางลิงก์โพสต์ Facebook')
                        ->description('ก๊อปปี้ลิงก์โพสต์จาก Facebook เพจสภาทนายความมาวางที่นี่')
                        ->schema([
                            Forms\Components\TextInput::make('facebook_url')
                                ->label('ลิงก์โพสต์ Facebook')
                                ->placeholder('เช่น https://www.facebook.com/RachaburiLawyer/posts/...')
                                ->url()
                                ->helperText('⚡ วางลิงก์โพสต์ Facebook ระบบจะไปดึงข้อความและรูปภาพทั้งหมด (รวมถึงรูป 70+ รูป) มาสร้างข่าวให้อัตโนมัติ'),

                            Forms\Components\Select::make('category_id')
                                ->label('หมวดหมู่ข่าว')
                                ->options(Category::where('type', 'news')->pluck('name', 'id'))
                                ->default(fn () => Category::where('type', 'news')->where('name', 'like', '%ประชาสัมพันธ์%')->value('id') ?? Category::where('type', 'news')->value('id'))
                                ->required(),

                            Forms\Components\Toggle::make('is_published')
                                ->label('เผยแพร่ทันทีหลังนำเข้า')
                                ->default(true),

                            Forms\Components\Textarea::make('raw_content')
                                ->label('หรือ วางข้อความโพสต์สำรอง (Optional)')
                                ->placeholder("ก๊อปปี้ข้อความจาก Facebook มาวางที่นี่ (กรณีต้องการระบุเนื้อหาเอง ระบบจะแยกหัวข้อและวันที่ให้อัตโนมัติ)")
                                ->rows(4),

                            Forms\Components\Textarea::make('raw_images')
                                ->label('ลิงก์รูปภาพเพิ่มเติม (Optional)')
                                ->placeholder("วาง URL รูปภาพ Facebook (บรรทัดละ 1 ลิงก์)")
                                ->rows(3),
                        ]),

                    Forms\Components\View::make('filament.components.facebook-bookmarklet-card'),
                ])
                ->action(function (array $data) {
                    $url = trim($data['facebook_url'] ?? '');
                    $rawContent = trim($data['raw_content'] ?? '');
                    $rawImages = trim($data['raw_images'] ?? '');
                    $categoryId = $data['category_id'] ?? null;
                    $isPublished = (bool) ($data['is_published'] ?? true);

                    if (empty($url) && empty($rawContent)) {
                        Notification::make()
                            ->title('กรุณาระบุลิงก์ Facebook หรือวางข้อความโพสต์')
                            ->warning()
                            ->send();
                        return;
                    }

                    $imageUrls = [];
                    if (!empty($rawImages)) {
                        $imageUrls = array_values(array_filter(array_map('trim', explode("\n", $rawImages))));
                    }

                    $textToUse = $rawContent;

                    if (!empty($url)) {
                        Notification::make()
                            ->title('กำลังดึงข้อมูลจาก Facebook...')
                            ->body('กำลังเปิดอ่านโพสต์และสแกนรูปภาพความละเอียดสูง กรุณารอสักครู่')
                            ->info()
                            ->send();

                        $scraped = FacebookImportService::scrapeFacebookUrl($url);
                        if (!empty($scraped['images'])) {
                            $imageUrls = array_merge($imageUrls, $scraped['images']);
                        }
                        if (empty($textToUse)) {
                            if (!empty($scraped['content'])) {
                                $textToUse = $scraped['content'];
                            } elseif (!empty($scraped['title'])) {
                                $textToUse = $scraped['title'];
                            }
                        }
                    }

                    if (empty($textToUse)) {
                        Notification::make()
                            ->title('ไม่สามารถดึงข้อมูลจากลิงก์นี้ได้')
                            ->body('กรุณาคัดลอก (Copy) ข้อความจาก Facebook มาวางในช่องข้อความสำรอง หรือตรวจการตั้งค่า Cloud Scraper ครับ')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    try {
                        $news = FacebookImportService::processImport(
                            text: $textToUse,
                            imageUrls: $imageUrls,
                            categoryId: $categoryId,
                            publishedAt: null,
                            isPublished: $isPublished
                        );

                        $imgCount = ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []);

                        Notification::make()
                            ->title('🎉 นำเข้าข่าวจาก Facebook สำเร็จ!')
                            ->body("ดึงข้อความและดาวน์โหลดรูปภาพ {$imgCount} ภาพเรียบร้อยแล้ว แอดมินสามารถตรวจสอบหรือแก้ไขได้ทันที")
                            ->success()
                            ->duration(8000)
                            ->send();

                        return redirect(NewsResource::getUrl('edit', ['record' => $news->id]));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('เกิดข้อผิดพลาดในการนำเข้า')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
