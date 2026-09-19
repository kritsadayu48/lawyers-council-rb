@extends('layouts.app')

@section('title', 'เกี่ยวกับองค์กร - สภาทนายความจังหวัดราชบุรี')
@section('meta_description', 'ทำเนียบประธาน คณะกรรมการสภาทนายความจังหวัดราชบุรีชุดปัจจุบัน พ.ศ. 2568 - 2571 และรายชื่อทนายความจังหวัดราชบุรี')

@section('content')
<div class="space-y-8">

    <!-- 1. Header Banner แนะนำองค์กร -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 sm:p-10 text-white shadow-xl border border-slate-700/60 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-amber-500 text-9xl pointer-events-none">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="max-w-3xl relative z-10">
            <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                <i class="fa-solid fa-scale-balanced"></i> สภาทนายความในพระบรมราชูปถัมภ์ จังหวัดราชบุรี
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                เกี่ยวกับ<span class="text-amber-500">องค์กร</span>
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed mt-3">
                สภาทนายความจังหวัดราชบุรี ทำหน้าที่เป็นศูนย์กลางในการส่งเสริม พัฒนา และกำกับดูแลการประกอบวิชาชีพทนายความในเขตจังหวัดราชบุรี 
                ตลอดจนให้ความช่วยเหลือประชาชนทางกฎหมาย และผดุงความยุติธรรมในสังคม
            </p>
        </div>
    </div>

    <!-- 2. Navigation Tabs สลับหมวดหมู่ -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-2 sm:p-2.5">
        <div class="flex flex-wrap gap-2" id="aboutTabs" role="tablist">
            <button type="button" class="tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 rounded-lg text-sm font-bold transition-all duration-200 bg-amber-600 text-white shadow-sm" data-target="tab-committee">
                <i class="fa-solid fa-users"></i>
                <span>คณะกรรมการชุดปัจจุบัน</span>
                <span class="text-[11px] font-semibold bg-white/20 px-2 py-0.5 rounded-full hidden sm:inline">{{ $committees->count() }} ท่าน</span>
            </button>
            <button type="button" class="tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100" data-target="tab-presidents">
                <i class="fa-solid fa-award text-amber-600"></i>
                <span>ทำเนียบประธาน</span>
                <span class="text-[11px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full hidden sm:inline">{{ $presidents->count() }} ท่าน</span>
            </button>
            <button type="button" class="tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100" data-target="tab-lawyers">
                <i class="fa-solid fa-id-badge text-blue-600"></i>
                <span>ทนายความจังหวัดราชบุรี</span>
                <span class="text-[11px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full hidden sm:inline">{{ $lawyers->count() }} ท่าน</span>
            </button>
            <button type="button" class="tab-btn flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-600 hover:text-red-600 hover:bg-red-50" data-target="tab-youtube">
                <i class="fa-brands fa-youtube text-[#FF0000]"></i>
                <span>วิดีโอและถ่ายทอดสด</span>
            </button>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: คณะกรรมการสภาทนายความจังหวัดราชบุรี ชุดปัจจุบัน -->
    <!-- ======================================================== -->
    <div id="tab-committee" class="tab-pane space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-200/80 px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="fa-solid fa-calendar-check text-amber-600"></i> วาระประจำปี พ.ศ. 2568 – 2571
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-2.5">
                    คณะกรรมการสภาทนายความจังหวัดราชบุรี
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    รายนามคณะกรรมการบริหารสภาทนายความจังหวัดราชบุรี ชุดปัจจุบันในการขับเคลื่อนและอำนวยความยุติธรรม
                </p>
            </div>

            @php
                $president = $committees->firstWhere('position', 'ประธานสภาทนายความจังหวัดราชบุรี') ?? $committees->first();
                $vicePresidents = $committees->filter(fn($c) => $c->id !== optional($president)->id && str_contains($c->position, 'รองประธาน'));
                $otherCommittees = $committees->filter(fn($c) => $c->id !== optional($president)->id && !str_contains($c->position, 'รองประธาน'));
            @endphp

            <!-- 1.1 การ์ดประธานสภาทนายความ (โดดเด่นตรงกลางด้านบน) -->
            @if($president)
            <div class="max-w-md mx-auto mb-12">
                <div class="bg-gradient-to-b from-amber-50/70 via-white to-white rounded-2xl p-6 sm:p-8 border-2 border-amber-400 shadow-lg text-center relative group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-600 text-white text-[11px] font-bold px-3.5 py-1 rounded-full shadow tracking-wider uppercase flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-amber-300 text-xs"></i> ประธานสภาทนายความ
                    </div>
                    
                    <div class="w-32 h-32 mx-auto rounded-full p-1 bg-gradient-to-tr from-amber-500 to-amber-300 shadow-md mt-2 mb-4">
                        @if($president->image_path)
                            <img src="{{ asset('storage/' . $president->image_path) }}" alt="{{ $president->name }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <div class="w-full h-full rounded-full bg-slate-900 text-amber-400 flex items-center justify-center text-4xl">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        @endif
                    </div>

                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        {{ $president->name }}
                    </h3>
                    <p class="text-xs sm:text-sm font-bold text-amber-700 mt-1">
                        {{ $president->position }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        วาระ {{ $president->term ?? 'พ.ศ. 2568 – 2571' }}
                    </p>

                    @if($president->phone)
                    <div class="mt-4 pt-3 border-t border-amber-100">
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $president->phone) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                            <i class="fa-solid fa-phone"></i> โทร: {{ $president->phone }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- 1.2 คณะรองประธานสภาทนายความ (4 ท่าน) -->
            @if($vicePresidents->count() > 0)
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-5 pb-2 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                    <h3 class="text-base font-bold text-slate-800">รองประธานสภาทนายความ</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($vicePresidents as $vp)
                    <div class="bg-slate-50/80 hover:bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col justify-between group">
                        <div>
                            <div class="w-20 h-20 mx-auto rounded-full p-0.5 bg-slate-200 group-hover:bg-amber-400 transition mb-3">
                                @if($vp->image_path)
                                    <img src="{{ asset('storage/' . $vp->image_path) }}" alt="{{ $vp->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <div class="w-full h-full rounded-full bg-white text-slate-500 flex items-center justify-center text-2xl">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-amber-700 transition">
                                {{ $vp->name }}
                            </h4>
                            <p class="text-xs text-amber-700 font-semibold mt-1">
                                {{ $vp->position }}
                            </p>
                        </div>
                        @if($vp->phone)
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60">
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $vp->phone) }}" class="inline-flex items-center gap-1.5 text-xs text-slate-600 hover:text-amber-600 font-medium transition">
                                <i class="fa-solid fa-phone text-amber-600"></i> {{ $vp->phone }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 1.3 คณะกรรมการและเลขานุการ (10 ท่าน) -->
            @if($otherCommittees->count() > 0)
            <div>
                <div class="flex items-center gap-2 mb-5 pb-2 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-700"></span>
                    <h3 class="text-base font-bold text-slate-800">คณะกรรมการและเลขานุการ</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($otherCommittees as $cm)
                    <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-slate-300 transition text-center flex flex-col justify-between group">
                        <div>
                            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xl mb-3 group-hover:bg-slate-800 group-hover:text-white transition">
                                @if($cm->image_path)
                                    <img src="{{ asset('storage/' . $cm->image_path) }}" alt="{{ $cm->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <i class="fa-solid {{ str_contains($cm->name, 'หญิง') || str_contains($cm->name, 'นาง') ? 'fa-user-nurse' : 'fa-user' }}"></i>
                                @endif
                            </div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                {{ $cm->name }}
                            </h4>
                            <p class="text-[11px] text-slate-500 font-medium mt-1">
                                {{ $cm->position }}
                            </p>
                        </div>
                        @if($cm->phone)
                        <div class="mt-3 pt-2 border-t border-slate-100">
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $cm->phone) }}" class="inline-flex items-center gap-1 text-[11px] text-slate-600 hover:text-amber-600 transition">
                                <i class="fa-solid fa-phone text-amber-500 text-[10px]"></i> {{ $cm->phone }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 2: ทำเนียบประธานสภาทนายความจังหวัดราชบุรี (อดีต - ปัจจุบัน) -->
    <!-- ======================================================== -->
    <div id="tab-presidents" class="tab-pane hidden space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-200/80 px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="fa-solid fa-award text-amber-600"></i> Hall of Fame
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-2.5">
                    ทำเนียบประธานสภาทนายความจังหวัดราชบุรี
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    รำลึกและเชิดชูเกียรติอดีตประธานสภาทนายความจังหวัดราชบุรีผู้สร้างคุณูปการแก่องค์กรและวิชาชีพกฎหมาย
                </p>
            </div>

            @if($presidents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($presidents as $p)
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center text-2xl shrink-0 shadow">
                                @if($p->image_path)
                                    <img src="{{ asset('storage/' . $p->image_path) }}" alt="{{ $p->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <i class="fa-solid fa-user-tie"></i>
                                @endif
                            </div>
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-900 mb-1">
                                    {{ $p->term ?? 'วาระ พ.ศ.' }}
                                </span>
                                <h3 class="font-extrabold text-base text-slate-900">{{ $p->name }}</h3>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $p->position }}</p>
                            </div>
                        </div>
                        @if($p->bio)
                        <p class="text-xs text-slate-600 mt-4 leading-relaxed bg-white p-3 rounded-lg border border-slate-100">
                            {{ $p->bio }}
                        </p>
                        @endif
                    </div>

                    @if($p->phone)
                    <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs">
                        <span class="text-slate-500">ติดต่อ:</span>
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $p->phone) }}" class="font-bold text-amber-700 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-phone text-amber-500"></i> {{ $p->phone }}
                        </a>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 text-slate-400 bg-slate-50 rounded-xl border border-dashed">
                <i class="fa-solid fa-award text-4xl text-slate-300 mb-2"></i>
                <p class="text-sm">กำลังรวบรวมข้อมูลทำเนียบอดีตประธานสภาทนายความจังหวัดราชบุรี</p>
            </div>
            @endif

            <div class="mt-8 p-4 rounded-xl bg-amber-50/50 border border-amber-200/60 flex items-center gap-3 text-xs text-amber-900">
                <i class="fa-solid fa-circle-info text-amber-600 text-base shrink-0"></i>
                <span>ผู้ดูแลระบบสามารถเพิ่ม แก้ไข หรือนำเข้ารายชื่ออดีตประธานสภาทนายความตั้งแต่ต้นจนถึงปัจจุบันได้ที่ระบบแอดมินหลังบ้าน</span>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 3: ทนายความจังหวัดราชบุรี (Directory & Search) -->
    <!-- ======================================================== -->
    <div id="tab-lawyers" class="tab-pane hidden space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                        ทนายความจังหวัดราชบุรี
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        รายชื่อ ข้อมูลติดต่อ และสำนักงานทนายความที่ประสงค์ให้สภาทนายความลงข้อมูลในทำเนียบ
                    </p>
                </div>

                <!-- กล่องค้นหาทนายความ Real-time -->
                <div class="relative w-full md:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="lawyerSearchInput" placeholder="พิมพ์ชื่อ, เบอร์โทร, หรือสำนักงาน..." class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                </div>
            </div>

            <!-- แสดงจำนวนผลการค้นหา -->
            <div class="pt-4 flex items-center justify-between text-xs text-slate-500">
                <div>
                    แสดงทนายความทั้งหมด <span id="lawyerCount" class="font-bold text-slate-800">{{ $lawyers->count() }}</span> ท่าน
                </div>
                <div class="text-slate-400 hidden sm:block">
                    * ทนายความที่ประสงค์จะลงข้อมูลติดต่อ สามารถติดต่อเจ้าหน้าที่สภาฯ ได้
                </div>
            </div>

            <!-- Grid รายชื่อทนายความ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4" id="lawyersGrid">
                @forelse($lawyers as $lawyer)
                <div class="lawyer-card bg-slate-50/70 hover:bg-white rounded-xl p-4 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group" data-keywords="{{ mb_strtolower($lawyer->name . ' ' . $lawyer->phone . ' ' . $lawyer->position . ' ' . $lawyer->office_name . ' ' . $lawyer->license_no) }}">
                    <div>
                        <div class="w-20 h-20 mx-auto rounded-full bg-white border-2 border-slate-200 group-hover:border-amber-500 text-slate-400 flex items-center justify-center text-2xl mb-3 overflow-hidden shadow-sm transition">
                            @if($lawyer->image_path)
                                <img src="{{ asset('storage/' . $lawyer->image_path) }}" alt="{{ $lawyer->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid {{ str_contains($lawyer->name, 'หญิง') || str_contains($lawyer->name, 'นาง') ? 'fa-user-tie' : 'fa-user-tie' }}"></i>
                            @endif
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 text-center leading-snug group-hover:text-amber-700 transition">
                            {{ $lawyer->name }}
                        </h4>
                        <div class="text-center mt-1">
                            <span class="inline-block text-[11px] font-semibold text-slate-600 bg-slate-200/70 px-2 py-0.5 rounded">
                                {{ $lawyer->position ?? 'ทนายความ' }}
                            </span>
                        </div>
                        @if($lawyer->license_no)
                        <div class="text-[11px] text-slate-500 text-center mt-1">
                            ใบอนุญาต: {{ $lawyer->license_no }}
                        </div>
                        @endif
                        @if($lawyer->office_name)
                        <div class="text-[11px] text-slate-600 text-center mt-1.5 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-building text-[10px] text-slate-400"></i>
                            <span class="truncate">{{ $lawyer->office_name }}</span>
                        </div>
                        @endif
                    </div>

                    @if($lawyer->phone)
                    <div class="mt-4 pt-2.5 border-t border-slate-200/60 text-center">
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $lawyer->phone) }}" class="inline-flex items-center justify-center gap-1.5 w-full py-1.5 rounded-lg bg-white group-hover:bg-amber-600 border border-slate-200 group-hover:border-amber-600 text-slate-700 group-hover:text-white text-xs font-semibold shadow-xs transition duration-200">
                            <i class="fa-solid fa-phone text-amber-500 group-hover:text-white"></i>
                            <span>{{ $lawyer->phone }}</span>
                        </a>
                    </div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-slate-400 bg-slate-50 rounded-xl border border-dashed">
                    <i class="fa-solid fa-id-badge text-3xl mb-2 block text-slate-300"></i>
                    ยังไม่มีข้อมูลทนายความในระบบ
                </div>
                @endforelse
            </div>

            <!-- กล่องแสดงเมื่อค้นหาไม่พบ -->
            <div id="noLawyerFound" class="hidden text-center py-12 text-slate-400 bg-slate-50 rounded-xl border border-dashed">
                <i class="fa-solid fa-magnifying-glass text-3xl mb-2 block text-slate-300"></i>
                ไม่พบรายชื่อทนายความที่ตรงกับคำค้นหา
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 4: ช่อง YouTube และวิดีโอกิจกรรมถ่ายทอดสด -->
    <!-- ======================================================== -->
    <div id="tab-youtube" class="tab-pane hidden space-y-6">
        <div class="bg-gradient-to-br from-red-950 via-slate-900 to-slate-950 rounded-2xl p-6 sm:p-10 text-white shadow-xl border border-red-900/40 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-red-500/10 text-9xl pointer-events-none">
                <i class="fa-brands fa-youtube"></i>
            </div>
            <div class="max-w-3xl relative z-10">
                <div class="inline-flex items-center gap-2 bg-red-500/20 border border-red-500/40 text-red-300 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    <i class="fa-brands fa-youtube text-red-500"></i> Official YouTube Channel
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                    ช่อง YouTube สภาทนายความจังหวัดราชบุรี
                </h2>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed mt-3">
                    ศูนย์รวมวิดีโอบันทึกกิจกรรม การอบรมสัมมนาทางวิชาชีพกฎหมาย และการถ่ายทอดสด (Live Streaming) 
                    กิจกรรมสำคัญของสภาทนายความจังหวัดราชบุรี เพื่อการเผยแพร่ความรู้และการมีส่วนร่วมของสมาชิก
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="https://www.youtube.com/@lawyerscouncilrb" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-600/30 transition transform hover:-translate-y-0.5">
                        <i class="fa-brands fa-youtube text-lg"></i>
                        <span>ติดตามช่อง YouTube ทางการ</span>
                    </a>
                    <span class="text-xs text-slate-400 bg-white/10 px-3 py-2 rounded-lg border border-white/10">
                        <i class="fa-solid fa-video text-amber-400 mr-1.5"></i> กำลังเตรียมพร้อมระบบถ่ายทอดสด
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-tower-broadcast"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">ถ่ายทอดสดกิจกรรมสภาฯ</h3>
                <p class="text-xs text-slate-500 mt-1">รับชมการถ่ายทอดสดงานพิธีสำคัญ และการประชุมสัมมนาแบบเรียลไทม์</p>
            </div>
            <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-clapperboard"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">วิดีโอสัมมนาย้อนหลัง</h3>
                <p class="text-xs text-slate-500 mt-1">คลังความรู้วิชาการ บรรยายพิเศษ และการพัฒนาทักษะวิชาชีพว่าความ</p>
            </div>
            <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">กดติดตาม & แจ้งเตือน</h3>
                <p class="text-xs text-slate-500 mt-1">ไม่พลาดทุกสาระกฎหมายและกิจกรรมสำคัญของสภาทนายความ</p>
            </div>
        </div>
    </div>

