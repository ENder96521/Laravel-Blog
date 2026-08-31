<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('已發布文章', Post::query()->where('status', Post::STATUS_PUBLISHED)->count())
                ->color('success'),

            Stat::make('待審草稿（AI 產生）', Post::query()
                ->where('status', Post::STATUS_DRAFT)
                ->where('source', Post::SOURCE_AI_GENERATED)
                ->count())
                ->color('warning'),

            Stat::make('排程發布中', Post::query()->where('status', Post::STATUS_SCHEDULED)->count())
                ->color('info'),

            Stat::make('草稿總數', Post::query()->where('status', Post::STATUS_DRAFT)->count())
                ->color('gray'),
        ];
    }
}
