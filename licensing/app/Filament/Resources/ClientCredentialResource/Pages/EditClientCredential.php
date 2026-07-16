<?php

namespace App\Filament\Resources\ClientCredentialResource\Pages;

use App\Filament\Resources\ClientCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientCredential extends EditRecord
{
    protected static string $resource = ClientCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
