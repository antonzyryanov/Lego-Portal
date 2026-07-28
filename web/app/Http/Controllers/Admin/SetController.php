<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegoSet\StoreLegoSetRequest;
use App\Http\Requests\LegoSet\UpdateLegoSetRequest;
use App\Models\LegoSet;
use App\Models\Series;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LegoSet::class);

        $query = LegoSet::query()->with('series')->orderBy('name');

        if ($request->filled('series_id')) {
            $query->where('series_id', $request->integer('series_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('release_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('release_date', '<=', $request->date('to'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('article_number', 'like', '%'.$q.'%');
            });
        }

        $sets = $query->paginate(20)->withQueryString();
        $seriesList = Series::query()->orderBy('name')->get();

        return view('admin.sets.index', compact('sets', 'seriesList'));
    }

    public function create(): View
    {
        $this->authorize('create', LegoSet::class);

        $seriesList = Series::query()->orderBy('name')->get();

        return view('admin.sets.create', compact('seriesList'));
    }

    public function store(StoreLegoSetRequest $request): RedirectResponse
    {
        LegoSet::query()->create($request->validated());

        return redirect()
            ->route('admin.sets.index')
            ->with('status', 'LEGO set created.');
    }

    public function edit(LegoSet $set): View
    {
        $this->authorize('update', $set);

        $seriesList = Series::query()->orderBy('name')->get();

        return view('admin.sets.edit', compact('set', 'seriesList'));
    }

    public function update(UpdateLegoSetRequest $request, LegoSet $set): RedirectResponse
    {
        $set->update($request->validated());

        return redirect()
            ->route('admin.sets.index')
            ->with('status', 'LEGO set updated.');
    }

    public function destroy(LegoSet $set): RedirectResponse
    {
        $this->authorize('delete', $set);
        $set->delete();

        return redirect()
            ->route('admin.sets.index')
            ->with('status', 'LEGO set deleted.');
    }
}
