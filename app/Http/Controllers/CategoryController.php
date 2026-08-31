<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('categories.index', ['categories' => $categories]);
    }

    public function show(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('categories.show', ['category' => $category, 'posts' => $posts]);
    }
}
