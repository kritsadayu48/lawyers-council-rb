@extends('layouts.app')

@section('title', 'ติดต่อเรา - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
    <h2 class="text-2xl font-bold text-slate-900 border-l-4 border-amber-600 pl-3 mb-6">
        ติดต่อสภาทนายความจังหวัดราชบุรี
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- ช่องทางการติดต่อ -->
        <div class="space-y-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">สถานที่ตั้ง / สำนักงาน</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        <strong>สภาทนายความจังหวัดราชบุรี</strong><br>
                        เลขที่ 33 ถนนเสือป่า ซอย 4 ตำบลหน้าเมือง อำเภอเมืองราชบุรี จังหวัดราชบุรี 70000
                    </p>
                    <a href="https://www.google.com/maps/search/?api=1&query=13.5273853,99.810601" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-semibold mt-2">
                        <i class="fa-solid fa-diamond-turn-right"></i> เปิดนำทางบน Google Maps
                    </a>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">เบอร์โทรศัพท์ติดต่อ</h4>
                    <p class="text-xs text-slate-600 mt-1">
                        <a href="tel:0971952029" class="hover:text-amber-600">097-195-2029</a>
                    </p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">อีเมล (E-mail)</h4>
                    <p class="text-xs text-slate-600 mt-1">
                        <a href="mailto:Lawyerscouncilrb@gmail.com" class="hover:text-amber-600">Lawyerscouncilrb@gmail.com</a>
                    </p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">วันและเวลาทำการ</h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        วันจันทร์ – วันศุกร์: 08.30 – 16.30 น.<br>
                        (หยุดวันเสาร์ - อาทิตย์ และวันหยุดนักขัตฤกษ์)
                    </p>
                </div>
            </div>
        </div>

        <!-- คอลัมน์ขวา: เพจ Facebook และ แผนที่ Google Maps ตามตัวอย่าง -->
        <div class="space-y-6 flex flex-col">
            <!-- 1. Facebook Page Plugin Widget (กล่องเพจเฟซบุ๊กพร้อมปุ่ม Follow/Share ตามตัวอย่าง) -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="bg-slate-900 text-white px-4 py-2.5 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <i class="fa-brands fa-facebook text-base text-[#1877F2]"></i>
                        <span>เพจเฟซบุ๊กทางการ (Facebook Page)</span>
                    </div>
                    <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="text-[11px] bg-white/10 hover:bg-white/20 text-blue-300 px-2.5 py-0.5 rounded transition flex items-center gap-1 font-medium">
                        เปิดเพจเต็ม <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </div>
                <div class="p-3 bg-slate-50/50 flex justify-center items-center overflow-hidden">
                    <iframe 
                        src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FRachaburiLawyer&tabs=&width=500&height=130&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" 
                        width="100%" 
                        height="130" 
                        style="border:none;overflow:hidden;max-width:500px;min-height:130px;" 
                        scrolling="no" 
                        frameborder="0" 
                        allowfullscreen="true" 
                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                    </iframe>
                </div>
            </div>

            <!-- 2. แผนที่ Google Maps (ตามตัวอย่างด้านล่าง) -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden flex-grow flex flex-col">
                <div class="bg-slate-900 text-white px-4 py-2.5 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <i class="fa-solid fa-map-location-dot text-amber-400"></i>
                        <span>แผนที่ตั้งสำนักงาน (ศาลจังหวัดราชบุรี)</span>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=13.5273853,99.810601" target="_blank" rel="noopener noreferrer" class="text-[11px] bg-white/10 hover:bg-white/20 text-amber-300 px-2.5 py-0.5 rounded transition flex items-center gap-1 font-medium">
                        เปิดใน Google Maps <i class="fa-solid fa-diamond-turn-right text-[9px]"></i>
                    </a>
                </div>
                <div class="h-64 sm:h-72 md:h-full min-h-[260px] w-full relative">
                    <iframe 
                        src="https://maps.google.com/maps?q=13.5273853,99.810601+(สภาทนายความจังหวัดราชบุรี)&t=&z=17&ie=UTF8&iwloc=B&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border:0; min-height: 260px;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection