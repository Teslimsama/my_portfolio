<?php

namespace App\Filament\Resources\LicenseCodeResource\Pages;

use App\Filament\Resources\LicenseCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLicenseCode extends ViewRecord
{
    protected static string $resource = LicenseCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit/delete — codes are immutable
        ];
    }
}
