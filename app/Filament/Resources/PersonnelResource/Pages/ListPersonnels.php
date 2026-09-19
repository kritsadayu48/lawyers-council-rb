<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Filament\Resources\PersonnelResource;
use App\Models\Personnel;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListPersonnels extends ListRecords
{
    protected static string $resource = PersonnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('เพิ่มข้อมูลบุคลากร')
                ->icon('heroicon-o-plus'),

            Actions\Action::make('importCsv')
                ->label('นำเข้ารายชื่อจากไฟล์ (CSV)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('นำเข้ารายชื่อบุคลากร / ทนายความจากไฟล์ CSV')
                ->modalDescription('อัปโหลดไฟล์ .csv เพื่อนำเข้ารายชื่อจำนวนมาก (50 - 100+ คน) เข้าสู่ระบบพร้อมกันได้ทันที')
                ->form([
                    Forms\Components\Select::make('default_type')
                        ->label('หมวดหมู่บุคลากรเริ่มต้น (หากในไฟล์ไม่ได้ระบุ)')
                        ->options(Personnel::typeLabels())
                        ->default(Personnel::TYPE_LAWYER)
                        ->required()
                        ->helperText('กำหนดว่ารายชื่อในไฟล์จะถูกจัดเข้าหมวดหมู่ใด'),

                    Forms\Components\FileUpload::make('csv_file')
                        ->label('เลือกไฟล์ CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'text/x-csv', 'text/comma-separated-values'])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->required()
                        ->helperText('ไฟล์ควรบันทึกด้วยการเข้ารหัส UTF-8 (รองรับภาษาไทย)'),

                    Forms\Components\Placeholder::make('format_guide')
                        ->label('รูปแบบคอลัมน์ในไฟล์ CSV')
                        ->content('หัวตารางที่รองรับ (สามารถใช้ภาษาไทยหรืออังกฤษได้):
- ชื่อ หรือ name (จำเป็น)
- ตำแหน่ง หรือ position
- เบอร์โทร หรือ phone
- สำนักงาน หรือ office_name
- วาระ หรือ term
- เลขที่ใบอนุญาต หรือ license_no
- หมวดหมู่ หรือ type (current_committee, past_president, ratchaburi_lawyer)'),
                ])
                ->action(function (array $data): void {
                    $filePath = Storage::disk('local')->path($data['csv_file']);
                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('ไม่พบไฟล์ที่อัปโหลด')
                            ->danger()
                            ->send();
                        return;
                    }

                    $fileContent = file_get_contents($filePath);
                    // ลบ UTF-8 BOM ถ้ามี
                    $bom = pack('H*', 'EFBBBF');
                    $fileContent = preg_replace("/^$bom/", '', $fileContent);

                    $lines = preg_split("/\r\n|\n|\r/", trim($fileContent));
                    if (empty($lines)) {
                        Notification::make()
                            ->title('ไฟล์ไม่มีข้อมูล')
                            ->warning()
                            ->send();
                        return;
                    }

                    // ตรวจสอบตัวคั่น (comma หรือ semicolon หรือ tab)
                    $firstLine = $lines[0];
                    $delimiter = ',';
                    if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                        $delimiter = ';';
                    } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                        $delimiter = "\t";
                    }

                    $headers = str_getcsv(array_shift($lines), $delimiter);
                    $headerMap = [];
                    foreach ($headers as $index => $header) {
                        $h = trim(mb_strtolower($header));
                        if (in_array($h, ['name', 'ชื่อ', 'ชื่อ-นามสกุล', 'ชื่อนามสกุล', 'ชื่อสกุล'])) {
                            $headerMap['name'] = $index;
                        } elseif (in_array($h, ['position', 'ตำแหน่ง'])) {
                            $headerMap['position'] = $index;
                        } elseif (in_array($h, ['phone', 'เบอร์โทร', 'เบอร์โทรศัพท์', 'โทรศัพท์', 'โทร'])) {
                            $headerMap['phone'] = $index;
                        } elseif (in_array($h, ['office', 'office_name', 'สำนักงาน', 'สังกัด', 'ที่อยู่'])) {
                            $headerMap['office_name'] = $index;
                        } elseif (in_array($h, ['term', 'วาระ', 'ปี'])) {
                            $headerMap['term'] = $index;
                        } elseif (in_array($h, ['license', 'license_no', 'เลขที่ใบอนุญาต', 'เลขตั๋วทนาย', 'ใบอนุญาต'])) {
                            $headerMap['license_no'] = $index;
                        } elseif (in_array($h, ['email', 'อีเมล'])) {
                            $headerMap['email'] = $index;
                        } elseif (in_array($h, ['type', 'หมวดหมู่', 'ประเภท'])) {
                            $headerMap['type'] = $index;
                        } elseif (in_array($h, ['order', 'order_column', 'ลำดับ'])) {
                            $headerMap['order_column'] = $index;
                        }
                    }

                    if (!isset($headerMap['name'])) {
                        // หากไม่มีหัวตารางชื่อ ให้สมมติว่าคอลัมน์แรกคือชื่อ
                        $headerMap['name'] = 0;
                        if (count($headers) > 1 && !isset($headerMap['phone'])) $headerMap['phone'] = 1;
                        if (count($headers) > 2 && !isset($headerMap['position'])) $headerMap['position'] = 2;
                    }

                    $successCount = 0;
                    $defaultType = $data['default_type'] ?? Personnel::TYPE_LAWYER;

                    foreach ($lines as $lineIndex => $line) {
                        if (empty(trim($line))) continue;
                        $row = str_getcsv($line, $delimiter);
                        $name = isset($headerMap['name']) ? trim($row[$headerMap['name']] ?? '') : '';
                        if (empty($name)) continue;

                        $type = $defaultType;
                        if (isset($headerMap['type']) && !empty(trim($row[$headerMap['type']] ?? ''))) {
                            $t = trim($row[$headerMap['type']]);
                            if (in_array($t, [Personnel::TYPE_COMMITTEE, Personnel::TYPE_PRESIDENT, Personnel::TYPE_LAWYER])) {
                                $type = $t;
                            } elseif (str_contains($t, 'กรรมการ')) {
                                $type = Personnel::TYPE_COMMITTEE;
                            } elseif (str_contains($t, 'ประธาน')) {
                                $type = Personnel::TYPE_PRESIDENT;
                            } else {
                                $type = Personnel::TYPE_LAWYER;
                            }
                        }

                        $position = isset($headerMap['position']) ? trim($row[$headerMap['position']] ?? '') : null;
                        if (empty($position) && $type === Personnel::TYPE_LAWYER) {
                            $position = 'ทนายความ';
                        }

                        $phone = isset($headerMap['phone']) ? trim($row[$headerMap['phone']] ?? '') : null;
                        $officeName = isset($headerMap['office_name']) ? trim($row[$headerMap['office_name']] ?? '') : null;
                        $term = isset($headerMap['term']) ? trim($row[$headerMap['term']] ?? '') : null;
                        $licenseNo = isset($headerMap['license_no']) ? trim($row[$headerMap['license_no']] ?? '') : null;
                        $email = isset($headerMap['email']) ? trim($row[$headerMap['email']] ?? '') : null;
                        $orderColumn = isset($headerMap['order_column']) ? (int) trim($row[$headerMap['order_column']] ?? 0) : ($lineIndex + 1);

                        Personnel::updateOrCreate(
                            [
                                'type' => $type,
                                'name' => $name,
                            ],
                            [
                                'position' => $position,
                                'phone' => $phone,
                                'office_name' => $officeName,
                                'term' => $term,
                                'license_no' => $licenseNo,
                                'email' => $email,
                                'order_column' => $orderColumn,
                                'is_active' => true,
                            ]
                        );

                        $successCount++;
                    }

                    // ลบไฟล์ชั่วคราว
                    @unlink($filePath);

                    Notification::make()
                        ->title("นำเข้ารายชื่อสำเร็จ {$successCount} รายการ")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('downloadTemplate')
                ->label('ดาวน์โหลดตัวอย่างไฟล์ CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function (): StreamedResponse {
                    return response()->streamDownload(function () {
                        // ส่ง UTF-8 BOM สำหรับเปิดใน MS Excel ภาษาไทย
                        echo "\xEF\xBB\xBF";
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, ['ชื่อ-นามสกุล', 'ตำแหน่ง', 'เบอร์โทรศัพท์', 'สำนักงาน/สังกัด', 'วาระ', 'เลขที่ใบอนุญาต', 'หมวดหมู่']);
                        fputcsv($handle, ['นายสมชาย รักความดี', 'ทนายความ', '081-2345678', 'สำนักงานสมชายทนายความ อ.เมืองราชบุรี', '', '1234/2555', 'ratchaburi_lawyer']);
                        fputcsv($handle, ['นางสาวสมหญิง จริงใจ', 'ทนายความ', '089-8765432', 'สำนักงานกฎหมายราชบุรี อ.บ้านโป่ง', '', '5678/2560', 'ratchaburi_lawyer']);
                        fputcsv($handle, ['นายกิตติศักดิ์ มั่นคง', 'กรรมการ', '086-1112233', 'ศาลจังหวัดราชบุรี', 'พ.ศ. 2568 – 2571', '', 'current_committee']);
                        fclose($handle);
                    }, 'personnel_import_template.csv', [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ];
    }
}
