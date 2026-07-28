<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\UpdateMessageRequest;
use App\Http\Requests\Forum\UpdateTopicRequest;
use App\Models\ForumMessage;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $topics = ForumTopic::query()
            ->with('user')
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        return view('admin.forum.index', compact('topics'));
    }

    public function messages(Request $request, ForumTopic $topic): View
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $topic->load('user');
        $messages = $topic->messages()->with('user')->latest()->paginate(20);

        return view('admin.forum.messages', compact('topic', 'messages'));
    }

    public function updateTopic(UpdateTopicRequest $request, ForumTopic $topic): RedirectResponse
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $topic->update($request->validated());

        return back()->with('status', 'Topic updated.');
    }

    public function destroyTopic(Request $request, ForumTopic $topic): RedirectResponse
    {
        abort_unless($request->user()?->canModerateForum(), 403);
        $this->authorize('delete', $topic);

        $topic->delete();

        return redirect()
            ->route('admin.forum.index')
            ->with('status', 'Topic deleted.');
    }

    public function updateMessage(UpdateMessageRequest $request, ForumMessage $message): RedirectResponse
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $message->update($request->validated());

        return back()->with('status', 'Message updated.');
    }

    public function destroyMessage(Request $request, ForumTopic $topic, ForumMessage $message): RedirectResponse
    {
        abort_unless($request->user()?->canModerateForum(), 403);
        abort_unless($message->topic_id === $topic->id, 404);
        $this->authorize('delete', $message);

        $message->delete();

        return back()->with('status', 'Message deleted.');
    }
}
