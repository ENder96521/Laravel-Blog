<x-layout
    :title="$post->title"
    :description="$post->meta_description ?? $post->display_excerpt"
    :og-image="$post->og_image_url"
>
    <article>
        @if($post->category)
            <x-tag class="block">
                <a href="{{ route('categories.show', $post->category) }}">{{ $post->category->name }}</a>
            </x-tag>
        @endif

        <h1 class="mt-3 text-3xl font-heading font-extrabold tracking-tight text-balance sm:text-4xl">
            {{ $post->title }}
        </h1>

        <p class="mt-4 font-mono text-xs text-neutral-500">
            {{ $post->author->name }} ・ {{ $post->published_at?->format('Y-m-d') }} ・ {{ $post->reading_time }} 分鐘閱讀
        </p>

        @if($post->cover_image_url)
            <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="mt-8 w-full object-cover">
        @endif

        <div class="prose-fieldnotes mt-8 max-w-none">
            {!! $post->content !!}
        </div>

        @if($post->tags->isNotEmpty())
            <div class="mt-10 flex flex-wrap gap-2 border-t border-divider pt-6">
                @foreach($post->tags as $tag)
                    <x-tag variant="outline">
                        <a href="{{ route('tags.show', $tag) }}">#{{ $tag->name }}</a>
                    </x-tag>
                @endforeach
            </div>
        @endif
    </article>

    <div class="mt-10">
        <a href="{{ route('home') }}" class="font-heading text-sm font-semibold hover:text-accent">← 回文章列表</a>
    </div>
</x-layout>
