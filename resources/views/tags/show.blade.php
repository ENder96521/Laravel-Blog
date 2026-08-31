<x-layout :title="'#'.$tag->name" :description="'標記「'.$tag->name.'」的技術文章列表。'">
    <p class="font-mono text-xs tracking-wide text-accent uppercase">標籤</p>
    <h1 class="mt-2 text-3xl font-heading font-extrabold tracking-tight">#{{ $tag->name }}</h1>

    @if($posts->isEmpty())
        <p class="mt-10 font-mono text-sm text-neutral-500">這個標籤目前還沒有文章。</p>
    @else
        <div class="mt-10 border-t border-divider">
            @foreach($posts as $post)
                <x-post-row :post="$post" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</x-layout>
