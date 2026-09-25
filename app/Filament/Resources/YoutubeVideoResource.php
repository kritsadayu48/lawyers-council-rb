<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YoutubeVideoResource\Pages;
use App\Models\YoutubeVideo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class YoutubeVideoResource extends Resource
{
    protected static ?string $model = YoutubeVideo::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'วิดีโอและช่อง YouTube';

    protected static ?string $modelLabel = 'วิดีโอ YouTube';

    protected static ?string $pluralModelLabel = 'คลังวิดีโอและถ่ายทอดสด YouTube';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลวิดีโอ YouTube')
                    ->description('นำลิงก์คลิปจาก YouTube มาวาง ระบบจะดึงรหัสและภาพปกอัตโนมัติ')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('ชื่อคลิปวิดีโอ')
                            ->placeholder('เช่น บันทึกภาพกิจกรรม วันรพี ประจำปี 2569')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('youtube_url')
                            ->label('ลิงก์ URL ของวิดีโอ YouTube')
                            ->placeholder('เช่น https://www.youtube.com/watch?v=... หรือ https://youtu.be/...')
                            ->url()
                            ->required()
                            ->helperText('รองรับลิงก์ทุกรูปแบบ (watch, share, shorts, live)')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('published_date')
                            ->label('วันที่ของกิจกรรม / วิดีโอ')
                            ->default(now()),

                        Forms\Components\TextInput::make('order_column')
                            ->label('ลำดับการแสดงผล (ตัวเลขน้อยขึ้นก่อน)')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label('รายละเอียดวิดีโอ (ย่อ)')
                            ->placeholder('เนื้อหาโดยสรุป หรือวิทยากรผู้บรรยาย')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('การตั้งค่าการแสดงผล')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('ตั้งเป็นวิดีโอเด่น / กำลังถ่ายทอดสด (Featured / Live)')
                            ->helperText('หากเปิดใช้งาน วิดีโอนี้จะถูกนำไปแสดงเป็นเครื่องเล่นขนาดใหญ่ด้านบนสุดของหน้าเว็บ')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('แสดงบนเว็บไซต์')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('ภาพปก')
                    ->width(90)
                    ->height(55)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-xs']),

                Tables\Columns\TextColumn::make('title')
                    ->label('ชื่อวิดีโอ')
                    ->searchable()
                    ->sortable()
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->title),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('วิดีโอเด่น/สด')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('แสดงบนเว็บ'),

                Tables\Columns\TextColumn::make('order_column')
                    ->label('ลำดับ')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_date')
                    ->label('วันที่')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('order_column', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('วิดีโอเด่น/สด'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('สถานะเปิดแสดงผล'),
            ])
            ->actions([
                Tables\Actions\Action::make('watch')
                    ->label('เปิดดู')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('danger')
                    ->url(fn (YoutubeVideo $record): string => $record->watch_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()->label('แก้ไข'),
                Tables\Actions\DeleteAction::make()->label('ลบ'),
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
            'index' => Pages\ListYoutubeVideos::route('/'),
            'create' => Pages\CreateYoutubeVideo::route('/create'),
            'edit' => Pages\EditYoutubeVideo::route('/{record}/edit'),
        ];
    }
}
