<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ route('home') }}</link>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        <description>紀錄 Laravel 與網頁工程裡實際踩過的取捨。</description>
        <language>zh-TW</language>
        @foreach($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('posts.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('posts.show', $post) }}</guid>
                <pubDate>{{ $post->published_at?->toRfc2822String() }}</pubDate>
                @if($post->category)
                    <category>{{ $post->category->name }}</category>
                @endif
                <description>{{ $post->display_excerpt }}</description>
            </item>
        @endforeach
    </channel>
</rss>
