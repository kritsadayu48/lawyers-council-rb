<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LawDocumentResource\Pages;
use App\Filament\Resources\LawDocumentResource\RelationManagers;
use App\Models\LawDocument;
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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class LawDocumentResource extends Resource
{
    protected static ?string $model = LawDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'คลังเอกสารและกฎหมาย';

    protected static ?string $modelLabel = 'เอกสารกฎหมาย';

    protected static ?string $pluralModelLabel = 'คลังเอกสารและกฎหมาย';

    public static function form(Form $form): Form
{
    return $form->schema([
        Select::make('category_id')
            ->label('หมวดหมู่เอกสาร')
            ->relationship('category', 'name', fn ($query) => $query->where('type', 'law_document'))
            ->searchable()
            ->preload()
            ->required(),
        TextInput::make('title')
            ->label('ชื่อเอกสาร / ระเบียบ / แบบฟอร์ม')
            ->required(),
        TextInput::make('document_no')
            ->label('เลขที่หนังสือ / ฉบับที่'),
        TextInput::make('year_be')
            ->label('ปี พ.ศ.')
            ->numeric()
            ->maxLength(4),
        FileUpload::make('file_path')
            ->label('อัปโหลดไฟล์เอกสาร (PDF)')
            ->directory('law-documents')
            ->acceptedFileTypes(['application/pdf'])
            ->maxSize(20480) // กำหนดขนาดไฟล์ไม่เกิน 20MB
            ->openable()
            ->downloadable()
            ->required()
            ->columnSpanFull(),
    ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')->label('ชื่อเอกสาร')->searchable()->sortable(),
            TextColumn::make('category.name')->label('หมวดหมู่')->badge()->sortable(),
            TextColumn::make('document_no')->label('เลขที่ฉบับ')->searchable()->sortable(),
            TextColumn::make('year_be')->label('ปี พ.ศ.')->sortable(),
            TextColumn::make('download_count')->label('ดาวน์โหลด (ครั้ง)')->sortable(),
            TextColumn::make('created_at')->label('วันที่เพิ่ม')->dateTime('d/m/Y H:i')->sortable(),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            SelectFilter::make('category_id')
                ->label('กรองตามหมวดหมู่')
                ->relationship('category', 'name', fn ($query) => $query->where('type', 'law_document')),
            SelectFilter::make('year_be')
                ->label('กรองตามปี พ.ศ.')
                ->options(fn () => LawDocument::query()->whereNotNull('year_be')->where('year_be', '!=', '')->distinct()->orderBy('year_be', 'desc')->pluck('year_be', 'year_be')->toArray()),
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from')->label('วันที่เพิ่มตั้งแต่'),
                    DatePicker::make('created_until')->label('ถึงวันที่'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLawDocuments::route('/'),
            'create' => Pages\CreateLawDocument::route('/create'),
            'edit' => Pages\EditLawDocument::route('/{record}/edit'),
        ];
    }
}
