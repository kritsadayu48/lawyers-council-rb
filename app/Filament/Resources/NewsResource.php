<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'ข่าวสารและประกาศ';

    protected static ?string $modelLabel = 'ข่าว/ประกาศ';

    protected static ?string $pluralModelLabel = 'ข่าวสารและประกาศ';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label('หมวดหมู่')
                ->relationship('category', 'name', fn ($query) => $query->where('type', 'news'))
                ->searchable()
                ->preload()
                ->required()
                ->helperText('💡 หากต้องการให้แสดงบน "กระดานประกาศและคำสั่ง" ที่หน้าแรก ให้เลือกหมวดหมู่ "ประกาศและหนังสือเวียน"'),
            TextInput::make('title')
                ->label('หัวข้อข่าว / หัวข้อประกาศ')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state, ?string $operation) {
                    if ($operation === 'create' && filled($state)) {
                        $slug = Str::slug($state);
                        if (empty($slug)) {
                            $slug = 'post-' . date('Ymd-His');
                        }
                        $set('slug', $slug);
                    }
                }),
            TextInput::make('slug')
                ->label('Slug (ชื่อลิงก์ URL)')
                ->required()
                ->unique(ignoreRecord: true),
            DatePicker::make('published_at')
                ->label('วันที่ลงข่าว / วันที่ออกประกาศ')
                ->default(now()),
            Toggle::make('is_published')
                ->label('เผยแพร่ทันที')
                ->default(true),
        FileUpload::make('cover_image')
            ->label('รูปภาพหน้าปกข่าว')
            ->directory('news-covers')
            ->image()
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('16:9')
            ->imageResizeTargetWidth('1200')
            ->imageResizeTargetHeight('675')
            ->maxSize(5120)
            ->helperText('ระบบจะช่วยปรับขนาดและบีบอัดรูปภาพให้พอดีกับการแสดงผลหน้าเว็บโดยอัตโนมัติ')
            ->columnSpanFull(),
        RichEditor::make('content')
            ->label('เนื้อหาข่าวสาร')
            ->required()
            ->columnSpanFull(),
        FileUpload::make('gallery_images')
            ->label('ภาพบรรยากาศกิจกรรม / คลังภาพ (อัปโหลดได้หลายภาพ)')
            ->directory('news-galleries')
            ->multiple()
            ->reorderable()
            ->image()
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('1600')
            ->imageResizeTargetHeight('1200')
            ->maxSize(5120)
            ->helperText('รองรับหลายภาพ ระบบจะบีบอัดขนาดภาพให้โหลดเร็วขึ้นอัตโนมัติ')
            ->columnSpanFull(),
    ]);
}

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('cover_image')->label('รูปปก')->circular(),
            TextColumn::make('title')->label('หัวข้อข่าว')->limit(40)->searchable(),
            TextColumn::make('category.name')->label('หมวดหมู่')->badge(),
            IconColumn::make('is_published')->label('สถานะ')->boolean(),
            TextColumn::make('published_at')->label('วันที่ลงข่าว')->date('d/m/Y'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('category_id')
                ->label('กรองตามหมวดหมู่')
                ->relationship('category', 'name', fn ($query) => $query->where('type', 'news')),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
