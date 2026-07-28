<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Models\News;
use App\Models\NewsImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', News::class);

        $query = News::query()->with('author')->latest('published_at');

        if ($request->filled('from')) {
            $query->whereDate('published_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('published_at', '<=', $request->date('to'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', '%'.$q.'%')
                    ->orWhere('body', 'like', '%'.$q.'%');
            });
        }

        $news = $query->paginate(20)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.news.create');
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $news = News::query()->create([
                'author_id' => $request->user()->id,
                'title' => $request->string('title')->toString(),
                'body' => $request->string('body')->toString(),
                'published_at' => $request->input('published_at') ?: now(),
            ]);

            $this->storeUploadedImages($news, $request);
        });

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article created.');
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);
        $news->load('images');
        $article = $news;

        return view('admin.news.edit', compact('article'));
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        DB::transaction(function () use ($request, $news) {
            $news->update($request->safe()->only(['title', 'body', 'published_at']));
            $this->storeUploadedImages($news, $request);
        });

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article updated.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'News article deleted.');
    }

    protected function storeUploadedImages(News $news, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sort = (int) $news->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $path = $file->store('news', 'public');
            $sort++;

            NewsImage::query()->create([
                'news_id' => $news->id,
                'path' => Storage::disk('public')->url($path),
                'sort_order' => $sort,
            ]);
        }
    }
}
