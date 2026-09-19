<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonnelResource\Pages;
use App\Models\Personnel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'ทำเนียบ / บุคลากร / ทนายความ';

    protected static ?string $modelLabel = 'ข้อมูลบุคลากร';

    protected static ?string $pluralModelLabel = 'ทำเนียบ บุคลากร และทนายความ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ข้อมูลสังกัดและประเภท')
                    ->description('กำหนดประเภทว่าเป็นคณะกรรมการชุดปัจจุบัน, ทำเนียบประธาน หรือทนายความทั่วไป')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('type')
                                ->label('ประเภทบุคลากร')
                                ->options(Personnel::typeLabels())
                                ->default(Personnel::TYPE_COMMITTEE)
                                ->required()
                                ->columnSpan(2),
                            Toggle::make('is_active')
                                ->label('แสดงบนเว็บไซต์')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                Section::make('ข้อมูลส่วนบุคคล')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('ชื่อ - นามสกุล')
                                ->placeholder('เช่น นายมนตรี อิ่มจิตร')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('position')
                                ->label('ตำแหน่ง')
                                ->placeholder('เช่น ประธานสภาทนายความ, รองประธาน, กรรมการ')
                                ->maxLength(255),
                            TextInput::make('term')
                                ->label('วาระ / ปี พ.ศ.')
                                ->placeholder('เช่น พ.ศ. 2568 – 2571')
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('เบอร์โทรศัพท์ติดต่อ')
                                ->placeholder('เช่น 094-8941142')
                                ->tel()
                                ->maxLength(255),
                            TextInput::make('office_name')
                                ->label('สำนักงาน / สังกัด')
                                ->placeholder('เช่น สำนักงานมนตรีทนายความ / อำเภอเมืองราชบุรี')
                                ->maxLength(255),
                            TextInput::make('license_no')
                                ->label('เลขที่ใบอนุญาตว่าความ (ถ้ามี)')
                                ->placeholder('เช่น 1234/2550')
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('อีเมล (ถ้ามี)')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('order_column')
                                ->label('ลำดับการแสดงผล (ตัวเลขน้อยขึ้นก่อน)')
                                ->numeric()
                                ->default(0),
                        ]),

                        FileUpload::make('image_path')
                            ->label('รูปถ่ายบุคลากร')
                            ->directory('personnels')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->helperText('แนะนำรูปถ่ายแนวตั้ง หรือรูปถ่ายหน้าตรงขนาดสี่เหลี่ยมจัตุรัส')
                            ->columnSpanFull(),

                        Textarea::make('bio')
                            ->label('ประวัติย่อ / ข้อมูลการศึกษา / ประสบการณ์ (ถ้ามี)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order_column', 'asc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('รูปถ่าย')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-avatar.png')),
                TextColumn::make('order_column')
                    ->label('ลำดับ')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('name')
                    ->label('ชื่อ - นามสกุล')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('position')
                    ->label('ตำแหน่ง')
                    ->searchable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('type')
                    ->label('หมวดหมู่')
                    ->formatStateUsing(fn ($state) => Personnel::typeLabels()[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        Personnel::TYPE_PRESIDENT => 'danger',
                        Personnel::TYPE_COMMITTEE => 'success',
                        Personnel::TYPE_LAWYER => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('term')
                    ->label('วาระ')
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('เบอร์โทรศัพท์')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone'),
                TextColumn::make('office_name')
                    ->label('สำนักงาน/สังกัด')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('สถานะ')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('หมวดหมู่บุคลากร')
                    ->options(Personnel::typeLabels()),
                TernaryFilter::make('is_active')
                    ->label('สถานะแสดงผลบนเว็บ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonnels::route('/'),
            'create' => Pages\CreatePersonnel::route('/create'),
            'edit' => Pages\EditPersonnel::route('/{record}/edit'),
        ];
    }
}
