@extends('layouts.app')

@section('title', 'หน้าแรก - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="space-y-10">

    <!-- 1. Grand Full-Width Announcement Banner Slider (แบนเนอร์ภาพประกาศขนาดใหญ่เด่นชัดแบบ lawyerscouncil.or.th) -->
    @if(isset($heroSlides) && $heroSlides->count() > 0)
    <div id="grandHeroCarousel" class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-700/80 bg-slate-950 group select-none">
        <!-- Slides Container -->
        <div class="relative h-[280px] sm:h-[400px] md:h-[480px] lg:h-[540px] w-full overflow-hidden">
            @foreach($heroSlides as $index => $slide)
            @php
                $isAnnouncement = $slide->category && (
                    str_contains($slide->category->slug, 'announcement') ||
                    str_contains($slide->category->slug, 'official') ||
                    str_contains($slide->category->name, 'ประกาศ')
                );
            @endphp
            <div class="grand-slide absolute inset-0 transition-all duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 scale-100 z-10 pointer-events-auto' : 'opacity-0 scale-95 z-0 pointer-events-none' }}" data-index="{{ $index }}">
                <a href="{{ route('news.show', $slide) }}" class="block w-full h-full relative group/item focus:outline-none">
                    @if($slide->cover_image)
                        <!-- Ambient Blur Backdrop on Wide Screens -->
                        <div class="absolute inset-0 bg-cover bg-center blur-2xl opacity-30 scale-110" style="background-image: url('{{ asset('storage/' . $slide->cover_image) }}');"></div>
                        <!-- Main Sharp Banner Image -->
                        <img src="{{ asset('storage/' . $slide->cover_image) }}" alt="{{ $slide->title }}" class="relative w-full h-full object-contain md:object-cover mx-auto transition-transform duration-700 group-hover/item:scale-[1.02]">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 flex items-center justify-center text-gray-500">
                            <i class="fa-regular fa-image text-6xl text-slate-700"></i>
                        </div>
                    @endif

                    <!-- Gradient Overlay for High Contrast Text Readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

                    <!-- Top Bar Badges -->
                    <div class="absolute top-4 sm:top-6 left-4 sm:left-6 flex items-center gap-2 z-20">
                        @if($isAnnouncement)
                            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs sm:text-sm px-3.5 py-1.5 rounded-full shadow-lg border border-amber-300/40">
                                <i class="fa-solid fa-bullhorn text-xs"></i> ประกาศสภาทนายความ
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs sm:text-sm px-3.5 py-1.5 rounded-full shadow-lg border border-amber-300/40">
                                <i class="fa-solid fa-star text-xs"></i> ข่าวเด่นและกิจกรรม
                            </span>
                        @endif
                    </div>

                    <!-- Slide Counter Badge -->
                    <div class="absolute top-4 sm:top-6 right-4 sm:right-6 bg-black/60 backdrop-blur-md border border-white/20 text-white text-xs sm:text-sm font-medium px-3.5 py-1 rounded-full shadow-lg z-20">
                        <span class="text-amber-400 font-bold">{{ $index + 1 }}</span> / {{ $heroSlides->count() }}
                    </div>

                    <!-- Bottom Caption Bar -->
                    <div class="absolute bottom-6 sm:bottom-8 left-4 sm:left-8 right-4 sm:right-8 text-white z-20">
                        <div class="max-w-4xl">
                            <div class="flex items-center gap-2 text-xs sm:text-sm text-amber-400 font-semibold mb-2 drop-shadow">
                                <span><i class="fa-regular fa-calendar mr-1.5"></i> {{ optional($slide->published_at)->format('d/m/Y') ?? $slide->created_at->format('d/m/Y') }}</span>
                                <span>•</span>
                                <span class="text-gray-200">{{ $slide->category->name ?? 'ทั่วไป' }}</span>
                            </div>
                            <h2 class="text-base sm:text-2xl md:text-3xl font-extrabold text-white leading-tight drop-shadow-md line-clamp-2 group-hover/item:text-amber-400 transition-colors duration-200">
                                {{ $slide->title }}
                            </h2>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm px-4 py-2 rounded-lg shadow-lg transition">
                                    อ่านรายละเอียดประกาศ <i class="fa-solid fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        @if($heroSlides->count() > 1)
        <!-- Prominent Navigation Arrows (แบบ lawyerscouncil.or.th) -->
        <button type="button" id="grandPrevBtn" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/50 hover:bg-amber-600 text-white flex items-center justify-center text-lg sm:text-2xl backdrop-blur-md border border-white/20 transition-all shadow-2xl z-30 focus:outline-none" aria-label="Previous Slide">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button type="button" id="grandNextBtn" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/50 hover:bg-amber-600 text-white flex items-center justify-center text-lg sm:text-2xl backdrop-blur-md border border-white/20 transition-all shadow-2xl z-30 focus:outline-none" aria-label="Next Slide">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Slide Indicators / Dots -->
        <div class="absolute bottom-2.5 left-1/2 -translate-x-1/2 flex items-center gap-2 z-30 bg-black/50 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/15">
            @foreach($heroSlides as $dotIndex => $slide)
            <button type="button" class="grand-dot h-2 rounded-full transition-all duration-300 {{ $dotIndex === 0 ? 'w-7 bg-amber-500' : 'w-2.5 bg-white/50 hover:bg-white/80' }}" data-index="{{ $dotIndex }}" aria-label="Go to slide {{ $dotIndex + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Carousel Logic Script -->
    @if($heroSlides->count() > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('grandHeroCarousel');
            if (!carousel) return;

            const slides = carousel.querySelectorAll('.grand-slide');
            const dots = carousel.querySelectorAll('.grand-dot');
            const prevBtn = document.getElementById('grandPrevBtn');
            const nextBtn = document.getElementById('grandNextBtn');
            const total = slides.length;
            let currentIndex = 0;
            let timer = null;
            const INTERVAL_MS = 5000; // 5 seconds

            function showSlide(index) {
                if (index < 0) index = total - 1;
                if (index >= total) index = 0;
                currentIndex = index;

                slides.forEach((slide, i) => {
                    if (i === currentIndex) {
                        slide.classList.remove('opacity-0', 'scale-95', 'z-0', 'pointer-events-none');
                        slide.classList.add('opacity-100', 'scale-100', 'z-10', 'pointer-events-auto');
                    } else {
                        slide.classList.remove('opacity-100', 'scale-100', 'z-10', 'pointer-events-auto');
                        slide.classList.add('opacity-0', 'scale-95', 'z-0', 'pointer-events-none');
                    }
                });

                dots.forEach((dot, i) => {
                    if (i === currentIndex) {
                        dot.classList.remove('w-2.5', 'bg-white/50');
                        dot.classList.add('w-7', 'bg-amber-500');
                    } else {
                        dot.classList.remove('w-7', 'bg-amber-500');
                        dot.classList.add('w-2.5', 'bg-white/50');
                    }
                });
            }

            function nextSlide() {
                showSlide(currentIndex + 1);
            }

            function prevSlide() {
                showSlide(currentIndex - 1);
            }

            function startTimer() {
                stopTimer();
                timer = setInterval(nextSlide, INTERVAL_MS);
            }

            function stopTimer() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    nextSlide();
                    startTimer();
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    prevSlide();
                    startTimer();
                });
            }

            dots.forEach(function(dot) {
                dot.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const idx = parseInt(dot.getAttribute('data-index'), 10);
                    showSlide(idx);
                    startTimer();
                });
            });

            carousel.addEventListener('mouseenter', stopTimer);
            carousel.addEventListener('mouseleave', startTimer);
            carousel.addEventListener('touchstart', stopTimer, { passive: true });
            carousel.addEventListener('touchend', startTimer, { passive: true });

            startTimer();
        });
    </script>
    @endif
    @endif

    <!-- 2. Welcome & Organization Overview Bar (แถบข้อมูลองค์กรและบริการด่วน) -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-xl overflow-hidden border border-slate-700/50 p-6 sm:p-8 md:p-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-8">
                <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                    <i class="fa-solid fa-scale-balanced"></i> สภาทนายความในพระบรมราชูปถัมภ์ จังหวัดราชบุรี
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                    สภาทนายความ<span class="text-amber-500">จังหวัดราชบุรี</span>
                </h2>
                <p class="text-gray-300 text-sm md:text-base leading-relaxed mt-2.5 max-w-2xl">
                    ศูนย์รวมข้อมูลข่าวสาร ระเบียบข้อบังคับสภาทนายความ คลังเอกสารทางกฎหมาย และการให้บริการปรึกษาอรรถคดีแก่ประชาชนผู้ยากไร้เพื่อผดุงความยุติธรรมในสังคม
                </p>
                <div class="flex flex-wrap gap-2.5 sm:gap-3 mt-5">
                    <a href="{{ route('documents.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 sm:px-5 py-2.5 sm:py-3 rounded-lg shadow-lg shadow-amber-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-folder-open"></i> คลังเอกสารกฎหมาย
                    </a>
                    <a href="{{ route('news.index') }}" class="bg-slate-700/80 hover:bg-slate-700 text-white text-sm font-semibold px-4 sm:px-5 py-2.5 sm:py-3 rounded-lg border border-slate-600 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-newspaper"></i> ข่าวสารและกิจกรรม
                    </a>
                    <a href="tel:0971952029" class="bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-white/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-phone text-amber-400"></i> โทรปรึกษา: 097-195-2029
                    </a>
                    <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="bg-[#1877F2] hover:bg-[#166fe5] text-white text-sm font-semibold px-4 sm:px-5 py-2.5 sm:py-3 rounded-lg shadow-lg shadow-blue-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-facebook text-base"></i> เพจ Facebook
                    </a>
                </div>
            </div>

            <!-- Right Stats Grid -->
            <div class="lg:col-span-4 grid grid-cols-3 gap-3 bg-slate-800/60 p-5 rounded-xl border border-slate-700/60 text-center">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-400">{{ $totalDocuments }}+</div>
                    <div class="text-[11px] sm:text-xs text-gray-300 font-medium mt-1">เอกสารในระบบ</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-400">{{ $totalNews }}</div>
                    <div class="text-[11px] sm:text-xs text-gray-300 font-medium mt-1">ข่าว/ประกาศ</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-400">จ. - ศ.</div>
                    <div class="text-[11px] sm:text-xs text-gray-300 font-medium mt-1">ทนายความอาสา</div>
                </div>
            </div>
        </div>
    </div>

    <!-- กระดานประกาศและหนังสือเวียนสภาทนายความจังหวัดราชบุรี -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-base text-white">ประกาศและคำสั่งสภาทนายความจังหวัดราชบุรี</h3>
                        <span class="bg-amber-500 text-slate-950 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">ล่าสุด</span>
                    </div>
                    <p class="text-xs text-gray-300 mt-0.5">ประกาศทางการ ระเบียบคำสั่ง และหนังสือเวียนสำหรับทนายความและประชาชน</p>
                </div>
            </div>
            @if(isset($announcementCategory))
            <a href="{{ route('news.index', ['category_id' => $announcementCategory->id]) }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 transition flex items-center gap-1 shrink-0">
                ดูประกาศทั้งหมด <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
            @endif
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($announcements as $ann)
            <a href="{{ route('news.show', $ann) }}" class="p-4 sm:px-6 hover:bg-amber-50/20 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                <div class="flex items-start sm:items-center gap-3 overflow-hidden">
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded shrink-0 whitespace-nowrap">
                        <i class="fa-regular fa-calendar text-slate-400"></i>
                        {{ optional($ann->published_at)->format('d/m/Y') ?? $ann->created_at->format('d/m/Y') }}
                    </span>
                    <span class="text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded shrink-0 whitespace-nowrap">
                        ประกาศ
                    </span>
                    <h4 class="text-sm font-medium text-slate-800 group-hover:text-amber-700 transition truncate">
                        {{ $ann->title }}
                    </h4>
                </div>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-amber-700 transition shrink-0 hidden sm:flex items-center gap-1">
                    อ่านประกาศ <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>
            @empty
            <div class="p-6 text-center text-xs text-gray-400">
                ยังไม่มีประกาศทางการในขณะนี้
            </div>
            @endforelse
        </div>
    </div>

    <!-- 2. เมนูลัดบริการประชาชนและทนายความ (E-Services & Quick Links) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-amber-600"></i> บริการประชาชนและสมาชิกทนายความ
            </h3>
            <span class="text-xs text-slate-500 font-medium">สภาทนายความจังหวัดราชบุรี</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <!-- บริการ 1: ตรวจสอบสถานะทนายความ -->
            <a href="https://www.lawyerscouncil.or.th" target="_blank" class="p-6 hover:bg-slate-50/80 transition flex flex-col justify-between group">
                <div>
                    <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-700 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-lg mb-3.5 transition-colors">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition">ตรวจสอบสถานะทนายความ</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        ตรวจสอบสถานะใบอนุญาตให้เป็นทนายความผ่านระบบสภาทนายความส่วนกลาง
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-600 group-hover:text-amber-700 mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span>เข้าสู่ระบบตรวจสอบ</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] group-hover:translate-x-0.5 transition-transform"></i>
                </div>
            </a>

            <!-- บริการ 2: ทนายความอาสาประจำศาล -->
            <a href="{{ route('news.index', ['category_id' => 1]) }}" class="p-6 hover:bg-slate-50/80 transition flex flex-col justify-between group">
                <div>
                    <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-700 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-lg mb-3.5 transition-colors">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition">ทนายความอาสาประจำศาล</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        บริการให้คำปรึกษาปัญหาทางกฎหมายฟรี ณ ศาลจังหวัดราชบุรี ในวันและเวลาราชการ
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-600 group-hover:text-amber-700 mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span>ดูรายละเอียดและเวลาทำการ</span>
                    <i class="fa-solid fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- บริการ 3: แบบฟอร์มคำขอและคดีความ -->
            <a href="{{ route('documents.index') }}" class="p-6 hover:bg-slate-50/80 transition flex flex-col justify-between group">
                <div>
                    <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-700 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-lg mb-3.5 transition-colors">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition">แบบฟอร์มคำขอ / ร้องทุกข์</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        ดาวน์โหลดแบบฟอร์มขึ้นทะเบียน, ต่ออายุใบอนุญาต (ท.1, ท.2) และแบบขอรับความช่วยเหลือ
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-600 group-hover:text-amber-700 mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span>ดาวน์โหลดแบบฟอร์ม</span>
                    <i class="fa-solid fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- บริการ 4: ติดต่อสายด่วน -->
            <a href="{{ route('contact') }}" class="p-6 hover:bg-slate-50/80 transition flex flex-col justify-between group">
                <div>
                    <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-700 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-lg mb-3.5 transition-colors">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition">ติดต่อสภาทนายความ</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        สำนักงานศาลจังหวัดราชบุรี โทร. 097-195-2029 หรืออีเมล Lawyerscouncilrb@gmail.com
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-600 group-hover:text-amber-700 mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span>ช่องทางติดต่อและแผนที่</span>
                    <i class="fa-solid fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- 3. ข่าวประชาสัมพันธ์และภาพกิจกรรมล่าสุด -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 border-l-4 border-slate-700 pl-3">ข่าวสารและกิจกรรมล่าสุด</h3>
                <p class="text-xs text-gray-400 mt-0.5">ติดตามข่าวสาร อบรมสัมมนา และประกาศคำสั่งของสภาทนายความจังหวัดราชบุรี</p>
            </div>
            <a href="{{ route('news.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 whitespace-nowrap">
                ดูข่าวทั้งหมด →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse($latestNews as $news)
            <a href="{{ route('news.show', $news) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition group">
                <div class="h-48 overflow-hidden bg-slate-100 relative">
                    @if($news->cover_image)
                        <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                            <i class="fa-regular fa-image text-3xl"></i>
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
                        <h4 class="font-bold text-sm text-slate-800 line-clamp-2 group-hover:text-amber-600 transition leading-snug">
                            {{ $news->title }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-2 line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($news->content), 100) }}
                        </p>
                    </div>
                    <div class="text-[11px] text-gray-400 mt-4 pt-3 border-t flex items-center justify-between">
                        <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i> {{ optional($news->published_at)->format('d/m/Y') ?? $news->created_at->format('d/m/Y') }}</span>
                        <span class="text-amber-600 font-medium group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                            อ่านต่อ <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-12 text-gray-400 bg-white rounded-xl border border-dashed">
                <i class="fa-regular fa-newspaper text-3xl mb-2 block text-gray-300"></i>
                ยังไม่มีข่าวประชาสัมพันธ์ในระบบ
            </div>
            @endforelse
        </div>
    </div>

    <!-- 4. ส่วนเอกสารกฎหมายและข้อบังคับล่าสุด (ไฮไลต์) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 border-l-4 border-amber-600 pl-3">เอกสารกฎหมายและข้อบังคับล่าสุด</h3>
                <p class="text-xs text-gray-400 mt-0.5">พระราชบัญญัติ ระเบียบข้อบังคับ และแบบฟอร์มคำขอดาวน์โหลด</p>
            </div>
            <a href="{{ route('documents.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 whitespace-nowrap">ดูทั้งหมดในคลังเอกสาร →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse($latestDocuments as $doc)
            <div class="flex items-center justify-between p-3.5 rounded-lg border border-gray-100 hover:border-amber-200 hover:bg-amber-50/20 transition">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-pdf text-lg"></i>
                    </div>
                    <div class="truncate">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $doc->title }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            <span class="text-amber-700 font-medium">{{ $doc->category->name ?? 'ทั่วไป' }}</span>
                            @if($doc->document_no) | เลขที่ {{ $doc->document_no }} @endif
                            @if($doc->year_be) | ปี พ.ศ. {{ $doc->year_be }} @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0 ml-3">
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs transition" title="เปิดดูเอกสาร">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                    <a href="{{ route('documents.download', $doc) }}" class="p-1.5 px-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded text-xs font-medium transition" title="ดาวน์โหลดไฟล์">
                        <i class="fa-solid fa-download mr-1"></i> โหลด
                    </a>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 col-span-2 py-4 text-center">ยังไม่มีเอกสารในระบบ</p>
            @endforelse
        </div>
    </div>

    <!-- 5. Official Facebook Fanpage Section (เพจเฟซบุ๊กทางการ สภาทนายความจังหวัดราชบุรี) -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-slate-900 rounded-2xl p-6 sm:p-8 text-white border border-blue-800/50 shadow-xl overflow-hidden relative">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-[#1877F2] text-[180px] pointer-events-none">
            <i class="fa-brands fa-facebook"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5 text-center sm:text-left">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#1877F2] text-white flex items-center justify-center text-3xl sm:text-4xl shadow-lg shadow-blue-500/30 shrink-0">
                    <i class="fa-brands fa-facebook-f"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-300 bg-blue-950/80 border border-blue-700/60 px-2.5 py-0.5 rounded-full mb-1">
                        <i class="fa-solid fa-circle-check text-blue-400"></i> ช่องทางโซเชียลมีเดียทางการ
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-white leading-tight">
                        เพจเฟซบุ๊กทางการ: สภาทนายความจังหวัดราชบุรี
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1 max-w-xl leading-relaxed">
                        ติดตามข้อมูลข่าวสาร ภาพบรรยากาศกิจกรรม อบรมวิชาการ และสาระความรู้กฎหมายที่เป็นประโยชน์แก่ประชาชนและเพื่อนสมาชิกทนายความได้ทุกวัน
                    </p>
                </div>
            </div>
            <div class="shrink-0 w-full md:w-auto">
                <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="w-full md:w-auto bg-[#1877F2] hover:bg-[#166fe5] text-white font-bold text-sm px-6 py-3.5 rounded-xl shadow-lg shadow-blue-600/40 transition-all flex items-center justify-center gap-2 group">
                    <i class="fa-brands fa-facebook text-lg"></i>
                    <span>กดติดตามเพจ Facebook</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 6. เครือข่ายหน่วยงานในกระบวนการยุติธรรม (Justice Network Partners) -->
    <div class="bg-slate-100/70 p-6 rounded-xl border border-slate-200/80">
        <h3 class="text-sm font-bold text-slate-700 mb-4 text-center uppercase tracking-wide">
            <i class="fa-solid fa-landmark text-amber-600 mr-1.5"></i> เครือข่ายหน่วยงานในกระบวนการยุติธรรม
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 text-center text-xs">
            <a href="https://www.lawyerscouncil.or.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <img src="{{ asset('images/logo.png') }}" alt="สภาทนายความ" class="w-8 h-8 object-contain group-hover:scale-110 transition">
                <span class="font-medium text-slate-800">สภาทนายความฯ (ส่วนกลาง)</span>
            </a>
            <a href="https://rbnc.coj.go.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <i class="fa-solid fa-gavel text-slate-700 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium text-slate-800">ศาลจังหวัดราชบุรี</span>
            </a>
            <a href="https://ago.go.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <i class="fa-solid fa-briefcase text-slate-700 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium text-slate-800">สำนักงานอัยการจังหวัดราชบุรี</span>
            </a>
            <a href="https://www.ratchaburi.police.go.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <i class="fa-solid fa-shield text-slate-700 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium text-slate-800">ตำรวจภูธรจังหวัดราชบุรี</span>
            </a>
            <a href="https://www.moj.go.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <i class="fa-solid fa-building-columns text-slate-700 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium text-slate-800">ยุติธรรมจังหวัดราชบุรี</span>
            </a>
        </div>
    </div>

</div>
@endsection