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
        $announcementCategory = Category::where('slug', 'official-announcements')
            ->orWhere('name', 'like', '%ประกาศ%')
            ->first();

        $announcementCategoryIds = Category::where('type', 'news')
            ->where(function ($q) {
                $q->where('slug', 'official-announcements')
                  ->orWhere('name', 'like', '%ประกาศ%');
            })
            ->pluck('id');

        // รายการประกาศและคำสั่งล่าสุด (แสดงบนกระดานประกาศหน้าแรก)
        $announcements = News::with('category')
            ->where('is_published', true)
            ->whereIn('category_id', $announcementCategoryIds)
            ->latest('published_at')
            ->latest('created_at')
            ->take(5)
            ->get();

        // ข่าวสารและกิจกรรมล่าสุด (แยกจากประกาศ)
        $latestNews = News::with('category')
            ->where('is_published', true)
            ->whereNotIn('category_id', $announcementCategoryIds)
            ->latest('published_at')
            ->latest('created_at')
            ->take(6)
            ->get();

        $latestDocuments = LawDocument::with('category')
            ->latest()
            ->take(6)
            ->get();

        // รายการสไลด์ข่าวเด่นและประกาศสำคัญ (Hero Auto-slider)
        $heroSlides = News::with('category')
            ->where('is_published', true)
            ->whereNotNull('cover_image')
            ->latest('published_at')
            ->latest('created_at')
            ->take(6)
            ->get();

        if ($heroSlides->isEmpty()) {
            $heroSlides = News::with('category')
                ->where('is_published', true)
                ->latest('published_at')
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        $totalDocuments = LawDocument::count();
        $totalNews = News::where('is_published', true)->count();

        return view('home', compact('latestNews', 'latestDocuments', 'announcements', 'announcementCategory', 'totalDocuments', 'totalNews', 'heroSlides'));
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

    public function about()
    {
        $presidents = \App\Models\Personnel::president()->active()->orderBy('order_column')->get();
        $committees = \App\Models\Personnel::committee()->active()->orderBy('order_column')->get();
        $lawyers = \App\Models\Personnel::lawyer()->active()->orderBy('order_column')->get();

        $youtubeVideos = \App\Models\YoutubeVideo::active()->get();
        $featuredVideo = \App\Models\YoutubeVideo::featured()->first() ?? $youtubeVideos->first();
        $youtubeChannelUrl = \App\Models\SiteSetting::get('youtube_channel_url', 'https://www.youtube.com/@lawyerscouncilrb');

        return view('pages.about', compact('presidents', 'committees', 'lawyers', 'youtubeVideos', 'featuredVideo', 'youtubeChannelUrl'));
    }
}