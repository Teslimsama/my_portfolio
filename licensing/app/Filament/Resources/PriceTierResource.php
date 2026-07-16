<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceTierResource\Pages;
use App\Models\PriceTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceTierResource extends Resource
{
    protected static ?string $model = PriceTier::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Price Tiers';

    protected static ?string $modelLabel = 'Price Tier';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('tier_key')
                    ->required()
                    ->options([
                        'fixed_3day' => '3 Days (Fixed)',
                        'fixed_7day' => '7 Days (Fixed)',
                        'fixed_30day' => '30 Days (Fixed)',
                        'custom' => 'Custom Duration',
                    ])
                    ->live()
                    ->helperText('Custom tier allows the buyer to choose the number of days.'),

                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->visible(fn (Forms\Get $get): bool => $get('tier_key') !== 'custom')
                    ->helperText('Leave empty for custom tier.'),

                Forms\Components\TextInput::make('discount_pct')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->helperText('Discount percentage. 0 for custom tier.'),

                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),

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

                Tables\Columns\TextColumn::make('tier_key')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed_3day' => 'success',
                        'fixed_7day' => 'info',
                        'fixed_30day' => 'warning',
                        'custom' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed_3day' => '3 Days',
                        'fixed_7day' => '7 Days',
                        'fixed_30day' => '30 Days',
                        'custom' => 'Custom',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->placeholder('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_pct')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('licensable_product_id')
                    ->relationship('licensableProduct', 'name')
                    ->label('Product'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListPriceTiers::route('/'),
            'create' => Pages\CreatePriceTier::route('/create'),
            'edit' => Pages\EditPriceTier::route('/{record}/edit'),
        ];
    }
}
