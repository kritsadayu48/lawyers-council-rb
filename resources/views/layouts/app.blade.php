<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'สภาทนายความจังหวัดราชบุรี')</title>
    <meta name="description" content="@yield('meta_description', 'เว็บไซต์อย่างเป็นทางการ สภาทนายความจังหวัดราชบุรี ให้ความช่วยเหลือประชาชนทางกฎหมาย คลังเอกสารกฎหมาย และข่าวสารกิจกรรม')">
    <meta name="keywords" content="สภาทนายความจังหวัดราชบุรี, สภาทนายความ, ปรึกษากฎหมายราชบุรี, ทนายความราชบุรี, คลังเอกสารกฎหมาย, ขอความช่วยเหลือทางกฎหมาย">
    
    <!-- Open Graph / Social Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'สภาทนายความจังหวัดราชบุรี')">
    <meta property="og:description" content="@yield('meta_description', 'เว็บไซต์อย่างเป็นทางการ สภาทนายความจังหวัดราชบุรี ให้ความช่วยเหลือประชาชนทางกฎหมาย คลังเอกสารกฎหมาย และข่าวสารกิจกรรม')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="สภาทนายความจังหวัดราชบุรี">
    <meta property="og:locale" content="th_TH">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">

    <!-- Robots / Crawlers -->
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="สภาทนายความจังหวัดราชบุรี">

    <!-- Canonical URL (รูปแบบบัญญัติสำหรับ Google Search Console) -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Schema.org JSON-LD Structured Data for Google Search Engine -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'LegalService',
        'name' => 'สภาทนายความจังหวัดราชบุรี',
        'alternateName' => [
            'สภาทนายความ ในพระบรมราชูปถัมภ์ จังหวัดราชบุรี',
            'Ratchaburi Lawyers Council',
        ],
        'url' => 'https://ratchaburilawyerscouncil.or.th',
        'logo' => 'https://ratchaburilawyerscouncil.or.th/images/logo.png',
        'image' => 'https://ratchaburilawyerscouncil.or.th/images/logo.png',
        'description' => 'เว็บไซต์อย่างเป็นทางการ สภาทนายความจังหวัดราชบุรี ศูนย์รวมข้อมูลข่าวสาร คลังเอกสารกฎหมาย และการให้ความช่วยเหลือประชาชนทางกฎหมาย',
        'telephone' => '+66-97-195-2029',
        'email' => 'Lawyerscouncilrb@gmail.com',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '33 ถนนเสือป่า ซอย 4',
            'addressLocality' => 'ตำบลหน้าเมือง',
            'addressRegion' => 'จังหวัดราชบุรี',
            'postalCode' => '70000',
            'addressCountry' => 'TH',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 13.5273853,
            'longitude' => 99.810601,
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens' => '08:30',
            'closes' => '16:30',
        ],
        'sameAs' => [
            'https://www.facebook.com/RachaburiLawyer/?locale=th_TH',
            'https://www.lawyerscouncil.or.th',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Favicon ตราสัญลักษณ์สภาทนายความ (พร้อม Cache-Busting) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- Compiled Production Assets (CSS & JS) -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback to Tailwind CDN if build not present -->
        <script>
            window.tailwind = window.tailwind || {};
            window.tailwind.config = { corePlugins: { preflight: true } };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Google Font: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Topbar แถบข้อมูลติดต่อด้านบนสุด -->
    <div class="bg-slate-900 text-gray-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-center sm:justify-start items-center gap-x-5 gap-y-1 text-center sm:text-left">
            <a href="tel:0971952029" class="hover:text-amber-400 transition flex items-center">
                <i class="fa-solid fa-phone mr-1.5 text-amber-500"></i> 097-195-2029
            </a>
            <a href="mailto:Lawyerscouncilrb@gmail.com" class="hover:text-amber-400 transition flex items-center">
                <i class="fa-solid fa-envelope mr-1.5 text-amber-500"></i> Lawyerscouncilrb@gmail.com
            </a>
            <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition flex items-center text-blue-300">
                <i class="fa-brands fa-facebook mr-1.5 text-[#1877F2] text-sm"></i> Facebook สภาทนายความ
            </a>
            <a href="{{ $youtubeChannelUrl ?? 'https://www.youtube.com/@lawyerscouncilrb' }}" target="_blank" rel="noopener noreferrer" class="hover:text-red-400 transition flex items-center text-red-300" title="ช่อง YouTube สภาทนายความจังหวัดราชบุรี">
                <i class="fa-brands fa-youtube mr-1.5 text-[#FF0000] text-sm"></i> YouTube สภาทนายความ
            </a>
        </div>
    </div>

    <!-- Header & Logo -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 sm:py-4 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 sm:space-x-4 group min-w-0">
                <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-full overflow-hidden bg-white shadow-md border border-slate-200 group-hover:shadow-lg transition shrink-0 p-0.5">
                    <img src="{{ asset('images/logo.png') }}" alt="ตราสัญลักษณ์สภาทนายความในพระบรมราชูปถัมภ์" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <h1 class="text-base sm:text-xl font-bold text-slate-900 leading-tight truncate sm:whitespace-normal">สภาทนายความจังหวัดราชบุรี</h1>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium tracking-wide truncate sm:whitespace-normal">RATCHABURI LAWYERS COUNCIL</p>
                </div>
            </a>
            
            <div class="text-right hidden md:block shrink-0">
                <p class="text-xs text-slate-500">ยึดมั่นในความยุติธรรม ปกป้องสิทธิและเสรีภาพของประชาชน</p>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" type="button" class="md:hidden p-2 text-slate-700 hover:text-amber-600 text-xl focus:outline-none rounded-lg hover:bg-slate-100 transition shrink-0" aria-label="Toggle navigation">
                <i id="mobileMenuIcon" class="fa-solid fa-bars transition-transform duration-200"></i>
            </button>
        </div>

        <!-- Desktop Navbar Menu (Visible on Desktop Only) -->
        <nav class="hidden md:block bg-slate-800 text-white shadow-inner">
            <div class="max-w-7xl mx-auto px-4 flex items-center space-x-1 sm:space-x-2 text-sm font-medium">
                <a href="{{ route('home') }}" class="py-3 px-3 transition {{ request()->routeIs('home') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-house mr-1"></i> หน้าแรก</a>
                <!-- เมนูแบบ Dropdown เมื่อนำเมาส์ไปชี้ "เกี่ยวกับองค์กร" -->
                <div class="relative group">
                    <a href="{{ route('about') }}" class="py-3 px-3 transition inline-flex items-center gap-1.5 {{ request()->routeIs('about') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}">
                        <i class="fa-solid fa-users mr-1"></i>
                        <span>เกี่ยวกับองค์กร</span>
                        <i class="fa-solid fa-chevron-down text-[10px] opacity-70 group-hover:rotate-180 transition-transform duration-200"></i>
                    </a>
                    <!-- เมนูย่อยที่แสดงเมื่อ Hover (Dropdown) -->
                    <div class="absolute left-0 top-full pt-1.5 w-64 hidden group-hover:block z-50 drop-shadow-xl">
                        <div class="bg-white rounded-xl shadow-2xl border border-slate-200/90 py-1.5 overflow-hidden text-slate-800">
                            <a href="{{ route('about') }}?tab=presidents#tab-presidents" class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold hover:bg-amber-50 hover:text-amber-700 transition">
                                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-award"></i>
                                </span>
                                <div>
                                    <span class="block">ธรรมเนียบประธาน</span>
                                    <span class="block text-[10px] text-slate-400 font-normal">อดีตประธานสภาฯ ถึงปัจจุบัน</span>
                                </div>
                            </a>
                            <div class="border-t border-slate-100 my-0.5"></div>
                            <a href="{{ route('about') }}?tab=committee#tab-committee" class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold hover:bg-amber-50 hover:text-amber-700 transition">
                                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-users"></i>
                                </span>
                                <div>
                                    <span class="block">คณะกรรมการสภา</span>
                                    <span class="block text-[10px] text-slate-400 font-normal">คณะกรรมการชุดปัจจุบัน (15 ท่าน)</span>
                                </div>
                            </a>
                            <div class="border-t border-slate-100 my-0.5"></div>
                            <a href="{{ route('about') }}?tab=lawyers#tab-lawyers" class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold hover:bg-amber-50 hover:text-amber-700 transition">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-id-badge"></i>
                                </span>
                                <div>
                                    <span class="block">ทนายความจังหวัดราชบุรี</span>
                                    <span class="block text-[10px] text-slate-400 font-normal">ทำเนียบรายชื่อทนายความ</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('news.index') }}" class="py-3 px-3 transition {{ request()->routeIs('news.*') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-newspaper mr-1"></i> ข่าวสารและกิจกรรม</a>
                <a href="{{ route('documents.index') }}" class="py-3 px-3 transition {{ request()->routeIs('documents.*') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-book mr-1"></i> คลังกฎหมายและแบบฟอร์ม</a>
                <a href="{{ route('contact') }}" class="py-3 px-3 transition {{ request()->routeIs('contact') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-address-book mr-1"></i> ติดต่อเรา</a>
            </div>
        </nav>

        <!-- Mobile Navigation Menu (Dropdown on Mobile Only) -->
        <div id="mobileMenu" class="hidden md:hidden bg-slate-900 border-t border-slate-800 text-white px-4 py-3 space-y-1.5 shadow-2xl">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('home') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-house w-5 text-center text-amber-400"></i>
                <span>หน้าแรก</span>
            </a>
            <div class="space-y-1">
                <a href="{{ route('about') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('about') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-users w-5 text-center text-amber-400"></i>
                        <span>เกี่ยวกับองค์กร</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs opacity-60"></i>
                </a>
                <div class="pl-8 pr-2 py-1 space-y-1 text-xs">
                    <a href="{{ route('about') }}?tab=presidents#tab-presidents" class="flex items-center gap-2 py-1.5 px-3 rounded-md text-slate-300 hover:text-amber-400 hover:bg-slate-800/80 transition">
                        <i class="fa-solid fa-award text-amber-400 text-[11px] w-4 text-center"></i>
                        <span>ธรรมเนียบประธาน</span>
                    </a>
                    <a href="{{ route('about') }}?tab=committee#tab-committee" class="flex items-center gap-2 py-1.5 px-3 rounded-md text-slate-300 hover:text-amber-400 hover:bg-slate-800/80 transition">
                        <i class="fa-solid fa-users text-amber-400 text-[11px] w-4 text-center"></i>
                        <span>คณะกรรมการสภา</span>
                    </a>
                    <a href="{{ route('about') }}?tab=lawyers#tab-lawyers" class="flex items-center gap-2 py-1.5 px-3 rounded-md text-slate-300 hover:text-amber-400 hover:bg-slate-800/80 transition">
                        <i class="fa-solid fa-id-badge text-blue-400 text-[11px] w-4 text-center"></i>
                        <span>ทนายความจังหวัดราชบุรี</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('news.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('news.*') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-newspaper w-5 text-center text-amber-400"></i>
                <span>ข่าวสารและกิจกรรม</span>
            </a>
            <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('documents.*') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-book w-5 text-center text-amber-400"></i>
                <span>คลังกฎหมายและแบบฟอร์ม</span>
            </a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('contact') ? 'bg-amber-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-address-book w-5 text-center text-amber-400"></i>
                <span>ติดต่อเรา</span>
            </a>
            <div class="pt-3 mt-2 border-t border-slate-800 flex items-center justify-between text-xs text-gray-400 px-3">
                <a href="tel:0971952029" class="hover:text-amber-400 flex items-center gap-1.5 py-1">
                    <i class="fa-solid fa-phone text-amber-500"></i> โทร: 097-195-2029
                </a>
                <a href="mailto:Lawyerscouncilrb@gmail.com" class="hover:text-amber-400 flex items-center gap-1.5 py-1">
                    <i class="fa-solid fa-envelope text-amber-500"></i> อีเมลสภาทนายความ
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-gray-400 text-sm py-8 border-t-4 border-amber-600">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 mb-6">
            <div class="md:col-span-1">
                <div class="flex items-center space-x-3 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="ตราสัญลักษณ์สภาทนายความ" class="w-10 h-10 rounded-full bg-white p-0.5 object-contain">
                    <h3 class="text-white font-bold text-base">สภาทนายความจังหวัดราชบุรี</h3>
                </div>
                <p class="text-xs leading-relaxed text-gray-400">
                    หน่วยงานส่งเสริมวิชาชีพทนายความ ให้ความช่วยเหลือประชาชนทางกฎหมาย และผดุงความยุติธรรมในสังคม
                </p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3 text-base">เมนูด่วน</h3>
                <ul class="text-xs space-y-2">
                    <li><a href="{{ route('news.index') }}" class="hover:text-amber-400">ข่าวสารและภาพกิจกรรม</a></li>
                    <li><a href="{{ route('documents.index') }}" class="hover:text-amber-400">ค้นหาไฟล์ข้อบังคับและกฎหมาย</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-amber-400">ทำเนียบคณะกรรมการ</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-amber-400">แผนที่และช่องทางติดต่อ</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3 text-base">ช่องทางติดต่อ</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    33 ถนนเสือป่า ซอย 4 ต.หน้าเมือง อ.เมืองราชบุรี จ.ราชบุรี 70000<br>
                    โทร: <a href="tel:0971952029" class="hover:text-amber-400">097-195-2029</a><br>
                    อีเมล: <a href="mailto:Lawyerscouncilrb@gmail.com" class="hover:text-amber-400">Lawyerscouncilrb@gmail.com</a><br>
                    <div class="mt-2 flex flex-col gap-1.5">
                        <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 font-medium">
                            <i class="fa-brands fa-facebook text-base text-[#1877F2]"></i> เพจเฟซบุ๊กทางการ
                        </a>
                        <a href="{{ $youtubeChannelUrl ?? 'https://www.youtube.com/@lawyerscouncilrb' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-red-400 hover:text-red-300 font-medium" title="ช่อง YouTube สภาทนายความจังหวัดราชบุรี">
                            <i class="fa-brands fa-youtube text-base text-[#FF0000]"></i> ช่อง YouTube สภาทนายความ
                        </a>
                    </div>
                </p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3 text-base flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-simple text-amber-500"></i> สถิติการเข้าชม
                </h3>
                <div class="bg-slate-800/80 rounded-lg p-3 border border-slate-700/60 text-xs space-y-2">
                    <div class="flex justify-between items-center text-gray-300">
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar text-amber-400 text-[10px]"></i> วันนี้:</span>
                        <span class="font-bold text-white bg-slate-700/70 px-2 py-0.5 rounded">{{ number_format($visitorStats['today'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-300">
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar-days text-amber-400 text-[10px]"></i> เดือนนี้:</span>
                        <span class="font-bold text-white bg-slate-700/70 px-2 py-0.5 rounded">{{ number_format($visitorStats['this_month'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-300 pt-1.5 border-t border-slate-700">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-users text-amber-400 text-[10px]"></i> เข้าชมทั้งหมด:</span>
                        <span class="font-bold text-amber-400 text-sm">{{ number_format($visitorStats['total'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center text-xs border-t border-slate-800 pt-4 text-gray-500">
            © {{ date('Y') + 543 }} สภาทนายความจังหวัดราชบุรี. All Rights Reserved.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('mobileMenuBtn');
            var menu = document.getElementById('mobileMenu');
            var icon = document.getElementById('mobileMenuIcon');
            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                    if (icon) {
                        if (menu.classList.contains('hidden')) {
                            icon.classList.remove('fa-xmark');
                            icon.classList.add('fa-bars');
                        } else {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-xmark');
                        }
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>