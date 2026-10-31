<?php

namespace App\Http\Controllers;

use App\Services\FacebookImportService;
use App\Models\News;
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
        if (!auth()->check()) {
            return redirect('/admin/login')->with('warning', 'กรุณาเข้าสู่ระบบผู้ดูแลระบบก่อนดำเนินการนำเข้าข่าวสาร');
        }

        if ($request->isMethod('GET')) {
            return view('filament.pages.facebook-import-receiver');
        }

        // กรณีมีการส่ง POST ตรงเข้ามาแบบด่วน
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

            return redirect(NewsResource::getUrl('index'));
        }
    }

    /**
     * ขั้นตอนที่ 1: สร้างหัวข้อข่าว และดาวน์โหลดรูปภาพชุดแรก (รูปปก + 4 รูปแรก)
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
            // ดาวน์โหลดรูปชุดแรก 5 รูป (เพื่อความรวดเร็วและป้องกัน PHP Timeout)
            $initialBatch = array_slice($images, 0, 6);
            $remainingImages = array_slice($images, 6);

            $news = FacebookImportService::processImport(
                text: $text,
                imageUrls: $initialBatch,
                categoryId: $categoryId,
                publishedAt: null,
                isPublished: true
            );

            return response()->json([
                'success' => true,
                'news_id' => $news->id,
                'downloaded_count' => ($news->cover_image ? 1 : 0) + count($news->gallery_images ?? []),
                'remaining_images' => $remainingImages,
                'redirect' => NewsResource::getUrl('edit', ['record' => $news->id]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ขั้นตอนที่ 2: ดาวน์โหลดรูปภาพที่เหลือแบบ Batch (ทีละ 10 รูป) ป้องกันการหมดเวลาเมื่อมี 70+ รูป
     */
    public function appendBatch(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $newsId = (int) $request->input('news_id');
        $batchImages = (array) $request->input('images', []);

        $news = News::find($newsId);
        if (!$news) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข่าวที่ระบุ'], 404);
        }

        try {
            $added = FacebookImportService::appendGalleryImages($news, $batchImages);

            return response()->json([
                'success' => true,
                'added_count' => $added,
                'total_gallery_count' => count($news->gallery_images ?? []),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
