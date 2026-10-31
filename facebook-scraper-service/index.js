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
        version: '1.4.0',
        uptime: process.uptime()
    });
});

/**
 * แก้ไขปัญหา Short Link / Share Link (เช่น /share/p/...) ให้เป็น Canonical URL
 */
async function resolveFacebookUrl(inputUrl) {
    if (inputUrl.includes('/share/') || inputUrl.includes('fb.watch') || inputUrl.includes('fb.me')) {
        try {
            const resp = await fetch(inputUrl, { method: 'HEAD', redirect: 'manual' });
            const loc = resp.headers.get('location');
            if (loc) {
                console.log(`[SCRAPER] Resolved short link ${inputUrl} -> ${loc}`);
                return loc.split('?')[0];
            }
        } catch (e) {
            console.warn(`[SCRAPER] Failed to resolve short link: ${e.message}`);
        }
    }
    return inputUrl;
}

// Main Scrape Endpoint
app.post('/scrape', async (req, res) => {
    let { url, token } = req.body;

    if (AUTH_TOKEN && token !== AUTH_TOKEN) {
        return res.status(401).json({ success: false, message: 'Unauthorized' });
    }

    if (!url || typeof url !== 'string' || !url.includes('facebook.com')) {
        return res.status(400).json({ success: false, message: 'Invalid Facebook URL provided' });
    }

    console.log(`[SCRAPER] Received request for URL: ${url}`);
    let browser = null;

    try {
        // 1. แปลง Share Link ให้เป็น Canonical URL ตัวเต็ม
        const canonicalUrl = await resolveFacebookUrl(url);

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
                '--window-size=1280,800'
            ]
        });

        const page = await browser.newPage();
        await page.setUserAgent(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
        );
        await page.setViewport({ width: 1280, height: 800 });
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'th-TH,th;q=0.9,en-US;q=0.8,en;q=0.7'
        });

        // Set cookies if configured on Render
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

        let fullText = '';
        const allImageUrls = new Set();
        let canonicalPostId = null;

        // =========================================================================
        // ขั้้นตอนที่ 1: ดึงข้อความตัวเต็มและภาพหลักผ่าน Facebook Embed Plugin
        // ข้อดี: Facebook เรนเดอร์ข้อความตัวเต็ม 100% ไม่ติด "ดูเพิ่มเติม" และไม่มี Login Wall
        // =========================================================================
        try {
            const embedUrl = 'https://www.facebook.com/plugins/post.php?href=' + encodeURIComponent(canonicalUrl) + '&show_text=true&width=500';
            console.log(`[SCRAPER] Loading Embed Plugin for full text: ${embedUrl}`);
            await page.goto(embedUrl, { waitUntil: 'networkidle2', timeout: 25000 });

            const embedData = await page.evaluate(() => {
                const bodyText = document.body.innerText || '';
                const lines = bodyText.split('\n').map(l => l.trim()).filter(Boolean);
                const postLines = [];
                let capturing = false;

                for (const line of lines) {
                    if (capturing) {
                        // หยุดเมื่อถึงจำนวนรูป (+72), ข้อมูลเพจ, หรือปุ่มคอมเมนต์
                        if (/^\+\d+$/.test(line) || /^\d+$/.test(line) || line === 'Comment' || line === 'Share' || line === 'Local business' || line.startsWith('Recent') || line.startsWith('Related')) {
                            break;
                        }
                        postLines.push(line);
                    } else {
                        // เริ่มจับข้อความเมื่อพบบรรทัดขึ้นต้นข่าว หรือบรรทัดที่มีข้อความยาว
                        if (line.includes('วันที่') || line.includes('ประกาศ') || line.includes('เรื่อง') || (line.length > 30 && !line.includes('Facebook'))) {
                            capturing = true;
                            postLines.push(line);
                        }
                    }
                }

                // ดึงรูปภาพจาก Embed
                const imgs = [];
                document.querySelectorAll('img').forEach(img => {
                    const src = img.src || '';
                    if (src.includes('scontent') || src.includes('fbcdn')) {
                        if (img.width >= 100 && img.height >= 100) {
                            imgs.push(src);
                        }
                    }
                });

                // ดึง Canonical Post URL หรือ Post ID
                let postId = null;
                document.querySelectorAll('a').forEach(a => {
                    const href = a.href || '';
                    const match = href.match(/posts\/(\d+)/);
                    if (match) postId = match[1];
                });

                return { text: postLines.join('\n'), imgs, postId };
            });

            if (embedData.text && embedData.text.length > 0) {
                fullText = embedData.text;
            }
            if (embedData.imgs) {
                embedData.imgs.forEach(u => allImageUrls.add(u));
            }
            if (embedData.postId) {
                canonicalPostId = embedData.postId;
            }
            console.log(`[SCRAPER] Embed text extracted! Length: ${fullText.length}, Images: ${allImageUrls.size}, PostID: ${canonicalPostId}`);
        } catch (embedErr) {
            console.warn(`[SCRAPER] Embed Plugin warning: ${embedErr.message}`);
        }

        // =========================================================================
        // ขั้นตอนที่ 2: หากพบ Post ID ให้เปิดหน้าอัลบั้ม media/set/?set=pcb.<id>
        // เพื่อสแกนดึงรูปภาพเพิ่มเติมให้ได้มากที่สุด (สูงสุด 24-70+ รูป)
        // =========================================================================
        if (canonicalPostId) {
            try {
                const albumUrl = `https://www.facebook.com/media/set/?set=pcb.${canonicalPostId}`;
                console.log(`[SCRAPER] Navigating to album: ${albumUrl}...`);
                await page.goto(albumUrl, { waitUntil: 'networkidle2', timeout: 25000 });

                // เลื่อนหน้าจอเพื่อโหลดรูปภาพในอัลบั้ม
                for (let scroll = 0; scroll < 6; scroll++) {
                    const batch = await page.evaluate(() => {
                        const list = [];
                        document.querySelectorAll('img').forEach(i => {
                            const src = i.src || '';
                            if (src && (src.includes('scontent') || src.includes('fbcdn'))) {
                                if (i.width >= 120 && i.height >= 120) list.push(src);
                            }
                        });
                        window.scrollBy(0, 900);
                        return list;
                    });
                    batch.forEach(src => allImageUrls.add(src));
                    await new Promise(r => setTimeout(r, 400));
                }
                console.log(`[SCRAPER] Album scan finished! Total unique images now: ${allImageUrls.size}`);
            } catch (albumErr) {
                console.warn(`[SCRAPER] Album scan warning: ${albumErr.message}`);
            }
        }

        // =========================================================================
        // ขั้นตอนที่ 3: Fallback สำรองผ่าน m.facebook.com หาก Embed ไม่พบข้อความ
        // =========================================================================
        if (!fullText) {
            try {
                const mobileUrl = canonicalUrl.replace('www.facebook.com', 'm.facebook.com').replace('facebook.com', 'm.facebook.com');
                console.log(`[SCRAPER] Fallback to mobile: ${mobileUrl}...`);
                await page.goto(mobileUrl, { waitUntil: 'load', timeout: 30000 });
                await new Promise(r => setTimeout(r, 2000));

                // คลิก ดูเพิ่มเติม
                await page.evaluate(() => {
                    document.querySelectorAll('*').forEach(el => {
                        const cleanText = (el.innerText || '').replace(/[\s\u00A0]+/g, '').trim();
                        if (cleanText === 'ดูเพิ่มเติม' || cleanText === 'Seemore' || (el.children.length === 0 && (cleanText.includes('ดูเพิ่มเติม') || cleanText.includes('Seemore')))) {
                            const btn = el.closest('[role="button"]') || el.parentElement || el;
                            if (btn) btn.click();
                        }
                    });
                });
                await new Promise(r => setTimeout(r, 2000));

                const mobileData = await page.evaluate(() => {
                    const bodyText = document.body.innerText || '';
                    const lines = bodyText.split('\n').map(l => l.trim()).filter(Boolean);
                    const postLines = [];
                    let startIndex = -1;

                    for (let i = 0; i < lines.length; i++) {
                        const l = lines[i];
                        if (l.match(/^\d+\s*[dhmwy]/i) || l.includes('·') || l.includes('ชม.') || l.includes('วัน')) {
                            startIndex = i + 1;
                            break;
                        }
                    }

                    if (startIndex !== -1) {
                        for (let i = startIndex; i < lines.length; i++) {
                            let l = lines[i];
                            if (/^\+\d+$/.test(l) || /^\d+$/.test(l) || l.startsWith('Related') || l.startsWith('Recent')) {
                                break;
                            }
                            l = l.replace(/\s*(?:\.\.\.|…)?\s*(?:ดูเพิ่มเติม|See more|Seemore)\s*$/i, '').trim();
                            if (l && !l.includes('Log In') && !l.includes('Open app')) {
                                postLines.push(l);
                            }
                        }
                    }

                    const imgs = [];
                    document.querySelectorAll('img').forEach(img => {
                        const src = img.src || '';
                        if (src.includes('scontent') || src.includes('fbcdn')) {
                            if (img.width >= 140 && img.height >= 140) imgs.push(src);
                        }
                    });

                    return { text: postLines.join('\n'), imgs };
                });

                if (mobileData.text) fullText = mobileData.text;
                if (mobileData.imgs) mobileData.imgs.forEach(u => allImageUrls.add(u));
            } catch (mErr) {
                console.warn(`[SCRAPER] Mobile fallback error: ${mErr.message}`);
            }
        }

        const images = Array.from(allImageUrls);
        console.log(`[SCRAPER] Scrape completed successfully! Text length: ${fullText.length}, Photos: ${images.length}`);

        await browser.close();
        browser = null;

        return res.json({
            success: true,
            text: fullText,
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
