<x-layout>
    <p class="max-w-2xl text-lg text-neutral-600">
        紀錄 Laravel 與網頁工程裡實際踩過的取捨——沒有結論的部分，也會寫出來。
    </p>

    @if($posts->isEmpty())
        <p class="mt-16 font-mono text-sm text-neutral-500">目前還沒有發布任何文章。</p>
    @else
        @php
            $featured = $posts->first();
            $secondary = $posts->slice(1, 2);
            $rest = $posts->slice(3);
        @endphp

        <div class="mt-10 grid gap-10 lg:grid-cols-2">
            <article>
                @if($featured->cover_image_url)
                    <a href="{{ route('posts.show', $featured) }}" class="block">
                        <img src="{{ $featured->cover_image_url }}" alt="{{ $featured->title }}" class="aspect-[4/3] w-full object-cover">
                    </a>
                @endif

                @if($featured->category)
                    <x-tag class="mt-4 block">
                        <a href="{{ route('categories.show', $featured->category) }}">{{ $featured->category->name }}</a>
                    </x-tag>
                @endif

                <h2 class="mt-2 text-3xl font-heading font-extrabold tracking-tight text-balance">
                    <a href="{{ route('posts.show', $featured) }}" class="hover:text-accent">{{ $featured->title }}</a>
                </h2>

                <p class="mt-3 text-neutral-600">{{ $featured->display_excerpt }}</p>

                <p class="mt-4 font-mono text-xs text-neutral-500">
                    {{ $featured->published_at?->format('Y-m-d') }} ・ {{ $featured->reading_time }} 分鐘閱讀
                </p>
            </article>

            <div class="flex flex-col divide-y divide-divider">
                @foreach($secondary as $post)
                    <article class="py-6 first:pt-0">
                        @if($post->category)
                            <x-tag class="block">
                                <a href="{{ route('categories.show', $post->category) }}">{{ $post->category->name }}</a>
                            </x-tag>
                        @endif

                        <h3 class="mt-2 text-xl font-heading font-extrabold tracking-tight text-balance">
                            <a href="{{ route('posts.show', $post) }}" class="hover:text-accent">{{ $post->title }}</a>
                        </h3>

                        <p class="mt-3 font-mono text-xs text-neutral-500">
                            {{ $post->published_at?->format('Y-m-d') }} ・ {{ $post->reading_time }} 分鐘閱讀
                        </p>
                    </article>
                @endforeach
            </div>
        </div>

        @if($rest->isNotEmpty())
            <div class="mt-10 border-t border-divider">
                @foreach($rest as $post)
                    <x-post-row :post="$post" />
                @endforeach
            </div>
        @endif

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</x-layout>
