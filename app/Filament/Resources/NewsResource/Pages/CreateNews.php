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
                ->modalDescription('ระบบช่วยดึงเนื้อหาและรูปภาพจากเพจ Facebook มาสร้างข่าวบนเว็บไซต์อัตโนมัติ')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('เริ่มนำเข้าข้อมูล')
                ->form([
                    Forms\Components\View::make('filament.components.facebook-bookmarklet-card'),

                    Forms\Components\Section::make('หรือ วางข้อความโพสต์ / ลิงก์ในหน้านี้')
                        ->schema([
                            Forms\Components\Textarea::make('raw_content')
                                ->label('ข้อความโพสต์จาก Facebook (แนะนำ: ก๊อปปี้ข้อความมาวางที่นี่)')
                                ->placeholder("วางข้อความที่ก๊อปปี้มาจาก Facebook ที่นี่...\nระบบจะแยกหัวข้อข่าว วันที่ลงข่าว และเนื้อหาให้อัตโนมัติ")
                                ->helperText('💡 แนะนำที่สุด: ก๊อปปี้ข้อความทั้งหมดจาก Facebook มาวางที่นี่ ระบบจะตรวจจับหัวข้อและวันที่ให้อัตโนมัติ')
                                ->rows(6),

                            Forms\Components\TextInput::make('facebook_url')
                                ->label('หรือ วางลิงก์โพสต์ Facebook')
                                ->placeholder('เช่น https://www.facebook.com/RachaburiLawyer/posts/...')
                                ->url()
                                ->helperText('หาก Facebook บล็อกการเข้าถึงจากภายนอก ให้เน้นใช้การก๊อปปี้ข้อความมาวางด้านบนแทน'),

                            Forms\Components\Select::make('category_id')
                                ->label('หมวดหมู่ข่าว')
                                ->options(Category::where('type', 'news')->pluck('name', 'id'))
                                ->default(fn () => Category::where('type', 'news')->where('name', 'like', '%ประชาสัมพันธ์%')->value('id') ?? Category::where('type', 'news')->value('id'))
                                ->required(),

                            Forms\Components\Toggle::make('is_published')
                                ->label('เผยแพร่ทันทีหลังนำเข้า')
                                ->default(true),

                            Forms\Components\Textarea::make('raw_images')
                                ->label('ลิงก์รูปภาพเพิ่มเติม (Optional)')
                                ->placeholder("วาง URL รูปภาพ Facebook (บรรทัดละ 1 ลิงก์)\nระบบจะดาวน์โหลดเฉพาะภาพจริง (>10KB) มาเก็บในคลังภาพให้อัตโนมัติ")
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
                            ->title('กรุณาระบุข้อความ หรือวางลิงก์โพสต์')
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
                            ->body('กรุณาคัดลอก (Copy) ข้อความจาก Facebook มาวางในช่อง "ข้อความโพสต์จาก Facebook" หรือใช้ปุ่มลัด Bookmarklet (📌 ส่งข่าวเข้าเว็บสภา) บนหน้า Facebook ครับ')
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
                            ->body("แยกหัวข้อ จัดเนื้อหา และดาวน์โหลดรูปภาพ {$imgCount} ภาพเรียบร้อยแล้ว แอดมินสามารถตรวจสอบหรือแก้ไขได้ทันที")
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
