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
        <button type="button" onclick="openLightbox(0)" class="w-full text-left block cursor-zoom-in group relative focus:outline-none" title="คลิกเพื่อดูรูปขนาดใหญ่">
            <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="w-full h-auto mx-auto object-contain transition duration-200 group-hover:opacity-95">
            <div class="absolute bottom-3 right-3 bg-slate-900/80 text-white text-xs px-2.5 py-1.5 rounded-lg opacity-90 sm:opacity-0 sm:group-hover:opacity-100 transition duration-200 flex items-center gap-1.5 shadow-md backdrop-blur-sm pointer-events-none">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
                <span>คลิกเพื่อดูรูปขยาย</span>
            </div>
        </button>
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
            @foreach($news->gallery_images as $index => $image)
            @php
                $imgIndex = $news->cover_image ? ($index + 1) : $index;
            @endphp
            <button type="button" onclick="openLightbox({{ $imgIndex }})" class="group block aspect-video sm:aspect-square overflow-hidden rounded-lg border border-gray-200 bg-slate-100 hover:shadow-md transition relative text-left w-full focus:outline-none" title="คลิกเพื่อดูภาพขยาย">
                <img src="{{ asset('storage/' . $image) }}" alt="ภาพกิจกรรม" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-lg">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </div>
            </button>
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

@php
    $allImages = [];
    if ($news->cover_image) {
        $allImages[] = asset('storage/' . $news->cover_image);
    }
    if (!empty($news->gallery_images) && is_array($news->gallery_images)) {
        foreach ($news->gallery_images as $img) {
            $allImages[] = asset('storage/' . $img);
        }
    }
@endphp

<!-- Image Lightbox Modal -->
<div id="imageLightboxModal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md items-center justify-center p-4 select-none" style="display: none;" role="dialog" aria-modal="true">
    <!-- Close Button -->
    <button type="button" id="lightboxCloseBtn" class="absolute top-4 right-4 text-white/80 hover:text-white bg-white/10 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center text-lg transition z-30 focus:outline-none" title="ปิด (ESC)">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- Prev Button -->
    <button type="button" id="lightboxPrevBtn" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 text-white/80 hover:text-white bg-white/10 hover:bg-white/25 rounded-full w-11 h-11 sm:w-13 sm:h-13 flex items-center justify-center text-lg sm:text-xl transition z-30 focus:outline-none" title="รูปก่อนหน้า (ลูกศรซ้าย)">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <!-- Next Button -->
    <button type="button" id="lightboxNextBtn" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 text-white/80 hover:text-white bg-white/10 hover:bg-white/25 rounded-full w-11 h-11 sm:w-13 sm:h-13 flex items-center justify-center text-lg sm:text-xl transition z-30 focus:outline-none" title="รูปถัดไป (ลูกศรขวา)">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Image Container -->
    <div class="max-w-5xl max-h-[92vh] flex flex-col items-center justify-center relative mx-auto z-10" id="lightboxContainer">
        <img id="lightboxImage" src="" alt="ขยายรูปภาพ" class="max-h-[80vh] sm:max-h-[84vh] max-w-full object-contain rounded-lg shadow-2xl transition duration-200">
        <div class="mt-3 flex items-center justify-between w-full text-white/80 text-xs px-2 gap-4">
            <span id="lightboxCounter" class="bg-black/50 px-3 py-1 rounded-md">รูปที่ 1 จาก 1</span>
            <a id="lightboxDownloadBtn" href="" download class="bg-black/50 hover:bg-black/70 px-3 py-1 rounded-md text-white/90 hover:text-white flex items-center gap-1.5 transition">
                <i class="fa-solid fa-download"></i> ดาวน์โหลดรูป
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const galleryItems = @json($allImages);
    let currentIndex = 0;

    const modal = document.getElementById('imageLightboxModal');
    const lightboxImg = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    const downloadBtn = document.getElementById('lightboxDownloadBtn');
    const prevBtn = document.getElementById('lightboxPrevBtn');
    const nextBtn = document.getElementById('lightboxNextBtn');

    function updateLightbox() {
        if (!galleryItems || galleryItems.length === 0) return;
        lightboxImg.src = galleryItems[currentIndex];
        downloadBtn.href = galleryItems[currentIndex];
        counter.textContent = `รูปที่ ${currentIndex + 1} จาก ${galleryItems.length}`;
        
        if (galleryItems.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }

    function openLightbox(index) {
        if (!galleryItems || galleryItems.length === 0) return;
        currentIndex = (index >= 0 && index < galleryItems.length) ? index : 0;
        updateLightbox();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function prevImage() {
        if (galleryItems.length <= 1) return;
        currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        updateLightbox();
    }

    function nextImage() {
        if (galleryItems.length <= 1) return;
        currentIndex = (currentIndex + 1) % galleryItems.length;
        updateLightbox();
    }

    if (modal) {
        document.getElementById('lightboxCloseBtn').addEventListener('click', closeLightbox);
        prevBtn.addEventListener('click', function(e) { e.stopPropagation(); prevImage(); });
        nextBtn.addEventListener('click', function(e) { e.stopPropagation(); nextImage(); });

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.id === 'lightboxContainer') {
                closeLightbox();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (modal.style.display !== 'flex') return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevImage();
            if (e.key === 'ArrowRight') nextImage();
        });

        // Touch swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        modal.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, {passive: true});

        modal.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX < touchStartX - 50) nextImage();
            if (touchEndX > touchStartX + 50) prevImage();
        }, {passive: true});
    }
</script>
@endpush