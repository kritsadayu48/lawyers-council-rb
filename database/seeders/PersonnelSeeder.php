<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personnel;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. คณะกรรมการสภาทนายความจังหวัดราชบุรี พ.ศ. 2568 – 2571 (ข้อมูลจริงจากเอกสาร)
        $committees = [
            [
                'order_column' => 1,
                'name' => 'นายมนตรี อิ่มจิตร',
                'position' => 'ประธานสภาทนายความจังหวัดราชบุรี',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '094-8941142',
            ],
            [
                'order_column' => 2,
                'name' => 'นายอารักษ์ เลขวัฒนะโรจน์',
                'position' => 'รองประธานสภาทนายความจังหวัดราชบุรีและเหรัญญิก',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '083-9860789',
            ],
            [
                'order_column' => 3,
                'name' => 'นายมุ่งวิชฌ์ ใจมุ่ง',
                'position' => 'รองประธานสภาทนายความจังหวัดราชบุรี',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '087-1593108',
            ],
            [
                'order_column' => 4,
                'name' => 'นายนพพงษ์ สนิทรักษา',
                'position' => 'รองประธานสภาทนายความจังหวัดราชบุรี',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '061-2595365',
            ],
            [
                'order_column' => 5,
                'name' => 'นายสาธิต บำเรอจิต',
                'position' => 'รองประธานสภาทนายความจังหวัดราชบุรี',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '081-9526353',
            ],
            [
                'order_column' => 6,
                'name' => 'นายทนันชัย เรืองศรี',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '084-1558590',
            ],
            [
                'order_column' => 7,
                'name' => 'นายเถลิงศักดิ์ อรรคทิมากูล',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '098-5890555',
            ],
            [
                'order_column' => 8,
                'name' => 'นางสาวอุมาพร ขำล้วน',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '096-9244469',
            ],
            [
                'order_column' => 9,
                'name' => 'นายประกฤติ สถานสถิตย์',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '081-9109023',
            ],
            [
                'order_column' => 10,
                'name' => 'นางสาวรวีวรรณ สมส่วน',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '084-3854567',
            ],
            [
                'order_column' => 11,
                'name' => 'นายเทิดเกียรติ ชุ้นเกษา',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '084-1590180',
            ],
            [
                'order_column' => 12,
                'name' => 'นายเจนณรงค์ สวัสดิวงค์',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '083-5490926',
            ],
            [
                'order_column' => 13,
                'name' => 'นายวัฒนพงศ์ อัจฉริยศรีพงศ์',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '090-9593370',
            ],
            [
                'order_column' => 14,
                'name' => 'นายจรัญ ระงับพิศม์',
                'position' => 'กรรมการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '089-9563860',
            ],
            [
                'order_column' => 15,
                'name' => 'ว่าที่ร.ต.หญิงพนา นิลจันทร์',
                'position' => 'กรรมการและเลขานุการ',
                'term' => 'พ.ศ. 2568 – 2571',
                'phone' => '087-1580245',
            ],
        ];

        foreach ($committees as $c) {
            Personnel::updateOrCreate(
                [
                    'type' => Personnel::TYPE_COMMITTEE,
                    'name' => $c['name'],
                ],
                array_merge($c, [
                    'is_active' => true,
                ])
            );
        }

        // 2. ทำเนียบประธานสภาทนายความจังหวัดราชบุรี (อดีต - ปัจจุบัน)
        $presidents = [
            [
                'order_column' => 1,
                'name' => 'นายมนตรี อิ่มจิตร',
                'position' => 'ประธานสภาทนายความจังหวัดราชบุรี (คนปัจจุบัน)',
                'term' => 'พ.ศ. 2568 – ปัจจุบัน',
                'phone' => '094-8941142',
                'bio' => 'ประธานสภาทนายความจังหวัดราชบุรี วาระประจำปี พ.ศ. 2568 – 2571',
            ],
        ];

        foreach ($presidents as $p) {
            Personnel::updateOrCreate(
                [
                    'type' => Personnel::TYPE_PRESIDENT,
                    'name' => $p['name'],
                ],
                array_merge($p, [
                    'is_active' => true,
                ])
            );
        }

        // 3. ทนายความจังหวัดราชบุรี (ตัวอย่างเบื้องต้นจากคณะกรรมการ พร้อมให้เพิ่ม/อัปโหลดได้ถึง 100+ คน)
        foreach ($committees as $i => $lawyer) {
            Personnel::updateOrCreate(
                [
                    'type' => Personnel::TYPE_LAWYER,
                    'name' => $lawyer['name'],
                ],
                [
                    'order_column' => $i + 1,
                    'position' => 'ทนายความ',
                    'phone' => $lawyer['phone'],
                    'office_name' => 'จังหวัดราชบุรี',
                    'is_active' => true,
                ]
            );
        }
    }
}
