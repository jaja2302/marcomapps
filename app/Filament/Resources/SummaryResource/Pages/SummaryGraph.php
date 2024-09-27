<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\SummaryResource\Widgets\GraphSummary;
use App\Filament\Resources\SummaryResource\Widgets\IncomeSummaryWidget;
use App\Filament\Resources\SummaryResource\Widgets\OutstandingPayments;
use App\Filament\Resources\SummaryResource\Widgets\SampleTypeDistributionWidget;

class SummaryGraph extends Page
{
    protected static string $resource = SummaryResource::class;

    protected static string $view = 'filament.resources.summary-resource.pages.summary-graph';

    public function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgets(): array
    {
        return [
            OutstandingPayments::class,
            GraphSummary::class,
            SampleTypeDistributionWidget::class,
            IncomeSummaryWidget::class,
        ];
    }
}
