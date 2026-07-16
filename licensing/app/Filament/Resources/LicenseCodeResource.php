<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseCodeResource\Pages;
use App\Models\LicenseCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicenseCodeResource extends Resource
{
    protected static ?string $model = LicenseCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'License Codes';

    protected static ?string $modelLabel = 'License Code';

    /**
     * License codes are created by the purchase flow, not manually.
     * The form is read-only and only used for the View page.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->disabled(),

                Forms\Components\TextInput::make('code')
                    ->disabled(),

                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->disabled(),

                Forms\Components\TextInput::make('price_paid')
                    ->numeric()
                    ->prefix('₦')
                    ->disabled(),

                Forms\Components\TextInput::make('payment_reference')
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->options([
                        'unused' => 'Unused',
                        'redeemed' => 'Redeemed',
                    ])
                    ->disabled(),

                Forms\Components\TextInput::make('redeemed_by_domain')
                    ->disabled()
                    ->visible(fn ($record) => $record?->isRedeemed()),

                Forms\Components\TextInput::make('redeemed_by_client_key')
                    ->disabled()
                    ->visible(fn ($record) => $record?->isRedeemed()),

                Forms\Components\DateTimePicker::make('redeemed_at')
                    ->disabled()
                    ->visible(fn ($record) => $record?->isRedeemed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('licensableProduct.name')
                    ->label('Product')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unused' => 'success',
                        'redeemed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('redeemed_by_domain')
                    ->label('Redeemed By')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('redeemed_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Purchased')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->label('Product'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unused' => 'Unused',
                        'redeemed' => 'Redeemed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions — codes are immutable
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
            'index' => Pages\ListLicenseCodes::route('/'),
            'view' => Pages\ViewLicenseCode::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Codes are only created via purchase flow
    }
}
