# 🚀 Facebook Scraper Microservice สำหรับ Google Cloud Run

บริการจำลองเบราว์เซอร์อัตโนมัติ (Headless Chrome + Puppeteer) สำหรับดึงข้อความและรูปภาพทั้งหมด (รวมอัลบั้ม 70+ รูป) จากโพสต์ Facebook เพื่อส่งให้เว็บไซต์สภาทนายความ

---

## 📋 วิธี Deploy ขึ้น Google Cloud Run (ฟรี 100%)

### วิธีที่ 1: ผ่านคำสั่ง gcloud CLI (เร็วที่สุด บรรทัดเดียวจบ)
เปิด Terminal เข้ามาที่โฟลเดอร์นี้ แล้วรันคำสั่ง:

```bash
cd facebook-scraper-service

gcloud run deploy facebook-scraper \
  --source . \
  --region asia-southeast1 \
  --allow-unauthenticated \
  --memory 1Gi \
  --cpu 1 \
  --timeout 120s
```

*หมายเหตุ: เมื่อสั่งรัน Google Cloud จะทำการ Build Image และสร้าง URL ให้ทันที*

---

### วิธีที่ 2: ผ่านหน้าเว็บ Google Cloud Console (GUI คลิกอย่างเดียว)
1. เข้าไปที่ [Google Cloud Console](https://console.cloud.google.com/)
2. ค้นหาเมนู **Cloud Run** แล้วกด **"Create Service"**
3. เลือก **"Deploy one revision from an existing container image"** หรือเชื่อมกับ **GitHub Repository**
4. ตั้งค่า:
   - **Service name:** `facebook-scraper`
   - **Region:** `asia-southeast1 (Singapore)`
   - **Authentication:** ติ๊กเลือก `Allow unauthenticated invocations`
   - **Memory:** แนะนำเลือก `1 GiB`
   - **Request timeout:** ตั้งค่า `120 seconds`
5. กดปุ่ม **"Create"**

---

## 🔗 นำ URL ไปใส่ในเว็บไซต์สภาทนายความ

เมื่อ Deploy สำเร็จ คุณจะได้ URL เช่น:
`https://facebook-scraper-xyz-as.a.run.app`

นำไปใส่ในไฟล์ `.env` ของเว็บไซต์ (บน cPanel):

```env
FACEBOOK_SCRAPER_URL=https://facebook-scraper-xyz-as.a.run.app
```

จากนั้นเมื่อแอดมิน **วางลิงก์โพสต์ Facebook** ในหน้าเว็บสภาทนายความ:
ระบบจะเรียก Scraper ตัวนี้ไปเปิดเบราว์เซอร์ดูดรูป 70+ รูปและข้อความทั้งหมดลงเว็บให้อัตโนมัติทันที!