</div>

<!-- JavaScript สำหรับสลับแท็บ และระบบค้นหาทนายความ Real-time -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tab Switching Logic
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    function switchTab(targetId) {
        if (!targetId) return;
        if (!targetId.startsWith('tab-')) {
            targetId = 'tab-' + targetId;
        }

        const targetBtn = document.querySelector(`.tab-btn[data-target="${targetId}"]`);
        const targetPane = document.getElementById(targetId);

        if (targetBtn && targetPane) {
            // Reset all buttons style
            tabButtons.forEach(b => {
                b.classList.remove('bg-amber-600', 'text-white', 'shadow-sm');
                b.classList.add('text-slate-600', 'hover:bg-slate-100');
            });

            // Activate target button
            targetBtn.classList.remove('text-slate-600', 'hover:bg-slate-100');
            targetBtn.classList.add('bg-amber-600', 'text-white', 'shadow-sm');

            // Hide all panes & show target
            tabPanes.forEach(pane => pane.classList.add('hidden'));
            targetPane.classList.remove('hidden');
        }
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            switchTab(targetId);
        });
    });

    // Check URL parameters (?tab=...) or Hash (#tab-...) on page load
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const hashParam = window.location.hash ? window.location.hash.replace('#', '') : null;

    if (tabParam) {
        switchTab(tabParam);
    } else if (hashParam) {
        switchTab(hashParam);
    }

    // Support in-page hash changes (when clicking dropdown from within /about page)
    window.addEventListener('hashchange', function() {
        const currentHash = window.location.hash ? window.location.hash.replace('#', '') : null;
        if (currentHash) {
            switchTab(currentHash);
        }
    });

    // 2. Real-time Lawyer Search
    const searchInput = document.getElementById('lawyerSearchInput');
    const lawyerCards = document.querySelectorAll('.lawyer-card');
    const lawyerCountSpan = document.getElementById('lawyerCount');
    const noLawyerFound = document.getElementById('noLawyerFound');

    if (searchInput && lawyerCards.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            lawyerCards.forEach(card => {
                const keywords = card.getAttribute('data-keywords') || '';
                if (query === '' || keywords.includes(query)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (lawyerCountSpan) {
                lawyerCountSpan.textContent = visibleCount;
            }

            if (noLawyerFound) {
                if (visibleCount === 0) {
                    noLawyerFound.classList.remove('hidden');
                } else {
                    noLawyerFound.classList.add('hidden');
                }
            }
        });
    }
});
</script>
@endsection