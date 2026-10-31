<?php

namespace App\Http\Controllers;

use App\Services\FacebookImportService;
use App\Filament\Resources\NewsResource;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class FacebookImportController extends Controller
{
    /**
     * รับข้อมูลนำเข้าจาก Bookmarklet หรือแบบฟอร์มภายนอก
     */
    public function handle(Request $request)
    {
        // 1. ตรวจสอบสิทธิ์ผู้ดูแลระบบ
        if (!auth()->check()) {
            session([
                'pending_fb_import' => [
                    'text' => $request->input('text'),
                    'images_json' => $request->input('images_json'),
                    'source_url' => $request->input('source_url'),
                ]
            ]);

            return redirect('/admin/login')->with('warning', 'กรุณาเข้าสู่ระบบผู้ดูแลระบบก่อนเพื่อดำเนินการนำเข้าข่าวสาร');
        }

        // กรณีเปิดผ่าน GET ตรงๆ ให้พาไปหน้ารายการข่าวสาร
        if ($request->isMethod('GET')) {
            if (session()->has('pending_fb_import')) {
                $pending = session()->pull('pending_fb_import');
                $text = $pending['text'] ?? '';
                $imagesJson = $pending['images_json'] ?? '[]';
                $images = json_decode($imagesJson, true) ?: [];

                if (!empty($text)) {
                    $news = FacebookImportService::processImport($text, $images);
                    Notification::make()
                        ->title('นำเข้าข่าวสารจาก Facebook สำเร็จ!')
                        ->body('ดาวน์โหลดรูปภาพและเนื้อหาเรียบร้อยแล้ว แอดมินสามารถตรวจสอบและกดบันทึกได้ทันที')
                        ->success()
                        ->send();

                    return redirect(NewsResource::getUrl('edit', ['record' => $news->id]));
                }
            }

            return redirect(NewsResource::getUrl('index'));
        }

        // 2. รับข้อมูล POST จาก Bookmarklet
        $text = (string) $request->input('text', '');
        $imagesJson = (string) $request->input('images_json', '[]');
        $images = json_decode($imagesJson, true) ?: [];

        if (empty(trim($text)) && empty($images)) {
            Notification::make()
                ->title('ไม่พบข้อมูลที่จะนำเข้า')
                ->body('ไม่พบข้อความหรือรูปภาพในโพสต์ที่ส่งมา')
                ->warning()
                ->send();

            return redirect(NewsResource::getUrl('index'));
        }

        // 3. ดำเนินการสร้างข่าวสารและดาวน์โหลดรูปภาพ
        try {
            $news = FacebookImportService::processImport(
                text: $text,
                imageUrls: $images,
                categoryId: null,
                publishedAt: now(),
                isPublished: true
            );

            $imgCount = ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []);

            Notification::make()
                ->title('🎉 นำเข้าข่าวจาก Facebook สำเร็จเรียบร้อย!')
                ->body("ดึงเนื้อหาและดาวน์โหลดรูปภาพเข้ามายังเซิร์ฟเวอร์แล้ว {$imgCount} ภาพ สามารถตรวจสอบและปรับแต่งได้เลย")
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

            return redirect(NewsResource::getUrl('index'));
        }
    }
}

