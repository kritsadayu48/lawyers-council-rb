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
                ->placeholder('เช่น ข่าวสัมมนา, แบบฟอร์มศาล')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    if (filled($state)) {
                        $slug = Str::slug($state);
                        $set('slug', empty($slug) ? 'cat-' . date('YmdHis') : $slug);
                    }
                }),
            TextInput::make('slug')
                ->label('Slug (URL)')
                ->placeholder('สร้างอัตโนมัติ')
                ->helperText('รหัส URL ภาษาอังกฤษ (ระบบสร้างให้อัตโนมัติ สามารถแก้ไขเองได้)')
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('type')
                ->label('ประเภทการใช้งานหมวดหมู่')
                ->options([
                    'news' => 'หมวดหมู่ข่าวสาร / กิจกรรม / ประกาศ',
                    'law_document' => 'หมวดหมู่คลังกฎหมาย / แบบฟอร์ม / ระเบียบ',
                ])
                ->default('news')
                ->required()
                ->helperText('เลือกประเภทเพื่อให้หมวดหมู่นี้ไปแสดงในส่วนข่าวสาร หรือส่วนคลังกฎหมาย'),
        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->label('ชื่อหมวดหมู่')->searchable()->sortable(),
            TextColumn::make('type')->label('ประเภท')->badge()->color(fn (string $state): string => match ($state) {
                'news' => 'info',
                'law_document' => 'success',
            })->sortable(),
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
