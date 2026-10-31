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
        <h3 class="text-lg font-bold text-white mb-1">กำลังนำเข้าข่าวสารและรูปภาพ...</h3>
        <p id="statusText" class="text-xs text-slate-300 mb-3">กำลังอ่านข้อมูลโพสต์...</p>
        
        <!-- แถบแสดงความคืบหน้า (Progress Bar) สำหรับรูป 70+ ภาพ -->
        <div class="w-full bg-slate-700 rounded-full h-2.5 overflow-hidden mb-2">
            <div id="progressBar" class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2.5 rounded-full transition-all duration-300" style="width: 10%;"></div>
        </div>
        <div class="flex justify-between text-[11px] text-slate-400">
            <span id="progressCount">เตรียมดาวน์โหลด...</span>
            <span id="progressPercent">10%</span>
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
                    var allImages = data.images || [];
                    var totalImages = allImages.length;

                    updateProgress(10, 'พบข้อความโพสต์และรูปภาพทั้งหมด ' + totalImages + ' รูป', 'เริ่มสร้างข่าว...');

                    // ขั้นตอนที่ 1: สร้างหัวข้อข่าว และโหลดรูปชุดแรก (รูปปก + 5 รูป)
                    fetch('{{ url("/admin/news/facebook-import/process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            text: data.text || '',
                            images: allImages,
                            source_url: data.source_url || ''
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(res) {
                        if (!res.success) {
                            throw new Error(res.message || 'ไม่สามารถสร้างข่าวได้');
                        }

                        var newsId = res.news_id;
                        var redirectUrl = res.redirect;
                        var remainingImages = res.remaining_images || [];

                        if (remainingImages.length === 0) {
                            updateProgress(100, '✅ นำเข้าสำเร็จครบถ้วน!', 'กำลังเปิดหน้าแก้ไขข่าว...');
                            setTimeout(function() { window.location.href = redirectUrl; }, 600);
                            return;
                        }

                        // ขั้นตอนที่ 2: แบ่งรูปที่เหลือดาวน์โหลดเป็น Batch ละ 8 รูป ป้องกัน Timeout สำหรับรูป 70+ รูป
                        var batchSize = 8;
                        var batches = [];
                        for (var i = 0; i < remainingImages.length; i += batchSize) {
                            batches.push(remainingImages.slice(i, i + batchSize));
                        }

                        var processedImages = 5;
                        var currentBatchIndex = 0;

                        function processNextBatch() {
                            if (currentBatchIndex >= batches.length) {
                                updateProgress(100, '✅ นำเข้าสำเร็จครบ ' + totalImages + ' ภาพ!', 'กำลังเปิดหน้าแก้ไขข่าว...');
                                setTimeout(function() { window.location.href = redirectUrl; }, 800);
                                return;
                            }

                            var chunk = batches[currentBatchIndex];
                            currentBatchIndex++;

                            fetch('{{ url("/admin/news/facebook-import/append-batch") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    news_id: newsId,
                                    images: chunk
                                })
                            })
                            .then(function(r) { return r.json(); })
                            .then(function(batchRes) {
                                processedImages += chunk.length;
                                if (processedImages > totalImages) processedImages = totalImages;

                                var pct = Math.round((processedImages / totalImages) * 90) + 10;
                                updateProgress(
                                    pct,
                                    'กำลังดาวน์โหลดรูปภาพความละเอียดสูง... (' + processedImages + ' จาก ' + totalImages + ' รูป)',
                                    processedImages + ' / ' + totalImages + ' รูป'
                                );

                                // โหลดชุดถัดไปทันที
                                processNextBatch();
                            })
                            .catch(function(err) {
                                console.warn('Batch download warning:', err);
                                // หากชุดนี้มีปัญหา ให้ข้ามไปทำชุดถัดไป ไม่ค้าง
                                processNextBatch();
                            });
                        }

                        // เริ่มกระบวนการโหลดรูปภาพที่เหลือ
                        processNextBatch();
                    })
                    .catch(function(err) {
                        alert('เกิดข้อผิดพลาด: ' + err.message);
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

            function updateProgress(percent, status, countText) {
                var bar = document.getElementById('progressBar');
                var st = document.getElementById('statusText');
                var cnt = document.getElementById('progressCount');
                var pct = document.getElementById('progressPercent');

                if (bar) bar.style.width = percent + '%';
                if (st) st.innerText = status;
                if (cnt) cnt.innerText = countText;
                if (pct) pct.innerText = percent + '%';
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
                updateProgress(50, 'กำลังจัดฟอร์แมตหัวข้อและสร้างข่าว...', 'กำลังบันทึก...');

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
