<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard as DashboardCluster;
use Filament\Pages\Page;
use App\Filament\Widgets\Tracksampel;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.clusters.dashboard.pages.dashboard';

    protected static ?string $cluster = DashboardCluster::class;

    protected function getHeaderWidgets(): array
    {
        return [
            Tracksampel::class,
        ];
    }
    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
