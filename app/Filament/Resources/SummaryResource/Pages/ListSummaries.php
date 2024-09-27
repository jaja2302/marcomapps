<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use App\Filament\Resources\SummaryResource\Widgets\GraphSummary;
use App\Filament\Resources\SummaryResource\Widgets\IncomeSummaryWidget;
use App\Filament\Resources\SummaryResource\Widgets\SampleTypeDistributionWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSummaries extends ListRecords
{
    protected static string $resource = SummaryResource::class;
    public static function getDefaultTableView(): ?string
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

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
