<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->limit(30)
            ->get();

        $xml = view('feed', ['posts' => $posts])->render();

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
