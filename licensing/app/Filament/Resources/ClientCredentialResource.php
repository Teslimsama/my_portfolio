<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientCredentialResource\Pages;
use App\Models\ClientCredential;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientCredentialResource extends Resource
{
    protected static ?string $model = ClientCredential::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Client Credentials';

    protected static ?string $modelLabel = 'Client Credential';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('The product this credential grants access to.'),

                Forms\Components\TextInput::make('client_name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('e.g. "Bolakaz - shopname.ng"'),

                Forms\Components\TextInput::make('domain')
                    ->maxLength(255)
                    ->url()
                    ->helperText('For reference only — not used in auth.'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('licensableProduct.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_key')
                    ->label('Client Key')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('domain')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->label('Product'),

                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Revoking this credential will prevent the client from redeeming codes.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListClientCredentials::route('/'),
            'create' => Pages\CreateClientCredential::route('/create'),
            'edit' => Pages\EditClientCredential::route('/{record}/edit'),
        ];
    }
}
