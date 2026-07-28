<?php

namespace App\Policies;

use App\Models\ForumTopic;
use App\Models\User;

class ForumTopicPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ForumTopic $forumTopic): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isBanned();
    }

    public function update(User $user, ForumTopic $forumTopic): bool
    {
        return $user->id === $forumTopic->user_id || $user->canModerateForum();
    }

    public function delete(User $user, ForumTopic $forumTopic): bool
    {
        return $user->id === $forumTopic->user_id || $user->canModerateForum();
    }
}
