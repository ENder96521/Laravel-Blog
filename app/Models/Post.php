<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_AI_GENERATED = 'ai_generated';
    public const SOURCE_IMPORTED = 'imported';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image_path',
        'status',
        'source',
        'generation_meta',
        'meta_description',
        'og_image_path',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'generation_meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if (! $post->slug) {
                $post->slug = Slug::make($post->title);
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * 沒填摘要時，自動用內容開頭當摘要。
     */
    public function getDisplayExcerptAttribute(): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags((string) $this->content), 120);
    }

    /**
     * 依中文閱讀速度（約 400 字/分鐘）估算閱讀時間，最少 1 分鐘。
     */
    public function getReadingTimeAttribute(): int
    {
        $length = mb_strlen(strip_tags((string) $this->content));

        return max(1, (int) ceil($length / 400));
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? Storage::disk('public')->url($this->cover_image_path) : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image_path
            ? Storage::disk('public')->url($this->og_image_path)
            : $this->cover_image_url;
    }
}
