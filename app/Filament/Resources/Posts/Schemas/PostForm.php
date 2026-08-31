<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Support\Slug;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->required(),

                Section::make('內容')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->label('標題')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Slug::make($state)) : null)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('用於網址，可手動覆蓋自動產生的結果')
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('摘要')
                            ->rows(2)
                            ->helperText('顯示於文章列表卡片，留空則自動截取內容開頭')
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('內容')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('分類與標籤')
                    ->components([
                        Select::make('category_id')
                            ->label('分類')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label('名稱')->required(),
                                TextInput::make('slug')->label('Slug')->helperText('留空則自動產生'),
                            ])
                            ->createOptionAction(fn ($action) => $action->modalHeading('新增分類')),
                        Select::make('tags')
                            ->label('標籤')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label('名稱')->required(),
                                TextInput::make('slug')->label('Slug')->helperText('留空則自動產生'),
                            ]),
                    ]),

                Section::make('圖片')
                    ->components([
                        FileUpload::make('cover_image_path')
                            ->label('封面圖')
                            ->image()
                            ->directory('covers')
                            ->visibility('public')
                            ->imageEditor(),
                        FileUpload::make('og_image_path')
                            ->label('OG 圖片（社群分享用）')
                            ->image()
                            ->directory('og-images')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('留空則發布時自動沿用封面圖'),
                    ]),

                Section::make('SEO')
                    ->components([
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(160)
                            ->columnSpanFull(),
                    ]),

                Section::make('發布狀態')
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('status')
                                    ->label('狀態')
                                    ->options([
                                        Post::STATUS_DRAFT => '草稿',
                                        Post::STATUS_SCHEDULED => '排程發布',
                                        Post::STATUS_PUBLISHED => '已發布',
                                    ])
                                    ->default(Post::STATUS_DRAFT)
                                    ->required()
                                    ->live(),
                                Select::make('source')
                                    ->label('來源')
                                    ->options([
                                        Post::SOURCE_MANUAL => '手動撰寫',
                                        Post::SOURCE_AI_GENERATED => 'AI 產生',
                                        Post::SOURCE_IMPORTED => '匯入',
                                    ])
                                    ->default(Post::SOURCE_MANUAL)
                                    ->required(),
                            ]),
                        DateTimePicker::make('published_at')
                            ->label('發布時間')
                            ->native(false)
                            ->helperText('狀態為「排程發布」時，此時間到達後才會視為可發布')
                            ->visible(fn (callable $get) => in_array($get('status'), [Post::STATUS_SCHEDULED, Post::STATUS_PUBLISHED])),
                        KeyValue::make('generation_meta')
                            ->label('AI 產生中繼資料')
                            ->helperText('由自動化流程寫入，記錄提示詞、模型、參考連結等，僅供追溯')
                            ->disabled()
                            ->visible(fn (callable $get) => $get('source') === Post::SOURCE_AI_GENERATED),
                    ]),
            ]);
    }
}
