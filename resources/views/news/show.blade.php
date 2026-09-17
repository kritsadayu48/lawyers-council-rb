@extends('layouts.app')

@section('title', $news->title . ' - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
    <!-- หมวดหมู่และวันที่ -->
    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
        <span class="bg-amber-50 text-amber-700 font-semibold px-2.5 py-1 rounded">
            {{ $news->category->name ?? 'ข่าวทั่วไป' }}
        </span>
        <span><i class="fa-regular fa-calendar mr-1"></i> {{ optional($news->published_at)->format('d/m/Y') ?? $news->created_at->format('d/m/Y') }}</span>
    </div>

    <!-- หัวข้อข่าว -->
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 leading-snug mb-6">
        {{ $news->title }}
    </h1>

    <!-- ภาพปกข่าว (ถ้ามี) -->
    @if($news->cover_image)
    <div class="mb-6 rounded-xl overflow-hidden border border-gray-200 bg-slate-50">
        <a href="{{ asset('storage/' . $news->cover_image) }}" target="_blank" rel="noopener noreferrer" class="block cursor-zoom-in group relative" title="คลิกเพื่อดูรูปภาพขนาดเต็ม">
            <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="w-full h-auto mx-auto object-contain transition duration-200 group-hover:opacity-95">
            <div class="absolute bottom-3 right-3 bg-slate-900/75 text-white text-xs px-2.5 py-1 rounded-md opacity-80 sm:opacity-0 sm:group-hover:opacity-100 transition duration-200 flex items-center gap-1.5 shadow-sm backdrop-blur-sm pointer-events-none">
                <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                <span>ดูรูปขนาดเต็ม</span>
            </div>
        </a>
    </div>
    @endif

    <!-- เนื้อหาข่าว -->
    <div class="prose max-w-none text-slate-700 leading-relaxed text-sm md:text-base border-b pb-8 space-y-4">
        {!! $news->content !!}
    </div>

    <!-- ภาพบรรยากาศกิจกรรม / คลังภาพ (ถ้ามี) -->
    @if($news->gallery_images && count($news->gallery_images) > 0)
    <div class="py-6 border-b">
        <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
            <i class="fa-regular fa-images text-amber-600"></i> ภาพบรรยากาศกิจกรรม ({{ count($news->gallery_images) }} ภาพ)
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($news->gallery_images as $image)
            <a href="{{ asset('storage/' . $image) }}" target="_blank" class="group block aspect-video sm:aspect-square overflow-hidden rounded-lg border border-gray-200 bg-slate-100 hover:shadow-md transition relative" title="คลิกเพื่อดูภาพขยาย">
                <img src="{{ asset('storage/' . $image) }}" alt="ภาพกิจกรรม" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-lg">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ข่าวอื่นๆ ที่น่าสนใจ -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 text-base border-l-4 border-amber-600 pl-3">ข่าวประชาสัมพันธ์อื่นๆ</h3>
            <a href="{{ route('news.index') }}" class="text-xs font-semibold text-amber-600 hover:underline">ดูข่าวทั้งหมด →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($otherNews as $item)
            <a href="{{ route('news.show', $item) }}" class="p-3 rounded-lg border hover:border-amber-300 hover:bg-slate-50 transition block">
                <span class="text-[11px] text-amber-600 font-medium">{{ $item->category->name ?? 'ข่าวสาร' }}</span>
                <h4 class="text-sm font-semibold text-slate-800 line-clamp-1 mt-1">{{ $item->title }}</h4>
                <span class="text-[11px] text-gray-400 mt-2 block"><i class="fa-regular fa-calendar mr-1"></i> {{ optional($item->published_at)->format('d/m/Y') ?? $item->created_at->format('d/m/Y') }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <div class="mt-8 pt-6 border-t flex items-center justify-between text-xs">
        <a href="{{ route('news.index') }}" class="inline-flex items-center font-semibold text-slate-600 hover:text-amber-600 transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> กลับสู่หน้ารวมข่าวสาร
        </a>
        <a href="{{ route('home') }}" class="inline-flex items-center font-semibold text-slate-600 hover:text-amber-600 transition">
            <i class="fa-solid fa-house mr-1.5"></i> หน้าแรก
        </a>
    </div>
</div>
@endsection