<?php

namespace App\Filament\Resources\SummaryResource\Widgets;

use App\Models\Databaseinvoice;
use App\Models\JenisSampel;
use Filament\Widgets\ChartWidget;

class SampleTypeDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Sample Type Distribution';
    public ?string $filter = 'week';

    protected function getData(): array
    {
        $data = $this->getFilteredData($this->filter);
        $sampleTypes = [];
        // dd($data);
        $jenisSampelData = JenisSampel::all()->pluck('nama', 'id');
        // dd($jenisSampelData);

        foreach ($data as $value) {
            $sampleType = $value['residetail']['data']; // Adjust this to get the correct sample type
            // dd($sampleType);
            // Assuming `data` contains the sample type as a JSON string
            $sampleTypeDecoded = json_decode($sampleType, true);
            // dd($sampleTypeDecoded);
            foreach ($sampleTypeDecoded as $sample) {
                // dd($sample);
                foreach ($sample['locationDetails'] as $key => $value) {
                    foreach ($value['parameterDetails'] as $key1 => $value1) {
                        $type = $jenisSampelData[$value1['sampleType']] ?? 'Unknown';

                        if (!isset($sampleTypes[$type])) {
                            $sampleTypes[$type] = 0;
                        }
                        $sampleTypes[$type]++;
                    }
                }
            }
        }
        // dd($sampleTypes);
        return [
            'datasets' => [
                [
                    'label' => 'Sample Types',
                    'data' => array_values($sampleTypes),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                    'hoverOffset' => 4,
                    'weight' => 1,
                    'hoverbackgroundColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    'hoverdash' => [
                        [2, 2],
                        [2, 2],
                        [2, 2],
                        [2, 2],
                        [2, 2],
                        [2, 2],
                    ],
                    'animation' => [
                        'animateScale' => true,
                        'animateRotate' => true,
                    ]
                ],
            ],
            'labels' => array_keys($sampleTypes),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
