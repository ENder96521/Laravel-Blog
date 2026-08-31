<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()->published()->latest('published_at')->get(['slug', 'updated_at']);
        $categories = Category::query()->get(['slug', 'updated_at']);

        $xml = view('sitemap', ['posts' => $posts, 'categories' => $categories])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
