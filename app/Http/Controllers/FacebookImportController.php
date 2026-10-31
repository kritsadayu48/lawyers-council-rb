<?php

namespace App\Http\Controllers;

use App\Services\FacebookImportService;
use App\Filament\Resources\NewsResource;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class FacebookImportController extends Controller
{
    /**
     * หน้าจอรับข้อมูลจาก Bookmarklet หรือนำเข้าด่วน
     */
    public function handle(Request $request)
    {
        // 1. ต้องเข้าสู่ระบบผู้ดูแลระบบก่อน
        if (!auth()->check()) {
            return redirect('/admin/login')->with('warning', 'กรุณาเข้าสู่ระบบผู้ดูแลระบบก่อนดำเนินการนำเข้าข่าวสาร');
        }

        // กรณีเปิดผ่าน GET (Bookmarklet จะเปิดหน้านี้พร้อม #data=...)
        if ($request->isMethod('GET')) {
            return view('filament.pages.facebook-import-receiver');
        }

        // กรณีมีการส่ง POST ตรงเข้ามา
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

        try {
            $news = FacebookImportService::processImport(
                text: $text,
                imageUrls: $images,
                categoryId: null,
                publishedAt: null,
                isPublished: true
            );

            $imgCount = ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []);

            Notification::make()
                ->title('🎉 นำเข้าข่าวจาก Facebook สำเร็จเรียบร้อย!')
                ->body("ดึงเนื้อหาและดาวน์โหลดรูปภาพเข้ามาแล้ว {$imgCount} ภาพ สามารถตรวจสอบและปรับแต่งได้เลย")
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

    /**
     * API ประมวลผลและสร้างข่าวสาร (Same-Origin Fetch จากหน้า Receiver)
     */
    public function process(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $text = trim((string) $request->input('text', ''));
        $images = (array) $request->input('images', []);
        $categoryId = $request->input('category_id') ? (int) $request->input('category_id') : null;

        if (empty($text) && empty($images)) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อความหรือรูปภาพสำหรับสร้างข่าว'], 422);
        }

        try {
            $news = FacebookImportService::processImport(
                text: $text,
                imageUrls: $images,
                categoryId: $categoryId,
                publishedAt: null,
                isPublished: true
            );

            $imgCount = ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []);

            Notification::make()
                ->title('🎉 นำเข้าข่าวจาก Facebook สำเร็จ!')
                ->body("ดึงเนื้อหาและดาวน์โหลดรูปภาพ {$imgCount} ภาพเรียบร้อยแล้ว แอดมินสามารถตรวจสอบหรือแก้ไขได้ทันที")
                ->success()
                ->duration(8000)
                ->send();

            return response()->json([
                'success' => true,
                'news_id' => $news->id,
                'redirect' => NewsResource::getUrl('edit', ['record' => $news->id]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
