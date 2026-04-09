<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TelemetryOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Telemetry Overview';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        try {
            $db = app('db')->connection('mongodb')->getDatabase();
            $analytics = $db->visitor_analytics;

            return [
                'datasets' => [
                    [
                        'label' => 'Visitor Actions',
                        'data' => [
                            $analytics->countDocuments(['action' => 'app_open']),
                            $analytics->countDocuments(['action' => 'just_view']),
                            $analytics->countDocuments(['action' => 'actual_reader'])
                        ],
                        'backgroundColor' => [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)'
                        ],
                        'borderColor' => [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        'borderWidth' => 1
                    ],
                ],
                'labels' => ['App Opens', 'Just Views', 'Actual Readers'],
            ];
        } catch (\Exception $e) {
            return [
                'datasets' => [['label' => 'Visitor Actions', 'data' => [0, 0, 0]]],
                'labels' => ['App Opens', 'Just Views', 'Actual Readers'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
