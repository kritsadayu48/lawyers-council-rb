@extends('layouts.app')

@section('title', 'คลังกฎหมายและเอกสารดาวน์โหลด - สภาทนายความจังหวัดราชบุรี')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
    <div class="border-b pb-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-amber-600"></i> คลังกฎหมาย ข้อบังคับ และแบบฟอร์ม
        </h2>
        <p class="text-sm text-gray-500 mt-1">สืบค้นเอกสาร พระราชบัญญัติ คำสั่ง และแบบฟอร์มคำขอต่างๆ</p>
    </div>

    <!-- ฟอร์มค้นหาและตัวกรอง -->
    <form method="GET" action="{{ route('documents.index') }}" class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">คำค้นหา (ชื่อเอกสาร / เลขที่)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="พิมพ์ชื่อเอกสารหรือเลขที่คำสั่ง..." 
                       class="w-full text-sm border-gray-300 rounded px-3 py-2 border focus:ring-1 focus:ring-amber-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">หมวดหมู่เอกสาร</label>
                <select name="category_id" class="w-full text-sm border-gray-300 rounded px-3 py-2 border focus:outline-none">
                    <option value="">-- ทุกหมวดหมู่ --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded text-sm transition">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> ค้นหา
                </button>
                <a href="{{ route('documents.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-3 rounded text-sm">
                    รีเซ็ต
                </a>
            </div>
        </div>
    </form>

    <!-- ตารางแสดงรายการเอกสาร -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-700 text-xs uppercase font-semibold border-b border-slate-200">
                    <th class="py-3.5 px-4 w-12 text-center">#</th>
                    <th class="py-3.5 px-4">ชื่อเอกสาร / รายละเอียด</th>
                    <th class="py-3.5 px-4 whitespace-nowrap min-w-[160px]">หมวดหมู่</th>
                    <th class="py-3.5 px-4 w-24 text-center whitespace-nowrap">ปี พ.ศ.</th>
                    <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[200px]">เอกสาร / ดาวน์โหลด</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($documents as $index => $doc)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 text-center text-gray-400 text-xs align-middle">{{ $documents->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 align-middle">
                        <div class="font-medium text-slate-800">{{ $doc->title }}</div>
                        @if($doc->document_no)
                            <span class="inline-block text-xs text-gray-500 mt-0.5">เลขที่: {{ $doc->document_no }}</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 text-xs px-2.5 py-1 rounded-md border border-amber-200 font-medium">
                            <i class="fa-regular fa-folder text-amber-600"></i>
                            {{ $doc->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center text-gray-600 font-medium align-middle whitespace-nowrap">{{ $doc->year_be ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-center align-middle whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <!-- ปุ่มเปิดดูไฟล์ PDF ในแท็บใหม่ -->
                            <a href="{{ asset('storage/' . $doc->file_path) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 px-2.5 py-1.5 rounded-md text-xs font-medium shadow-sm transition whitespace-nowrap"
                               title="เปิดดูเอกสาร">
                                <i class="fa-regular fa-eye text-slate-500"></i>
                                <span>ดูเอกสาร</span>
                            </a>

                            <!-- ปุ่มดาวน์โหลดไฟล์ลงเครื่อง -->
                            <a href="{{ route('documents.download', $doc) }}" 
                               class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-2.5 py-1.5 rounded-md text-xs font-medium shadow-sm transition whitespace-nowrap"
                               title="ดาวน์โหลดไฟล์">
                                <i class="fa-solid fa-file-arrow-down text-red-500"></i>
                                <span>ดาวน์โหลด</span>
                            </a>
                        </div>
                        <div class="text-[11px] text-gray-400 mt-1.5">
                            <i class="fa-solid fa-circle-arrow-down text-gray-300 mr-0.5"></i> ดาวน์โหลดแล้ว {{ number_format($doc->download_count) }} ครั้ง
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-circle-exclamation text-2xl mb-2 block"></i>
                        ไม่พบเอกสารตามเงื่อนไขที่ค้นหา
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $documents->links() }}
    </div>
</div>
@endsection