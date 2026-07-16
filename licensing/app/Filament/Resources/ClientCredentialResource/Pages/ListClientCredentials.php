<?php

namespace App\Filament\Resources\ClientCredentialResource\Pages;

use App\Filament\Resources\ClientCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientCredentials extends ListRecords
{
    protected static string $resource = ClientCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
