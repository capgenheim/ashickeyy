<?php

namespace App\Filament\Widgets;

use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriberStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Subscriber::count();
        $thisMonth = Subscriber::where('subscribed_at', '>=', now()->startOfMonth())->count();
        $lastMonth = Subscriber::where('subscribed_at', '>=', now()->subMonth()->startOfMonth())
            ->where('subscribed_at', '<', now()->startOfMonth())
            ->count();

        $trend = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            Stat::make('Total Subscribers', $total)
                ->description('Email newsletter list')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
            Stat::make('New This Month', $thisMonth)
                ->description($trend >= 0 ? "+{$trend}% from last month" : "{$trend}% from last month")
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger'),
            Stat::make('Posts Published', \App\Models\Post::where('status', 'published')->count())
                ->description('Total published articles')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
