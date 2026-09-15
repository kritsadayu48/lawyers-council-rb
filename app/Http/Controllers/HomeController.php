<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\LawDocument;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $announcementCategory = Category::where('slug', 'official-announcements')->first();

        // รายการประกาศและคำสั่งล่าสุด
        $announcements = News::with('category')
            ->where('is_published', true)
            ->where('category_id', $announcementCategory?->id)
            ->latest('published_at')
            ->latest('created_at')
            ->take(4)
            ->get();

        // ข่าวสารและกิจกรรมล่าสุด (แยกจากประกาศ)
        $latestNews = News::with('category')
            ->where('is_published', true)
            ->when($announcementCategory, fn($q) => $q->where('category_id', '!=', $announcementCategory->id))
            ->latest('published_at')
            ->latest('created_at')
            ->take(6)
            ->get();

        $latestDocuments = LawDocument::with('category')
            ->latest()
            ->take(6)
            ->get();

        $totalDocuments = LawDocument::count();
        $totalNews = News::where('is_published', true)->count();

        return view('home', compact('latestNews', 'latestDocuments', 'announcements', 'announcementCategory', 'totalDocuments', 'totalNews'));
    }

    public function showNews(News $news)
    {
        // ข่าวอื่นๆ ที่เกี่ยวข้อง/ล่าสุด 4 ข่าว
        $otherNews = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('news.show', compact('news', 'otherNews'));
    }

    public function newsIndex(Request $request)
    {
        $categories = Category::where('type', 'news')->get();

        $query = News::with('category')->where('is_published', true);

        // ค้นหาตามหัวข้อหรือเนื้อหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // กรองตามหมวดหมู่ข่าว
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $newsList = $query->latest('published_at')->latest('created_at')->paginate(9)->withQueryString();

        return view('news.index', compact('newsList', 'categories'));
    }
}