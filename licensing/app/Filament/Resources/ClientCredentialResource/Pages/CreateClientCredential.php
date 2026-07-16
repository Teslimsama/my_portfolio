<?php

namespace App\Filament\Resources\ClientCredentialResource\Pages;

use App\Filament\Resources\ClientCredentialResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateClientCredential extends CreateRecord
{
    protected static string $resource = ClientCredentialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $rawSecret = Str::random(40);

        // Generate a readable client_key from the client_name
        $prefix = strtoupper(Str::slug($data['client_name'], ''));
        $prefix = substr($prefix, 0, 12);
        $data['client_key'] = $prefix . '_' . Str::random(24);

        // Store ONLY the hashed secret in the database
        $data['api_secret'] = Hash::make($rawSecret);

        // Flash the raw secret to show once after creation
        session()->flash('raw_api_secret', $rawSecret);
        session()->flash('new_client_key', $data['client_key']);
        session()->flash('new_client_name', $data['client_name']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $rawSecret = session('raw_api_secret');
        $clientKey = session('new_client_key');

        Notification::make()
            ->title('Client Credential Created')
            ->body("Client Key: {$clientKey}")
            ->success()
            ->persistent()
            ->send();

        // Display the plaintext secret in a separate notification (critical — shown once)
        Notification::make()
            ->title('⚠️ API Secret — COPY NOW')
            ->body("Secret: {$rawSecret}\n\nThis is the ONLY time this secret will be shown. Store it securely.")
            ->warning()
            ->persistent()
            ->send();
    }
}
