@extends('layouts.app')

@section('title', 'หน้าแรก - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="space-y-10">

    <!-- 1. Hero Showcase Section -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl text-white shadow-xl overflow-hidden border border-slate-700/50">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-7 p-8 md:p-10 z-10">
                <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    <i class="fa-solid fa-scale-balanced"></i> สภาทนายความในพระบรมราชูปถัมภ์ จังหวัดราชบุรี
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight tracking-tight">
                    สภาทนายความ<span class="text-amber-500">จังหวัดราชบุรี</span>
                </h2>
                <p class="text-gray-300 text-sm md:text-base leading-relaxed mt-3 mb-6 max-w-xl">
                    ศูนย์รวมข้อมูลข่าวสาร ระเบียบข้อบังคับสภาทนายความ คลังเอกสารทางกฎหมาย และการให้บริการปรึกษาอรรถคดีแก่ประชาชนผู้ยากไร้เพื่อผดุงความยุติธรรมในสังคม
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('documents.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-5 py-3 rounded-lg shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-folder-open"></i> คลังเอกสารกฎหมาย
                    </a>
                    <a href="{{ route('news.index') }}" class="bg-slate-700/80 hover:bg-slate-700 text-white text-sm font-semibold px-5 py-3 rounded-lg border border-slate-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-newspaper"></i> ข่าวสารและกิจกรรม
                    </a>
                    <a href="tel:0971952029" class="bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-4 py-3 rounded-lg border border-white/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-phone text-amber-400"></i> โทรปรึกษา
                    </a>
                </div>

                <!-- Stats Bar -->
                <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-700/60 text-center sm:text-left">
                    <div>
                        <div class="text-2xl font-bold text-amber-400">{{ $totalDocuments }}+</div>
                        <div class="text-[11px] text-gray-400 font-medium">เอกสารในระบบ</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-amber-400">{{ $totalNews }}</div>
                        <div class="text-[11px] text-gray-400 font-medium">ข่าวสารและกิจกรรม</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-amber-400">จ. - ศ.</div>
                        <div class="text-[11px] text-gray-400 font-medium">ทนายความอาสาประจำศาล</div>
                    </div>
                </div>
            </div>

            <!-- Right Featured News / Banner -->
            <div class="lg:col-span-5 p-6 lg:p-8">
                @if($latestNews->first())
                <a href="{{ route('news.show', $latestNews->first()) }}" class="group block relative rounded-xl overflow-hidden shadow-2xl border border-slate-700 bg-slate-800">
                    <div class="h-64 sm:h-72 overflow-hidden relative">
                        @if($latestNews->first()->cover_image)
                            <img src="{{ asset('storage/' . $latestNews->first()->cover_image) }}" alt="{{ $latestNews->first()->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-slate-800 flex items-center justify-center text-gray-500">
                                <i class="fa-regular fa-image text-5xl"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <span class="absolute top-3 left-3 text-xs font-semibold bg-amber-500 text-slate-950 px-2.5 py-1 rounded shadow">
                            ★ ข่าวเด่นล่าสุด
                        </span>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <span class="text-[11px] text-amber-400 font-medium block mb-1">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ optional($latestNews->first()->published_at)->format('d/m/Y') ?? $latestNews->first()->created_at->format('d/m/Y') }}
                            </span>
                            <h3 class="text-base sm:text-lg font-bold line-clamp-2 group-hover:text-amber-400 transition leading-snug">
                                {{ $latestNews->first()->title }}
                            </h3>
                        </div>
                    </div>
                </a>
                @endif
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

    <!-- 5. เครือข่ายหน่วยงานในกระบวนการยุติธรรม (Justice Network Partners) -->
    <div class="bg-slate-100/70 p-6 rounded-xl border border-slate-200/80">
        <h3 class="text-sm font-bold text-slate-700 mb-4 text-center uppercase tracking-wide">
            <i class="fa-solid fa-landmark text-amber-600 mr-1.5"></i> เครือข่ายหน่วยงานในกระบวนการยุติธรรม
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 text-center text-xs">
            <a href="https://www.lawyerscouncil.or.th" target="_blank" class="bg-white p-3 rounded-lg border hover:border-amber-400 hover:shadow-sm transition flex flex-col items-center justify-center gap-2 group">
                <i class="fa-solid fa-scale-balanced text-amber-600 text-xl group-hover:scale-110 transition"></i>
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