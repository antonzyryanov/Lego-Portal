<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreTopicRequest;
use App\Http\Requests\Forum\UpdateTopicRequest;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = ForumTopic::query()
            ->with('user')
            ->withCount('messages')
            ->latest()
            ->paginate(15);

        return view('forum.index', compact('topics'));
    }

    public function create(): View
    {
        $this->authorize('create', ForumTopic::class);

        return view('forum.create');
    }

    public function store(StoreTopicRequest $request): RedirectResponse
    {
        $topic = ForumTopic::query()->create([
            'user_id' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString(),
        ]);

        $request->user()->incrementRating(5);

        return redirect()
            ->route('forum.show', $topic)
            ->with('status', 'Topic created.');
    }

    public function show(ForumTopic $topic): View
    {
        $topic->load(['user', 'messages.user']);
        $messages = $topic->messages;

        return view('forum.show', compact('topic', 'messages'));
    }

    public function edit(ForumTopic $topic): View
    {
        $this->authorize('update', $topic);

        return view('forum.edit', compact('topic'));
    }

    public function update(UpdateTopicRequest $request, ForumTopic $topic): RedirectResponse
    {
        $topic->update($request->validated());

        return redirect()
            ->route('forum.show', $topic)
            ->with('status', 'Topic updated.');
    }

    public function destroy(ForumTopic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        $topic->delete();

        return redirect()
            ->route('forum.index')
            ->with('status', 'Topic deleted.');
    }
}
