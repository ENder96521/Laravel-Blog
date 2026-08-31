<x-layout title="分類" description="依主題瀏覽所有技術文章分類。">
    <h1 class="text-3xl font-heading font-extrabold tracking-tight">分類</h1>

    @if($categories->isEmpty())
        <p class="mt-10 font-mono text-sm text-neutral-500">目前還沒有任何分類。</p>
    @else
        <ul class="mt-10 divide-y divide-divider border-t border-divider">
            @foreach($categories as $category)
                <li class="flex items-center justify-between py-4">
                    <a href="{{ route('categories.show', $category) }}" class="text-lg font-heading font-bold hover:text-accent">
                        {{ $category->name }}
                    </a>
                    <span class="font-mono text-xs text-neutral-500">{{ $category->posts_count }} 篇</span>
                </li>
            @endforeach
        </ul>
    @endif
</x-layout>
