<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::query()
            ->published()
            ->with(['author', 'images'])
            ->latest('published_at')
            ->paginate(10);

        return view('news.index', compact('news'));
    }

    public function show(News $news): View
    {
        abort_unless(
            $news->published_at !== null && $news->published_at->lte(now()),
            404
        );

        $news->load(['author', 'images']);
        $article = $news;

        return view('news.show', compact('article'));
    }
}
