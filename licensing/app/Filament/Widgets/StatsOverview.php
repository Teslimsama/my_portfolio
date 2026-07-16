<?php

namespace App\Filament\Widgets;

use App\Models\LicensableProduct;
use App\Models\LicenseCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = LicensableProduct::where('is_active', true)->count();
        $totalCodes = LicenseCode::count();
        $totalRevenue = LicenseCode::sum('price_paid');
        $redeemedCount = LicenseCode::where('status', 'redeemed')->count();
        $unusedCount = LicenseCode::where('status', 'unused')->count();

        return [
            Stat::make('Active Products', $totalProducts)
                ->description('Licensable products available for purchase')
                ->icon('heroicon-o-cube'),

            Stat::make('Total Codes', $totalCodes)
                ->description("{$redeemedCount} redeemed · {$unusedCount} unused")
                ->icon('heroicon-o-ticket'),

            Stat::make('Total Revenue', '₦' . number_format($totalRevenue, 2))
                ->description('Lifetime revenue')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
