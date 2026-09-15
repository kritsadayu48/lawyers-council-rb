@extends('layouts.app')

@section('title', 'ข่าวสารและกิจกรรม - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="space-y-6">

    <!-- ส่วนหัวข้อหน้าเว็บ -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2.5">
                <i class="fa-solid fa-newspaper text-amber-600"></i> ข่าวสารและกิจกรรม
            </h2>
            <p class="text-sm text-gray-500 mt-1">ติดตามข่าวประชาสัมพันธ์ ประกาศคำสั่ง หนังสือเวียน และภาพกิจกรรมสภาทนายความจังหวัดราชบุรี</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                <i class="fa-regular fa-clock"></i> อัปเดตล่าสุด: {{ date('d/m/') . (date('Y') + 543) }}
            </span>
        </div>
    </div>

    <!-- ฟอร์มค้นหาและตัวกรองหมวดหมู่ -->
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <form method="GET" action="{{ route('news.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <!-- ช่องค้นหา -->
            <div class="md:col-span-6">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">ค้นหาข่าวสาร</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="พิมพ์คำค้นหาหัวข้อข่าว หรือเนื้อหา..." 
                           class="w-full text-sm border-gray-300 rounded-lg pl-9 pr-3 py-2.5 border focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 absolute left-3 top-3 text-sm"></i>
                </div>
            </div>

            <!-- เลือกหมวดหมู่ -->
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">หมวดหมู่ข่าว</label>
                <select name="category_id" class="w-full text-sm border-gray-300 rounded-lg px-3 py-2.5 border focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition">
                    <option value="">-- ทุกหมวดหมู่ข่าวสาร --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ปุ่มดำเนินการ -->
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> กรอง
                </button>
                @if(request('search') || request('category_id'))
                <a href="{{ route('news.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 px-3 rounded-lg text-sm transition" title="ล้างตัวกรอง">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>

        <!-- หมวดหมู่แบบ Quick Pill Filter -->
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 text-xs">
            <span class="text-gray-500 font-medium">หมวดหมู่ด่วน:</span>
            <a href="{{ route('news.index') }}" 
               class="px-3 py-1 rounded-full transition {{ !request('category_id') ? 'bg-amber-600 text-white font-medium' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                ทั้งหมด ({{ $newsList->total() }})
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('news.index', ['category_id' => $cat->id]) }}" 
               class="px-3 py-1 rounded-full transition {{ request('category_id') == $cat->id ? 'bg-amber-600 text-white font-medium' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- ตารางการ์ดแสดงข่าวสาร -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($newsList as $news)
        <a href="{{ route('news.show', $news) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md hover:border-amber-200 transition group">
            <div class="h-48 overflow-hidden bg-slate-100 relative">
                @if($news->cover_image)
                    <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                @else
                    <div class="h-full w-full flex items-center justify-center text-gray-400 bg-slate-100">
                        <i class="fa-regular fa-image text-4xl text-gray-300"></i>
                    </div>
                @endif
                <span class="absolute top-3 left-3 text-[11px] font-medium text-amber-900 bg-amber-50/95 backdrop-blur-sm px-2.5 py-1 rounded-md border border-amber-200/80 shadow-sm">
                    {{ $news->category->name ?? 'ข่าวทั่วไป' }}
                </span>
                @if($news->gallery_images && count($news->gallery_images) > 0)
                <span class="absolute bottom-3 right-3 text-[10px] font-medium text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-regular fa-images"></i> {{ count($news->gallery_images) }} ภาพ
                </span>
                @endif
            </div>
            <div class="p-5 flex-grow flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-800 line-clamp-2 group-hover:text-amber-600 transition leading-snug">
                        {{ $news->title }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-2 line-clamp-3 leading-relaxed">
                        {{ Str::limit(strip_tags($news->content), 120) }}
                    </p>
                </div>
                <div class="text-xs text-gray-400 mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i> {{ optional($news->published_at)->format('d/m/Y') ?? $news->created_at->format('d/m/Y') }}</span>
                    <span class="text-amber-600 font-medium group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        อ่านต่อ <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full bg-white rounded-xl p-12 text-center border border-dashed border-gray-200">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
                <i class="fa-regular fa-newspaper"></i>
            </div>
            <h4 class="text-base font-semibold text-slate-800">ไม่พบข่าวสารหรือกิจกรรม</h4>
            <p class="text-xs text-gray-500 mt-1">ยังไม่มีข้อมูลข่าวในหมวดหมู่นี้ หรือไม่พบตามคำค้นหา</p>
            @if(request('search') || request('category_id'))
            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 mt-4 text-xs font-medium text-amber-600 hover:underline">
                <i class="fa-solid fa-rotate-left"></i> ดูข่าวสารทั้งหมด
            </a>
            @endif
        </div>
        @endforelse
    </div>

    <!-- แถบแบ่งหน้า (Pagination) -->
    <div class="mt-8">
        {{ $newsList->links() }}
    </div>

</div>
@endsection
