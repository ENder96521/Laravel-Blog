<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('home', ['posts' => $posts]);
    }

    public function show(Post $post): View
    {
        abort_unless(
            $post->status === Post::STATUS_PUBLISHED && $post->published_at?->isPast(),
            404
        );

        $post->load(['category', 'tags', 'author']);

        return view('posts.show', ['post' => $post]);
    }

    public function search(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->published()
            ->with(['category', 'tags'])
            ->when($term !== '', fn ($query) => $query->search($term))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('search', ['posts' => $posts, 'term' => $term]);
    }
}
