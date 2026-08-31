@props(['post'])

<article class="border-b border-divider py-6 first:pt-0 last:border-b-0">
    <div class="flex items-start justify-between gap-4">
        <h3 class="text-xl font-heading font-extrabold tracking-tight text-balance">
            <a href="{{ route('posts.show', $post) }}" class="hover:text-accent">
                {{ $post->title }}
            </a>
        </h3>

        @if($post->category)
            <x-tag variant="outline" class="mt-1 shrink-0">
                <a href="{{ route('categories.show', $post->category) }}">{{ $post->category->name }}</a>
            </x-tag>
        @endif
    </div>

    <p class="mt-2 text-sm text-neutral-600">{{ $post->display_excerpt }}</p>

    <p class="mt-3 font-mono text-xs text-neutral-500">
        {{ $post->published_at?->format('Y-m-d') }} ・ {{ $post->reading_time }} 分鐘閱讀
    </p>
</article>
