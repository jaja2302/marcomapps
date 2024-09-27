<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\SummaryResource\Widgets\GraphSummary;
use App\Filament\Resources\SummaryResource\Widgets\IncomeSummaryWidget;
use App\Filament\Resources\SummaryResource\Widgets\SampleTypeDistributionWidget;

class Testing extends Page
{
    protected static string $resource = SummaryResource::class;
    protected static ?string $modelLabel = 'cliente';
    protected static string $view = 'filament.resources.summary-resource.pages.testing';
    protected static ?string $navigationLabel = 'Chart';
    protected static ?string $slug = 'pending-orders';

    public function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgets(): array
    {
        return [
            GraphSummary::class,
            SampleTypeDistributionWidget::class,
            IncomeSummaryWidget::class,
        ];
    }
}
