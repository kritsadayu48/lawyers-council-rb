@extends('layouts.app')

@section('title', 'เกี่ยวกับองค์กร - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="space-y-8">
    <!-- กล่องหัวข้อ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-slate-900 border-l-4 border-amber-600 pl-3 mb-4">
            ประวัติความเป็นมาและบทบาทหน้าที่
        </h2>
        <p class="text-sm md:text-base text-slate-700 leading-relaxed mb-4">
            สภาทนายความจังหวัดราชบุรี ทำหน้าที่เป็นศูนย์กลางในการส่งเสริม พัฒนา และกำกับดูแลการประกอบวิชาชีพทนายความในเขตพื้นที่จังหวัดราชบุรี ตลอดจนให้ความช่วยเหลือทางกฎหมายแก่ประชาชนผู้ยากไร้หรือไม่ได้รับความเป็นธรรม เพื่อผดุงความยุติธรรมและสร้างความเชื่อมั่นในกระบวนการยุติธรรมของสังคม
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-amber-600 text-xl mb-2"><i class="fa-solid fa-scale-balanced"></i></div>
                <h4 class="font-bold text-slate-800 text-sm mb-1">ช่วยเหลือประชาชน</h4>
                <p class="text-xs text-slate-600">ให้คำปรึกษาและจัดหาทนายความอาสาว่าความแก่ผู้ยากไร้</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-amber-600 text-xl mb-2"><i class="fa-solid fa-graduation-cap"></i></div>
                <h4 class="font-bold text-slate-800 text-sm mb-1">พัฒนาวิชาชีพ</h4>
                <p class="text-xs text-slate-600">จัดการอบรมและเสริมสร้างความรู้ทางกฎหมายแก่สมาชิกทนายความ</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div class="text-amber-600 text-xl mb-2"><i class="fa-solid fa-shield-halved"></i></div>
                <h4 class="font-bold text-slate-800 text-sm mb-1">มรรยาททนายความ</h4>
                <p class="text-xs text-slate-600">กำกับดูแลความประพฤติและจริยธรรมในการประกอบวิชาชีพ</p>
            </div>
        </div>
    </div>

    <!-- โครงสร้างคณะกรรมการบริหาร -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h3 class="text-xl font-bold text-slate-900 border-l-4 border-slate-800 pl-3 mb-6">
            คณะกรรมการสภาทนายความจังหวัดราชบุรี
        </h3>

        <!-- ประธานสภาทนายความ -->
        <div class="max-w-xs mx-auto text-center mb-10">
            <div class="w-32 h-32 mx-auto bg-slate-100 rounded-full border-4 border-amber-500 overflow-hidden flex items-center justify-center text-slate-400 text-4xl shadow">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-base mt-3">คุณมนตรี อิ่มจิตร</h4>
            <p class="text-xs text-amber-700 font-semibold mt-0.5">ประธานสภาทนายความจังหวัดราชบุรี</p>
        </div>

        <!-- รายชื่อกรรมการตัวอย่าง (ลูกค้าสามารถให้รายชื่อมาใส่เพิ่มได้) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="p-4 rounded-lg bg-slate-50 border">
                <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-slate-400 text-xl mb-2 shadow-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-xs font-bold text-slate-800">รองประธาน</div>
                <div class="text-[11px] text-slate-500">สภาทนายความจังหวัดราชบุรี</div>
            </div>
            <div class="p-4 rounded-lg bg-slate-50 border">
                <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-slate-400 text-xl mb-2 shadow-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-xs font-bold text-slate-800">เลขานุการ</div>
                <div class="text-[11px] text-slate-500">สภาทนายความจังหวัดราชบุรี</div>
            </div>
            <div class="p-4 rounded-lg bg-slate-50 border">
                <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-slate-400 text-xl mb-2 shadow-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-xs font-bold text-slate-800">เหรัญญิก</div>
                <div class="text-[11px] text-slate-500">สภาทนายความจังหวัดราชบุรี</div>
            </div>
            <div class="p-4 rounded-lg bg-slate-50 border">
                <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-slate-400 text-xl mb-2 shadow-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-xs font-bold text-slate-800">กรรมการฝ่ายวิชาการ</div>
                <div class="text-[11px] text-slate-500">สภาทนายความจังหวัดราชบุรี</div>
            </div>
        </div>
    </div>
</div>
@endsection