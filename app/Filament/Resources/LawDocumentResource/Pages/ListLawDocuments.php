<?php

namespace App\Filament\Resources\LawDocumentResource\Pages;

use App\Filament\Resources\LawDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLawDocuments extends ListRecords
{
    protected static string $resource = LawDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
