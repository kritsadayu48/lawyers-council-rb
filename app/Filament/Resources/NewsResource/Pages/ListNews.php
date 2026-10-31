<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\Category;
use App\Services\FacebookImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('สร้าง ข่าว/ประกาศ ใหม่'),

            Actions\Action::make('import_facebook')
                ->label('นำเข้าจาก Facebook')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->modalHeading('นำเข้าข่าวสารและรูปภาพจาก Facebook')
                ->modalDescription('ระบบช่วยดึงเนื้อหาและรูปภาพจากเพจ Facebook มาสร้างข่าวบนเว็บไซต์อัตโนมัติ เพื่อประหยัดเวลาของแอดมิน')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('เริ่มนำเข้าข้อมูล')
                ->form([
                    Forms\Components\View::make('filament.components.facebook-bookmarklet-card'),

                    Forms\Components\Section::make('หรือ วางลิงก์ / ข้อความโพสต์ในหน้านี้')
                        ->description('กรอกลิงก์หรือข้อความหากต้องการนำเข้าผ่านหน้าจัดการนี้โดยตรง')
                        ->schema([
                            Forms\Components\TextInput::make('facebook_url')
                                ->label('ลิงก์โพสต์ Facebook')
                                ->placeholder('เช่น https://www.facebook.com/RachaburiLawyer/posts/...')
                                ->url()
                                ->helperText('นำลิงก์โพสต์จากหน้าเพจสภาทนายความมาวางที่นี่'),

                            Forms\Components\Select::make('category_id')
                                ->label('หมวดหมู่ข่าว')
                                ->options(Category::where('type', 'news')->pluck('name', 'id'))
                                ->default(fn () => Category::where('type', 'news')->where('name', 'like', '%ประชาสัมพันธ์%')->value('id') ?? Category::where('type', 'news')->value('id'))
                                ->required(),

                            Forms\Components\Toggle::make('is_published')
                                ->label('เผยแพร่ทันทีหลังนำเข้า')
                                ->default(true),

                            Forms\Components\Textarea::make('raw_content')
                                ->label('ข้อความโพสต์สำรอง (กรณีเซิร์ฟเวอร์ติดบล็อก Facebook)')
                                ->placeholder('หากลิงก์โพสต์ดึงไม่ได้ ให้คัดลอกข้อความจาก Facebook มาวางที่นี่ ระบบจะตัดบรรทัดแรกเป็นหัวข้อให้อัตโนมัติ')
                                ->rows(4),

                            Forms\Components\Textarea::make('raw_images')
                                ->label('ลิงก์รูปภาพเพิ่มเติม (Optional)')
                                ->placeholder('วาง URL รูปภาพ Facebook (บรรทัดละ 1 ลิงก์) หากต้องการให้ระบบดาวน์โหลดมาเพิ่ม')
                                ->rows(3),
                        ]),
                ])
                ->action(function (array $data) {
                    $url = trim($data['facebook_url'] ?? '');
                    $rawContent = trim($data['raw_content'] ?? '');
                    $rawImages = trim($data['raw_images'] ?? '');
                    $categoryId = $data['category_id'] ?? null;
                    $isPublished = (bool) ($data['is_published'] ?? true);

                    if (empty($url) && empty($rawContent)) {
                        Notification::make()
                            ->title('กรุณาระบุลิงก์ หรือวางข้อความโพสต์')
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
                            ->title('Facebook ป้องกันการเข้าถึงโพสต์นี้จากเซิร์ฟเวอร์')
                            ->body('แนะนำให้ใช้ "ปุ่มลัดดูดข่าว (Bookmarklet)" บนหน้า Facebook หรือคัดลอกข้อความโพสต์มาวางในช่อง "ข้อความโพสต์สำรอง"')
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
                            publishedAt: now(),
                            isPublished: $isPublished
                        );

                        $imgCount = ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []);

                        Notification::make()
                            ->title('🎉 นำเข้าข่าวจาก Facebook สำเร็จ!')
                            ->body("ดึงเนื้อหาและดาวน์โหลดรูปภาพ {$imgCount} ภาพเรียบร้อยแล้ว แอดมินสามารถตรวจสอบหรือแก้ไขได้ทันที")
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
}
