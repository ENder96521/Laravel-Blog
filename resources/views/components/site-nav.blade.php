<header class="border-b border-divider">
    <div class="mx-auto flex max-w-4xl items-center justify-between px-5 py-5 sm:px-6">
        <a href="{{ route('home') }}" class="font-mono text-lg font-medium tracking-tight">
            <span class="text-accent">//</span> {{ config('app.name') }}
        </a>

        <nav class="flex items-center gap-5 text-sm font-heading font-semibold">
            <a href="{{ route('home') }}" class="hover:text-accent">首頁</a>
            <a href="{{ route('categories.index') }}" class="hover:text-accent">分類</a>
            <a href="{{ route('search') }}" class="hover:text-accent" aria-label="搜尋文章">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4.5">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m20 20-3.5-3.5" stroke-linecap="round" />
                </svg>
            </a>

            <button
                type="button"
                x-data="themeToggle"
                x-on:click="toggle"
                aria-label="切換深淺模式"
                class="flex size-6 items-center justify-center border border-divider bg-surface"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3">
                    <circle cx="12" cy="12" r="5" />
                </svg>
            </button>
        </nav>
    </div>
</header>
