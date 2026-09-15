#!/bin/bash

# สคริปต์สำหรับแพ็กไฟล์สำหรับ Deploy ขึ้น cPanel
echo "📦 กำลังสร้างไฟล์ ratchaburi_lawyers_deploy.zip ..."

# ลบไฟล์ zip เดิมออกก่อน (ถ้ามี)
rm -f ratchaburi_lawyers_deploy.zip

# สร้างไฟล์ zip ใหม่โดยตัดไฟล์ที่ไม่จำเป็นออก
zip -r ratchaburi_lawyers_deploy.zip . \
    -x "*.git*" \
    -x "*node_modules*" \
    -x "*tests*" \
    -x "*.phpunit*" \
    -x "*ratchaburi_lawyers_deploy.zip*" \
    -x "*storage/logs/*.log" \
    -x "*storage/framework/views/*.php" \
    -x "*.DS_Store"

echo "✅ สร้างไฟล์ ratchaburi_lawyers_deploy.zip สำเร็จเรียบร้อย!"
ls -lh ratchaburi_lawyers_deploy.zip
