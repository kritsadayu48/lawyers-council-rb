const express = require('express');
const cors = require('cors');
const puppeteer = require('puppeteer');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 8080;
const AUTH_TOKEN = process.env.AUTH_TOKEN || '';
const FB_COOKIES = process.env.FB_COOKIES || ''; // Optional: c_user=...; xs=...

// Health check endpoint
app.get('/', (req, res) => {
    res.json({
        status: 'ok',
        service: 'Lawyers Council Facebook Scraper API',
        version: '1.2.0',
        uptime: process.uptime()
    });
});

/**
 * แปลง URL ของ Facebook ให้เป็นรูปแบบ m.facebook.com เพื่อหลีกเลี่ยง Login Wall ของ Desktop
 */
function toMobileUrl(url) {
    try {
        let u = new URL(url);
        u.hostname = 'm.facebook.com';
        return u.toString();
    } catch (e) {
        return url.replace('www.facebook.com', 'm.facebook.com').replace('facebook.com', 'm.facebook.com');
    }
}

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
        // Launch Puppeteer with safe Docker-compatible args (DO NOT USE --single-process or --no-zygote)
        browser = await puppeteer.launch({
            headless: true,
            executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || undefined,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--no-first-run',
                '--disable-extensions',
                '--disable-accelerated-2d-canvas',
                '--disable-background-networking',
                '--window-size=412,915'
            ]
        });

        const page = await browser.newPage();

        // Use Mobile User Agent so Facebook renders the post content without requiring login
        await page.setUserAgent(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1'
        );
        await page.setViewport({ width: 412, height: 915, isMobile: true });
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'th-TH,th;q=0.9,en-US;q=0.8,en;q=0.7'
        });

        // Set cookies if configured
        if (FB_COOKIES) {
            const cookiePairs = FB_COOKIES.split(';').map(c => c.trim()).filter(Boolean);
            const cookies = cookiePairs.map(pair => {
                const [name, ...val] = pair.split('=');
                return {
                    name: name.trim(),
                    value: val.join('=').trim(),
                    domain: '.facebook.com'
                };
            });
            if (cookies.length > 0) {
                await page.setCookie(...cookies);
            }
        }

        const isAlbumUrl = url.includes('media/set') || url.includes('/photos/') || url.includes('photos_albums');
        const targetMobileUrl = toMobileUrl(url);
        console.log(`[SCRAPER] Navigating to: ${targetMobileUrl}...`);

        await page.goto(targetMobileUrl, {
            waitUntil: 'load',
            timeout: 35000
        });

        await new Promise(r => setTimeout(r, 2000));

        // 1. คลิกปุ่ม "See more" / "ดูเพิ่มเติม" เพื่อขยายข้อความโพสต์ให้ครบถ้วน 100%
        try {
            await page.evaluate(() => {
                const links = Array.from(document.querySelectorAll('a, span, div[role="button"]'));
                for (const el of links) {
                    const txt = (el.innerText || '').trim();
                    if (txt === 'See more' || txt === 'ดูเพิ่มเติม' || txt.includes('See more') || txt.includes('ดูเพิ่มเติม')) {
                        el.click();
                        break;
                    }
                }
            });
            await new Promise(r => setTimeout(r, 1000));
        } catch (e) {
            console.warn(`[SCRAPER] See more click warning: ${e.message}`);
        }

        // 2. ดึงข้อความโพสต์ทั้งหมดอย่างแม่นยำ
        const postData = await page.evaluate(() => {
            let fullText = '';

            const textContainers = document.querySelectorAll('div[data-ad-preview="message"], div[dir="auto"]');
            for (const el of textContainers) {
                const t = (el.innerText || '').trim();
                if (t.length > fullText.length && !t.includes('Log In') && !t.includes('Open app')) {
                    fullText = t;
                }
            }

            if (!fullText) {
                const bodyText = document.body.innerText || '';
                const lines = bodyText.split('\n').map(l => l.trim()).filter(Boolean);
                const filteredLines = [];
                let started = false;

                for (const line of lines) {
                    if (line.includes('Log In') || line.includes('Open app') || line.includes('Facebook') || line.includes('สภาทนายความจังหวัดราชบุรี')) {
                        if (started) break;
                        continue;
                    }
                    if (line.match(/^\d+[dhmw]/) || line.includes('วันที่') || line.length > 25) {
                        started = true;
                    }
                    if (started) {
                        if (line.includes('Related Pages') || line.includes('Recent posts') || line.includes('Recent Posts')) {
                            break;
                        }
                        filteredLines.push(line);
                    }
                }
                fullText = filteredLines.join('\n');
            }

            return { text: fullText.trim() };
        });

        console.log(`[SCRAPER] Extracted text length: ${postData.text.length}`);

        // 3. สแกนรูปภาพเฉพาะของโพสต์นี้เท่านั้น (หยุดก่อนเริ่ม Recent posts หรือ Related Pages)
        console.log(`[SCRAPER] Scanning post images...`);
        const allImageUrls = new Set();

        const postImgs = await page.evaluate((isAlbum) => {
            const stopKeywords = ['recent posts', 'โพสต์ล่าสุด', 'related pages', 'เพจที่เกี่ยวข้อง'];
            let stopTop = 999999;

            if (!isAlbum) {
                document.querySelectorAll('h1, h2, h3, h4, span, div').forEach(el => {
                    const txt = (el.innerText || '').trim().toLowerCase();
                    if (stopKeywords.some(k => txt === k || txt.startsWith(k))) {
                        const rect = el.getBoundingClientRect();
                        const pos = rect.top + window.scrollY;
                        if (pos > 100 && pos < stopTop) {
                            stopTop = pos;
                        }
                    }
                });
            }

            const list = [];
            document.querySelectorAll('img').forEach(img => {
                const src = img.src || '';
                if (!src || (!src.includes('scontent') && !src.includes('fbcdn'))) return;
                if (src.includes('emoji') || src.includes('rsrc.php') || src.includes('p50x50') || src.includes('p100x100') || src.includes('silhouette')) return;
                const alt = (img.alt || '').toLowerCase();
                if (alt.includes('profile') || alt.includes('โปรไฟล์')) return;
                const w = img.naturalWidth || img.width || 0;
                const h = img.naturalHeight || img.height || 0;
                const rect = img.getBoundingClientRect();
                const top = rect.top + window.scrollY;

                if (w >= 140 && h >= 140 && (isAlbum || top < stopTop)) {
                    list.push(src);
                }
            });

            return list;
        }, isAlbumUrl);

        postImgs.forEach(u => allImageUrls.add(u));

        // หากเป็นอัลบั้ม ให้เลื่อนหน้าจอลงเพื่อดึงรูปภาพทั้งหมด
        if (isAlbumUrl) {
            for (let i = 0; i < 15; i++) {
                await page.evaluate(() => window.scrollBy(0, 1000));
                await new Promise(r => setTimeout(r, 400));
                const moreImgs = await page.evaluate(() => {
                    const l = [];
                    document.querySelectorAll('img').forEach(img => {
                        const src = img.src || '';
                        if (!src || (!src.includes('scontent') && !src.includes('fbcdn'))) return;
                        if (src.includes('emoji') || src.includes('rsrc.php') || src.includes('p50x50') || src.includes('p100x100') || src.includes('silhouette')) return;
                        const w = img.naturalWidth || img.width || 0;
                        const h = img.naturalHeight || img.height || 0;
                        if (w >= 140 && h >= 140) l.push(src);
                    });
                    return l;
                });
                moreImgs.forEach(u => allImageUrls.add(u));
            }
        }

        const images = Array.from(allImageUrls);
        console.log(`[SCRAPER] Scrape completed! Found ${images.length} images.`);

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
