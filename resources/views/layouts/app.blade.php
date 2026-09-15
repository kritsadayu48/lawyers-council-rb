<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'สภาทนายความจังหวัดราชบุรี')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-4">
                <span><i class="fa-solid fa-phone mr-1 text-amber-500"></i> 097-195-2029</span>
                <span><i class="fa-solid fa-envelope mr-1 text-amber-500"></i> Lawyerscouncilrb@gmail.com</span>
            </div>
            <div>
                <a href="/admin" class="hover:text-amber-400"><i class="fa-solid fa-lock mr-1"></i> สำหรับเจ้าหน้าที่ (เข้าสู่ระบบ)</a>
            </div>
        </div>
    </div>

    <!-- Header & Logo -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-amber-600 rounded-full flex items-center justify-center text-white text-2xl shadow">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">สภาทนายความจังหวัดราชบุรี</h1>
                    <p class="text-xs text-slate-500 font-medium tracking-wide">RATCHABURI LAWYERS COUNCIL</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs text-slate-500">ยึดมั่นในความยุติธรรม ปกป้องสิทธิและเสรีภาพของประชาชน</p>
            </div>
        </div>

        <!-- Navbar Menu -->
        <nav class="bg-slate-800 text-white shadow-inner">
            <div class="max-w-7xl mx-auto px-4 flex items-center space-x-1 sm:space-x-2 text-sm font-medium overflow-x-auto whitespace-nowrap">
                <a href="{{ route('home') }}" class="py-3 px-3 transition {{ request()->routeIs('home') ? 'bg-amber-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-house mr-1"></i> หน้าแรก</a>
                <a href="{{ route('about') }}" class="py-3 px-3 transition {{ request()->routeIs('about') ? 'bg-amber-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-users mr-1"></i> เกี่ยวกับองค์กร/โครงสร้าง</a>
                <a href="{{ route('news.index') }}" class="py-3 px-3 transition {{ request()->routeIs('news.*') ? 'bg-amber-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-newspaper mr-1"></i> ข่าวสารและกิจกรรม</a>
                <a href="{{ route('documents.index') }}" class="py-3 px-3 transition {{ request()->routeIs('documents.*') ? 'bg-amber-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-book mr-1"></i> คลังกฎหมายและแบบฟอร์ม</a>
                <a href="{{ route('contact') }}" class="py-3 px-3 transition {{ request()->routeIs('contact') ? 'bg-amber-600 text-white' : 'text-slate-200 hover:bg-slate-700 hover:text-white' }}"><i class="fa-solid fa-address-book mr-1"></i> ติดต่อเรา</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-gray-400 text-sm py-8 border-t-4 border-amber-600">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
            <div>
                <h3 class="text-white font-bold mb-3 text-base">สภาทนายความจังหวัดราชบุรี</h3>
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
                    โทร: 097-195-2029<br>
                    อีเมล: Lawyerscouncilrb@gmail.com
                </p>
            </div>
        </div>
        <div class="text-center text-xs border-t border-slate-800 pt-4 text-gray-500">
            © {{ date('Y') + 543 }} สภาทนายความจังหวัดราชบุรี. All Rights Reserved.
        </div>
    </footer>

</body>
</html>