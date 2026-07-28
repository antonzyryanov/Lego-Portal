<?php

namespace App\Http\Controllers;

use App\Models\LegoSet;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetController extends Controller
{
    public function index(Request $request): View
    {
        $seriesList = Series::query()->orderBy('name')->get();

        $query = LegoSet::query()->with('series')->orderByDesc('release_date');

        if ($request->filled('series')) {
            $query->whereHas('series', function ($q) use ($request) {
                $q->where('slug', $request->string('series')->toString());
            });
        }

        if ($request->filled('series_id')) {
            $query->where('series_id', $request->integer('series_id'));
        }

        $sets = $query->paginate(12)->withQueryString();

        return view('sets.index', [
            'sets' => $sets,
            'seriesList' => $seriesList,
            'series' => null,
        ]);
    }

    public function bySeries(Series $series): View
    {
        $seriesList = Series::query()->orderBy('name')->get();

        $sets = LegoSet::query()
            ->with('series')
            ->where('series_id', $series->id)
            ->orderByDesc('release_date')
            ->paginate(12);

        return view('sets.index', [
            'sets' => $sets,
            'seriesList' => $seriesList,
            'series' => $series,
        ]);
    }

    public function show(LegoSet $set): View
    {
        $set->load('series');
        $seriesList = Series::query()->orderBy('name')->get();

        return view('sets.show', [
            'set' => $set,
            'seriesList' => $seriesList,
            'series' => $set->series,
        ]);
    }
}
