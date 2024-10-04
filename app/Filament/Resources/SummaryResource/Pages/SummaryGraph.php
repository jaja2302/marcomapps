<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\SummaryResource\Widgets\GraphSummary;
use App\Filament\Resources\SummaryResource\Widgets\IncomeSummaryWidget;
use App\Filament\Resources\SummaryResource\Widgets\OutstandingPayments;
use App\Filament\Resources\SummaryResource\Widgets\SampleTypeDistributionWidget;
use Filament\Support\Enums\MaxWidth;

class SummaryGraph extends Page
{
    protected static string $resource = SummaryResource::class;

    protected static string $view = 'filament.resources.summary-resource.pages.summary-graph';
    protected static ?string $title = 'Summary';
    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }
    protected function getFooterWidgets(): array
    {
        return [
            IncomeSummaryWidget::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OutstandingPayments::class,

            GraphSummary::class,
            SampleTypeDistributionWidget::class,
        ];
    }
}
