<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard;
use Filament\Pages\Page;

class InputData extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.dashboard.pages.input-data';

    protected static ?string $cluster = Dashboard::class;
}
