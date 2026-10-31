# Base Image: Node.js 20 on Debian Slim
FROM node:20-slim

# ติดตั้ง Google Chrome และฟอนต์ภาษาไทย (fonts-thai-tlwg) สำหรับเรนเดอร์ภาษาไทย
RUN apt-get update \
    && apt-get install -y wget gnupg ca-certificates procps libxss1 \
    && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | gpg --dearmor -o /usr/share/keyrings/googlechrome-linux-keyring.gpg \
    && sh -c 'echo "deb [arch=amd64 signed-by=/usr/share/keyrings/googlechrome-linux-keyring.gpg] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
    && apt-get update \
    && apt-get install -y google-chrome-stable fonts-thai-tlwg fonts-freefont-ttf \
       --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# ตั้งค่าไดเรกทอรีการทำงาน
WORKDIR /app

# คัดลอก package.json และติดตั้งเฉพาะ production dependencies
COPY package*.json ./
RUN npm install --omit=dev

# คัดลอกโค้ดทั้งหมด
COPY . .

# ตั้งค่า Environment Variables สำหรับ Puppeteer บน Google Cloud Run
ENV PORT=8080
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome-stable

# พอร์ตสำหรับ Google Cloud Run
EXPOSE 8080

# คำสั่งรัน Server
CMD ["node", "index.js"]
