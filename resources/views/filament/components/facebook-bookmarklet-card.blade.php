@php
    $currentHost = request()->getHttpHost();
    $targetUrl = (str_contains($currentHost, 'localhost') || str_contains($currentHost, '127.0.0.1'))
        ? url('/admin/news/facebook-import')
        : 'https://' . $currentHost . '/admin/news/facebook-import';
    $bookmarkletJs = \App\Services\FacebookImportService::generateBookmarkletJs($targetUrl);
@endphp

<div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50/70 via-indigo-50/40 to-white p-5 text-slate-800 shadow-sm dark:border-blue-900/60 dark:from-slate-900 dark:via-blue-950/30 dark:to-slate-900 dark:text-slate-100 mb-2">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-[#1877F2] text-white shadow-md shadow-blue-500/20">
            <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>⚡ ปุ่มลัดดูดข่าวคลิกเดียว (Bookmarklet) — รองรับอัลบั้ม 70+ รูป</span>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                        ดึงรูปครบ 100% พร้อมเลื่อนจอออโต้
                    </span>
                </h4>
            </div>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                ขณะเปิดดูโพสต์หรืออัลบั้มรูปบน Facebook เพจสภาทนายความ ให้กดปุ่มนี้บนแถบบุ๊กมาร์ก ระบบจะเลื่อนหน้าจออัตโนมัติเพื่อสแกนรูปภาพความละเอียดสูงทั้งหมด และดาวน์โหลดเข้าสู่เว็บไซต์ให้อัตโนมัติ
            </p>
        </div>
    </div>

    <!-- กล่องลากปุ่ม Bookmarklet -->
    <div class="mt-4 rounded-xl border border-dashed border-blue-300 bg-white/80 p-4 dark:border-blue-800 dark:bg-slate-800/80">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-center sm:text-left">
                <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    👇 ลากปุ่มนี้ขึ้นไปวางที่ "แถบที่คั่นหน้า" (Bookmarks Bar) ด้านบนของเบราว์เซอร์:
                </span>
                <div class="mt-2 inline-flex items-center gap-2">
                    <!-- ปุ่มสำหรับลากไปวางบนแถบ Bookmark -->
                    <a href="{{ $bookmarkletJs }}"
                       onclick="event.preventDefault(); alert('💡 วิธีติดตั้ง: กรุณาคลิกค้างแล้วลาก (Drag & Drop) ปุ่มนี้ขึ้นไปวางบน แถบที่คั่นหน้า (Bookmarks Bar) ด้านบนของเบราว์เซอร์ Chrome / Edge ครับ');"
                       draggable="true"
                       title="ลากปุ่มนี้ไปวางบน Bookmarks bar ของเบราว์เซอร์"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 hover:from-blue-700 hover:to-indigo-700 hover:scale-105 active:scale-95 transition cursor-grab active:cursor-grabbing border border-blue-400/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <span>📌 ส่งข่าวเข้าเว็บสภา</span>
                    </a>
                    <span class="text-xs text-slate-400">⬅️ คลิกค้างแล้วลากขึ้นข้างบน</span>
                </div>
            </div>

            <!-- ปุ่มคัดลอกโค้ดสำหรับนำไปสร้าง Bookmark เอง -->
            <button type="button"
                    onclick="navigator.clipboard.writeText(`{{ $bookmarkletJs }}`); alert('✅ คัดลอกสคริปต์ Bookmarklet สำเร็จ! ท่านสามารถนำไปวางในช่อง URL เมื่อสร้าง Bookmark ใหม่ได้เลยครับ');"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 transition">
                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>คัดลอกโค้ดสคริปต์</span>
            </button>
        </div>

        <!-- คำแนะนำเคล็ดลับสำหรับอัลบั้ม 70+ รูป -->
        <div class="mt-3.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
            <span class="font-bold">💡 เคล็ดลับสำหรับโพสต์ที่มีรูป 70+ รูป (หรือมีปุ่ม +XX):</span>
            <br>
            เนื่องจาก Facebook ซ่อนรูปภาพส่วนใหญ่ไว้ จะแสดงแค่ 4-5 รูปแรกในหน้าฟีด แนะนำให้ <strong>คลิกที่รูปภาพหรือปุ่ม '+XX' บน Facebook เพื่อเปิดดู 'อัลบั้มเต็ม'</strong> ก่อน แล้วจึงกดปุ่ม Bookmarklet ระบบจะเลื่อนหน้าจออัตโนมัติ (Auto-scroll) เพื่อดึงรูปจริงความละเอียดสูงทั้งหมดมาให้ครบ 70+ รูปทันทีครับ!
        </div>

        <!-- ขั้นตอน 3 ขั้นตอนง่ายๆ -->
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-700 text-xs">
            <div class="flex items-start gap-2">
                <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 text-[11px]">1</span>
                <span class="text-slate-600 dark:text-slate-400">เปิดดูโพสต์หรือคลิกเปิดอัลบั้มรูปบน Facebook</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 text-[11px]">2</span>
                <span class="text-slate-600 dark:text-slate-400">กดปุ่ม <strong>"📌 ส่งข่าวเข้าเว็บสภา"</strong> บนแถบบุ๊กมาร์ก (รอจอเลื่อน 3 วิ)</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 text-[11px]">3</span>
                <span class="text-slate-600 dark:text-slate-400">ระบบดาวน์โหลดรูปและเนื้อหาครบทุกรูป ตรวจทานแล้วกดบันทึก!</span>
            </div>
        </div>
    </div>
</div>
