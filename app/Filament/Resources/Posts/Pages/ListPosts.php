<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('全部'),

            'pending' => Tab::make('待審草稿')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', Post::STATUS_DRAFT)
                    ->where('source', Post::SOURCE_AI_GENERATED))
                ->badge(fn () => Post::query()
                    ->where('status', Post::STATUS_DRAFT)
                    ->where('source', Post::SOURCE_AI_GENERATED)
                    ->count()),

            'draft' => Tab::make('草稿')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Post::STATUS_DRAFT)),

            'scheduled' => Tab::make('排程發布')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Post::STATUS_SCHEDULED)),

            'published' => Tab::make('已發布')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Post::STATUS_PUBLISHED)),
        ];
    }
}
