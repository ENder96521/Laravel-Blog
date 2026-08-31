<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('tags.show', ['tag' => $tag, 'posts' => $posts]);
    }
}
