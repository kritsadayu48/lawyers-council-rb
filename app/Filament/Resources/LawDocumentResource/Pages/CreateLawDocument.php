<?php

namespace App\Filament\Resources\LawDocumentResource\Pages;

use App\Filament\Resources\LawDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLawDocument extends CreateRecord
{
    protected static string $resource = LawDocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
