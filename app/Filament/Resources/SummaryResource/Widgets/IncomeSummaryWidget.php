<?php

namespace App\Filament\Resources\SummaryResource\Widgets;

use App\Models\Databaseinvoice;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class IncomeSummaryWidget extends ChartWidget
{
    protected static ?string $heading = 'Income Summary';
    public ?string $filter = 'year'; // Adjusted filter to match your requirement
    protected static ?string $pollingInterval = null;
    protected function getData(): array
    {
        $data = $this->getFilteredData($this->filter);
        $incomeSummary = [];

        foreach ($data as $value) {
            $month = Carbon::parse($value['tanggal_penerbitan_invoice'])->format('F Y');
            if (!isset($incomeSummary[$month])) {
                $incomeSummary[$month] = 0;
            }
            $incomeSummary[$month] += $value['residetail']['totalharga_disc']; // Adjust according to your structure
        }

        // Sort the summary by month in ascending order
        uksort($incomeSummary, function ($a, $b) {
            return Carbon::createFromFormat('F Y', $a)->gt(Carbon::createFromFormat('F Y', $b)) ? 1 : -1;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Income (Rupiah)',
                    'data' => array_values($incomeSummary),

                ],
            ],
            'labels' => array_keys($incomeSummary),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'year' => 'this year', // You can customize this if needed
        ];
    }

    private function getFilteredData(string $filter)
    {
        $now = now()->setTimezone('Asia/Jakarta');

        // Get the current year
        $currentYear = $now->year;

        return Databaseinvoice::with('Perusahaan', 'Residetail')
            ->whereYear('tanggal_penerbitan_invoice', $currentYear) // Filter for the current year
            ->get();
    }
}
