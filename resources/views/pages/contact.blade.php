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
                    <a href="https://maps.app.goo.gl/wJk4zX92pQdJgUeK8" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-semibold mt-2">
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
                <div class="w-10 h-10 bg-blue-100 text-[#1877F2] rounded-lg flex items-center justify-center text-xl shrink-0 shadow-sm">
                    <i class="fa-brands fa-facebook-f"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">เพจเฟซบุ๊กทางการ (Facebook Fanpage)</h4>
                    <p class="text-xs text-slate-600 mt-1">
                        <a href="https://www.facebook.com/RachaburiLawyer/?locale=th_TH" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center gap-1.5 mt-0.5 group">
                            <span>สภาทนายความจังหวัดราชบุรี</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
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

        <!-- แผนที่ Google Maps -->
        <div class="rounded-xl overflow-hidden border border-slate-200 h-72 md:h-full min-h-[300px] shadow-sm relative group">
            <iframe 
                src="https://maps.google.com/maps?q=13.5273853,99.810601+(สภาทนายความจังหวัดราชบุรี)&t=&z=17&ie=UTF8&iwloc=B&output=embed" 
                width="100%" 
                height="100%" 
                style="border:0; min-height: 300px;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</div>
@endsection