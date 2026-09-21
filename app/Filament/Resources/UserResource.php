<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'ผู้ดูแลระบบ (Admin)';

    protected static ?string $modelLabel = 'ผู้ดูแลระบบ';

    protected static ?string $pluralModelLabel = 'จัดการบัญชีผู้ดูแลระบบ';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลบัญชีผู้ดูแลระบบ')
                    ->description('กำหนดชื่อ อีเมล และรหัสผ่านสำหรับเข้าสู่ระบบจัดการหลังบ้าน')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('ชื่อ - นามสกุล หรือชื่อผู้ดูแล')
                            ->placeholder('เช่น แอดมินสภาทนายความ')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('อีเมลเข้าสู่ระบบ (Email)')
                            ->placeholder('เช่น admin@ratchaburilawyerscouncil.or.th')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('password')
                            ->label('รหัสผ่าน (Password)')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างช่องนี้ไว้' : 'กำหนดรหัสผ่านอย่างน้อย 6 ตัวอักษรขึ้นไป')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อผู้ดูแลระบบ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('อีเมลเข้าสู่ระบบ')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('คัดลอกอีเมลแล้ว')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่สร้างบัญชี')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('แก้ไข / เปลี่ยนรหัสผ่าน'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
