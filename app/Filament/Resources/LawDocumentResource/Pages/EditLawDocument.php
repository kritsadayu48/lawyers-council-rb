<?php

namespace App\Filament\Resources\LawDocumentResource\Pages;

use App\Filament\Resources\LawDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLawDocument extends EditRecord
{
    protected static string $resource = LawDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
