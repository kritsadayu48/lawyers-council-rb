const express = require('express');
const cors = require('cors');
const puppeteer = require('puppeteer');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 8080;
const AUTH_TOKEN = process.env.AUTH_TOKEN || ''; // Optional secret token

// Health check endpoint
app.get('/', (req, res) => {
    res.json({
        status: 'ok',
        service: 'Lawyers Council Facebook Scraper API',
        version: '1.0.0',
        uptime: process.uptime()
    });
});

// Main Scrape Endpoint
app.post('/scrape', async (req, res) => {
    const { url, token } = req.body;

    if (AUTH_TOKEN && token !== AUTH_TOKEN) {
        return res.status(401).json({ success: false, message: 'Unauthorized' });
    }

    if (!url || typeof url !== 'string' || !url.includes('facebook.com')) {
        return res.status(400).json({ success: false, message: 'Invalid Facebook URL provided' });
    }

    console.log(`[SCRAPER] Received request for URL: ${url}`);
    let browser = null;

    try {
        browser = await puppeteer.launch({
            headless: 'new',
            executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || undefined,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--no-first-run',
                '--no-zygote',
                '--single-process',
                '--disable-extensions',
                '--window-size=1366,768'
            ]
        });

        const page = await browser.newPage();

        // Realistic Desktop Chrome Headers
        await page.setUserAgent(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
        );
        await page.setViewport({ width: 1366, height: 768 });
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'th-TH,th;q=0.9,en-US;q=0.8,en;q=0.7'
        });

        console.log(`[SCRAPER] Navigating to page...`);
        await page.goto(url, {
            waitUntil: 'networkidle2',
            timeout: 45000
        });

        // 1. ปิดหน้าต่าง Popup ล็อกอิน หรือคุกกี้ที่ Facebook มักจะแสดงกวน
        try {
            await page.evaluate(() => {
                const closeSelectors = [
                    'div[aria-label="Close"]',
                    'div[aria-label="ปิด"]',
                    'div[role="button"][tabindex="0"]',
                    'i[data-visualcompletion="css-img"]'
                ];
                closeSelectors.forEach(sel => {
                    document.querySelectorAll(sel).forEach(el => {
                        const txt = (el.innerText || el.getAttribute('aria-label') || '').toLowerCase();
                        if (txt.includes('close') || txt.includes('ปิด') || txt.includes('not now') || txt.includes('ไม่ใช่ตอนนี้')) {
                            el.click();
                        }
                    });
                });
            });
        } catch (e) {}

        // 2. คลิกปุ่ม "ดูเพิ่มเติม" / "See more" เพื่อขยายข้อความโพสต์ตัวเต็ม
        try {
            await page.evaluate(() => {
                const buttons = document.querySelectorAll('div[role="button"]');
                for (let i = 0; i < buttons.length; i++) {
                    const txt = buttons[i].innerText || '';
                    if (txt.includes('ดูเพิ่มเติม') || txt.includes('See more')) {
                        buttons[i].click();
                    }
                }
            });
            await new Promise(r => setTimeout(r, 600));
        } catch (e) {}

        // 3. ดึงข้อความโพสต์
        const postData = await page.evaluate(() => {
            let text = '';
            const msgEl = document.querySelector('div[data-ad-preview="message"]') ||
                          document.querySelector('div[dir="auto"]') ||
                          document.querySelector('div[role="article"]');
            if (msgEl) {
                text = msgEl.innerText.trim();
            }
            if (!text) {
                const article = document.querySelector('div[role="article"]') || document.body;
                text = article ? article.innerText.trim() : '';
            }

            // ค้นหาลิงก์อัลบั้ม หรือปุ่ม +XX
            let albumHref = null;
            const links = Array.from(document.querySelectorAll('a'));
            for (const a of links) {
                const h = a.href || '';
                const txt = a.innerText || '';
                if (/\+\d+/.test(txt) || h.includes('media/set') || h.includes('/photos/')) {
                    albumHref = h;
                    break;
                }
            }

            return { text, albumHref };
        });

        console.log(`[SCRAPER] Extracted text length: ${postData.text.length}`);

        // 4. หากพบลิงก์ไปยังอัลบั้มเต็ม ให้เปิดเข้าไปเพื่อดูดรูป 70+ รูป
        if (postData.albumHref && (postData.albumHref.includes('media/set') || postData.albumHref.includes('photos'))) {
            console.log(`[SCRAPER] Found album link: ${postData.albumHref}, navigating...`);
            try {
                await page.goto(postData.albumHref, { waitUntil: 'networkidle2', timeout: 35000 });
                await new Promise(r => setTimeout(r, 1000));
            } catch (e) {
                console.warn(`[SCRAPER] Failed to navigate to album, staying on post page: ${e.message}`);
            }
        }

        // 5. เลื่อนหน้าจออัตโนมัติ (Auto-scroll) เพื่อโหลดรูปภาพทั้งหมด 70+ รูปจาก Virtual Scroll
        console.log(`[SCRAPER] Auto-scrolling to load all photos...`);
        const allImageUrls = new Set();
        let prevCount = 0;
        let stableTicks = 0;

        for (let i = 0; i < 28; i++) {
            const currentImgs = await page.evaluate(() => {
                const list = [];
                document.querySelectorAll('img').forEach(img => {
                    const src = img.src || '';
                    if (!src || (!src.includes('scontent') && !src.includes('fbcdn'))) return;
                    if (src.includes('emoji') || src.includes('rsrc.php') || src.includes('p50x50') || src.includes('p100x100') || src.includes('p160x160') || src.includes('silhouette')) return;
                    const alt = (img.alt || '').toLowerCase();
                    if (alt.includes('profile') || alt.includes('โปรไฟล์')) return;
                    const w = img.naturalWidth || img.width || 0;
                    const h = img.naturalHeight || img.height || 0;
                    if (w >= 160 && h >= 140) {
                        list.push(src);
                    }
                });
                return list;
            });

            currentImgs.forEach(u => allImageUrls.add(u));

            if (allImageUrls.size === prevCount) {
                stableTicks++;
                if (stableTicks >= 4) break; // ไม่มีรูปใหม่เพิ่มขึ้นแล้ว
            } else {
                stableTicks = 0;
                prevCount = allImageUrls.size;
            }

            await page.evaluate(() => window.scrollBy(0, 950));
            await new Promise(r => setTimeout(r, 350));
        }

        const images = Array.from(allImageUrls);
        console.log(`[SCRAPER] Scrape completed! Found ${images.length} high-res photos.`);

        await browser.close();
        browser = null;

        return res.json({
            success: true,
            text: postData.text,
            images: images,
            count: images.length,
            url: url
        });

    } catch (err) {
        console.error(`[SCRAPER ERROR]: ${err.message}`);
        if (browser) {
            try { await browser.close(); } catch (e) {}
        }
        return res.status(500).json({
            success: false,
            message: 'Scraping failed: ' + err.message
        });
    }
});

app.listen(PORT, () => {
    console.log(`🚀 Facebook Scraper Service running on port ${PORT}`);
});
