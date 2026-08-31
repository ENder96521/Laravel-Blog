<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label('封面')
                    ->square(),
                TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('category.name')
                    ->label('分類')
                    ->badge(),
                TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Post::STATUS_DRAFT => '草稿',
                        Post::STATUS_SCHEDULED => '排程發布',
                        Post::STATUS_PUBLISHED => '已發布',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        Post::STATUS_DRAFT => 'gray',
                        Post::STATUS_SCHEDULED => 'warning',
                        Post::STATUS_PUBLISHED => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->label('來源')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Post::SOURCE_MANUAL => '手動撰寫',
                        Post::SOURCE_AI_GENERATED => 'AI 產生',
                        Post::SOURCE_IMPORTED => '匯入',
                        default => $state,
                    })
                    ->color(fn (string $state) => $state === Post::SOURCE_AI_GENERATED ? 'info' : 'gray'),
                TextColumn::make('published_at')
                    ->label('發布時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        Post::STATUS_DRAFT => '草稿',
                        Post::STATUS_SCHEDULED => '排程發布',
                        Post::STATUS_PUBLISHED => '已發布',
                    ]),
                SelectFilter::make('source')
                    ->label('來源')
                    ->options([
                        Post::SOURCE_MANUAL => '手動撰寫',
                        Post::SOURCE_AI_GENERATED => 'AI 產生',
                        Post::SOURCE_IMPORTED => '匯入',
                    ]),
                SelectFilter::make('category_id')
                    ->label('分類')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('發布')
                    ->icon(Heroicon::OutlinedRocketLaunch)
                    ->color('success')
                    ->visible(fn (Post $record) => $record->status !== Post::STATUS_PUBLISHED)
                    ->requiresConfirmation()
                    ->action(fn (Post $record) => $record->update([
                        'status' => Post::STATUS_PUBLISHED,
                        'published_at' => now(),
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
