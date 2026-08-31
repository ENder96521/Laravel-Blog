<x-layout :title="$category->name" :description="'「'.$category->name.'」分類下的技術文章列表。'">
    <p class="font-mono text-xs tracking-wide text-accent uppercase">分類</p>
    <h1 class="mt-2 text-3xl font-heading font-extrabold tracking-tight">{{ $category->name }}</h1>

    @if($posts->isEmpty())
        <p class="mt-10 font-mono text-sm text-neutral-500">這個分類目前還沒有文章。</p>
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
