<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreMessageRequest;
use App\Http\Requests\Forum\UpdateMessageRequest;
use App\Models\ForumMessage;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request, ForumTopic $topic): RedirectResponse
    {
        ForumMessage::query()->create([
            'topic_id' => $topic->id,
            'user_id' => $request->user()->id,
            'body' => $request->string('body')->toString(),
        ]);

        $request->user()->incrementRating(1);

        return redirect()
            ->route('forum.show', $topic)
            ->with('status', 'Message posted.');
    }

    public function update(UpdateMessageRequest $request, ForumMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);
        $message->update($request->validated());

        return redirect()
            ->route('forum.show', $message->topic_id)
            ->with('status', 'Message updated.');
    }

    public function destroy(ForumTopic $topic, ForumMessage $message): RedirectResponse
    {
        abort_unless($message->topic_id === $topic->id, 404);
        $this->authorize('delete', $message);

        $message->delete();

        return redirect()
            ->route('forum.show', $topic)
            ->with('status', 'Message deleted.');
    }
}
