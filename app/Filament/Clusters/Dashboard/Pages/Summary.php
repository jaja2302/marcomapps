<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard;
use Filament\Pages\Page;

class Summary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.dashboard.pages.summary';

    protected static ?string $cluster = Dashboard::class;
}
