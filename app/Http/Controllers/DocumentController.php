<?php

namespace App\Http\Controllers;

use App\Models\LawDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'law_document')->get();

        $query = LawDocument::with('category');

        // ค้นหาตามคำค้น (Title หรือ Document No)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_no', 'like', "%{$search}%");
            });
        }

        // กรองตามหมวดหมู่
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // กรองตามปี พ.ศ.
        if ($request->filled('year')) {
            $query->where('year_be', $request->year);
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        return view('documents.index', compact('documents', 'categories'));
    }

    // ฟังก์ชันดาวน์โหลดและนับจำนวนครั้ง
    public function download(LawDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'ไม่พบไฟล์เอกสารในระบบ');
        }

        $document->increment('download_count');
        return Storage::disk('public')->download($document->file_path);
    }
}