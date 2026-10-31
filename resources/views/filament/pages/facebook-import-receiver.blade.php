<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>นำเข้าข่าวสารจาก Facebook | สภาทนายความจังหวัดราชบุรี</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <!-- Loading State: เมื่อมีข้อมูลส่งมาผ่าน Bookmarklet -->
    <div id="loadingState" class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl text-center">
        <div class="relative w-16 h-16 mx-auto mb-4">
            <div class="absolute inset-0 rounded-full border-4 border-blue-500/20 animate-ping"></div>
            <div class="w-16 h-16 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
        </div>
        <h3 class="text-lg font-bold text-white mb-1">กำลังนำเข้าข่าวสารจาก Facebook...</h3>
        <p id="statusText" class="text-xs text-slate-400 mb-4">กำลังอ่านข้อมูลและดาวน์โหลดรูปภาพความละเอียดสูง</p>
        <div class="w-full bg-slate-700 rounded-full h-1.5 overflow-hidden">
            <div class="bg-blue-500 h-1.5 rounded-full animate-pulse w-3/4"></div>
        </div>
    </div>

    <!-- Fallback / Manual Import State: เมื่อเปิดหน้านี้โดยตรง -->
    <div id="manualState" class="hidden max-w-lg w-full bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-700">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">นำเข้าข่าวสารจาก Facebook ด่วน</h3>
                <p class="text-xs text-slate-400">วางข้อความที่คัดลอกจาก Facebook เพจสภาทนายความ</p>
            </div>
        </div>

        <form id="manualForm" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">ข้อความโพสต์จาก Facebook:</label>
                <textarea id="rawText" rows="6" placeholder="ก๊อปปี้ข้อความโพสต์ทั้งหมดมาวางที่นี่... (ระบบจะตัดหัวข้อ, วันที่, และเนื้อหาให้อัตโนมัติ)" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">หมวดหมู่ข่าว:</label>
                <select id="categoryId" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    @foreach(\App\Models\Category::where('type', 'news')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-between pt-2">
                <a href="/admin/news" class="text-xs text-slate-400 hover:text-white transition">กลับหน้ารายการข่าว</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-blue-600/30 transition">
                    สร้างข่าวและจัดฟอร์แมตทันที
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var hash = window.location.hash;
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (hash && hash.indexOf('#data=') === 0) {
                try {
                    var jsonStr = decodeURIComponent(hash.substring(6));
                    var data = JSON.parse(jsonStr);

                    document.getElementById('statusText').innerText = 'พบข้อความโพสต์และรูปภาพ ' + (data.images ? data.images.length : 0) + ' รูป กำลังบันทึกเข้าสู่เว็บไซต์...';

                    fetch('{{ url("/admin/news/facebook-import/process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            text: data.text || '',
                            images: data.images || [],
                            source_url: data.source_url || ''
                        })
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(res) {
                        if (res.success && res.redirect) {
                            document.getElementById('statusText').innerText = '✅ นำเข้าสำเร็จ! กำลังเปิดหน้าแก้ไขข่าว...';
                            window.location.href = res.redirect;
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + (res.message || 'ไม่สามารถนำเข้าข้อมูลได้'));
                            document.getElementById('loadingState').classList.add('hidden');
                            document.getElementById('manualState').classList.remove('hidden');
                        }
                    })
                    .catch(function(err) {
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + err.message);
                        document.getElementById('loadingState').classList.add('hidden');
                        document.getElementById('manualState').classList.remove('hidden');
                    });

                } catch(e) {
                    alert('ไม่สามารถอ่านข้อมูลจาก Bookmarklet ได้: ' + e.message);
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('manualState').classList.remove('hidden');
                }
            } else {
                // ไม่มี Hash -> แสดงหน้าแบบฟอร์มวางข้อความด่วน
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('manualState').classList.remove('hidden');
            }

            // จัดการฟอร์มแบบ Manual
            document.getElementById('manualForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var text = document.getElementById('rawText').value.trim();
                var categoryId = document.getElementById('categoryId').value;

                if (!text) {
                    alert('กรุณาวางข้อความโพสต์จาก Facebook ก่อนครับ');
                    return;
                }

                document.getElementById('manualState').classList.add('hidden');
                document.getElementById('loadingState').classList.remove('hidden');
                document.getElementById('statusText').innerText = 'กำลังจัดฟอร์แมตหัวข้อและสร้างข่าว...';

                fetch('{{ url("/admin/news/facebook-import/process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        text: text,
                        category_id: categoryId,
                        images: []
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(r) {
                    if (r.success && r.redirect) {
                        window.location.href = r.redirect;
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (r.message || 'ไม่สามารถบันทึกได้'));
                        document.getElementById('loadingState').classList.add('hidden');
                        document.getElementById('manualState').classList.remove('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>
