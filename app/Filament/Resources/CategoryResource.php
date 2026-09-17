<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Closure;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'หมวดหมู่';

    protected static ?string $modelLabel = 'หมวดหมู่';

    protected static ?string $pluralModelLabel = 'หมวดหมู่ทั้งหมด';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('ชื่อหมวดหมู่')
                ->placeholder('พิมพ์ชื่อหมวดหมู่ เช่น ข่าวสัมมนา, แบบฟอร์มศาล')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('type')
                ->label('ประเภทการใช้งานหมวดหมู่')
                ->options([
                    'news' => '📰 สำหรับหมวดหมู่ข่าวสาร / กิจกรรม / ประกาศ',
                    'law_document' => '📂 สำหรับหมวดหมู่คลังกฎหมาย / แบบฟอร์ม / ระเบียบ',
                ])
                ->default('news')
                ->required()
                ->helperText('กำหนดว่าจะให้หมวดหมู่นี้ไปแสดงในส่วนข่าวสาร หรือส่วนดาวน์โหลดเอกสาร')
                ->columnSpanFull(),
            Forms\Components\Section::make('ตั้งค่าเพิ่มเติม (สำหรับแอดมิน / ไม่จำเป็นต้องแก้ไข)')
                ->collapsed()
                ->schema([
                    TextInput::make('slug')
                        ->label('รหัส URL (Slug)')
                        ->helperText('หากเว้นว่างไว้ ระบบจะสร้างให้อัตโนมัติในฐานข้อมูล')
                        ->unique(ignoreRecord: true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->label('ชื่อหมวดหมู่')->searchable()->sortable(),
            TextColumn::make('type')->label('ประเภทการใช้งาน')->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'news' => '📰 ข่าวสาร/กิจกรรม',
                    'law_document' => '📂 คลังกฎหมาย/แบบฟอร์ม',
                    default => $state,
                })
                ->color(fn (string $state): string => match ($state) {
                    'news' => 'info',
                    'law_document' => 'success',
                    default => 'gray',
                })->sortable(),
            TextColumn::make('news_count')->counts('news')->label('จำนวนข่าว')->sortable(),
            TextColumn::make('law_documents_count')->counts('lawDocuments')->label('จำนวนเอกสาร')->sortable(),
            TextColumn::make('created_at')->label('สร้างเมื่อ')->dateTime('d/m/Y')->sortable(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            SelectFilter::make('type')
                ->label('กรองตามประเภท')
                ->options([
                    'news' => 'ข่าวสาร / กิจกรรม',
                    'law_document' => 'คลังกฎหมาย / แบบฟอร์ม',
                ]),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
