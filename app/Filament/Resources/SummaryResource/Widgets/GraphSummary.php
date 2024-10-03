<?php

namespace App\Filament\Resources\SummaryResource\Widgets;

use App\Models\Databaseinvoice;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class GraphSummary extends ChartWidget
{
    protected static ?string $heading = 'Total Revenue Summary';

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $data = $this->getFilteredData($this->filter);
        $data = $data->groupBy('Perusahaan.nama');
        $result = [];

        foreach ($data as $perusahaan => $perusahaanData) {
            $totalRevenue = 0;
            foreach ($perusahaanData as $value) {
                $totalRevenue += $value['residetail']['totalharga_disc']; // Assuming this key exists
            }

            $result[] = [
                'perusahaan' => $perusahaan,
                'total_revenue' => $totalRevenue,
            ];
        }

        $total = array_map(fn($item) => $item['total_revenue'], $result);
        $customerLabels = array_map(fn($item) => $item['perusahaan'], $result);

        return [
            'datasets' => [
                [
                    'label' => 'Total Revenue (Rupiah)',
                    'data' => $total,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                ],
            ],
            'labels' => $customerLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    private function getFilteredData(string $filter)
    {
        $now = now()->setTimezone('Asia/Jakarta');

        switch ($filter) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }

        return Databaseinvoice::with('Perusahaan', 'Residetail')
            ->whereBetween('tanggal_penerbitan_invoice', [$start, $end])
            ->get();
    }
}
