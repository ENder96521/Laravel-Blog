<x-layout title="搜尋" description="搜尋技術文章標題與內容。">
    <h1 class="text-3xl font-heading font-extrabold tracking-tight">搜尋</h1>

    <form action="{{ route('search') }}" method="GET" class="mt-6">
        <input
            type="search"
            name="q"
            value="{{ $term }}"
            placeholder="輸入關鍵字…"
            class="w-full border border-divider bg-surface px-4 py-3 font-body text-base text-text focus:outline-none focus:ring-1 focus:ring-accent"
        >
    </form>

    @if($term === '')
        <p class="mt-10 font-mono text-sm text-neutral-500">輸入關鍵字開始搜尋。</p>
    @elseif($posts->isEmpty())
        <p class="mt-10 font-mono text-sm text-neutral-500">找不到符合「{{ $term }}」的文章。</p>
    @else
        <p class="mt-10 font-mono text-xs text-neutral-500">
            共 {{ $posts->total() }} 筆符合「{{ $term }}」的結果
        </p>

        <div class="mt-4 border-t border-divider">
            @foreach($posts as $post)
                <x-post-row :post="$post" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</x-layout>
