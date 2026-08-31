@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
])

@php
    $siteName = config('app.name');
    $pageTitle = $title ? "{$title} — {$siteName}" : "{$siteName}：一個工程師的技術筆記本";
    $pageDescription = $description ?? '紀錄 Laravel 與網頁工程裡實際踩過的取捨——沒有結論的部分，也會寫出來。';
@endphp
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            if (stored === 'dark' || stored === 'light') {
                document.documentElement.setAttribute('data-theme', stored);
            }
        })();
    </script>

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $siteName }} RSS" href="{{ route('feed') }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bg text-text font-body antialiased">
    <x-site-nav />

    <main class="mx-auto max-w-4xl px-5 py-10 sm:px-6">
        {{ $slot }}
    </main>

    <x-site-footer />
</body>
</html>
