<?php

namespace App\Http\Controllers;

use App\Models\LegoSet;
use App\Models\News;
use App\Models\Series;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $series = Series::query()->orderBy('name')->get();
        $latestNews = News::query()
            ->published()
            ->with('author')
            ->latest('published_at')
            ->limit(5)
            ->get();
        $featuredSets = LegoSet::query()
            ->with('series')
            ->latest('release_date')
            ->limit(8)
            ->get();

        return view('home', compact('series', 'latestNews', 'featuredSets'));
    }
}
