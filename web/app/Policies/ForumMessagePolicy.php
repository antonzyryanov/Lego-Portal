<?php

namespace App\Policies;

use App\Models\ForumMessage;
use App\Models\User;

class ForumMessagePolicy
{
    public function create(User $user): bool
    {
        return ! $user->isBanned();
    }

    public function update(User $user, ForumMessage $forumMessage): bool
    {
        return $user->id === $forumMessage->user_id || $user->canModerateForum();
    }

    public function delete(User $user, ForumMessage $forumMessage): bool
    {
        return $user->id === $forumMessage->user_id || $user->canModerateForum();
    }
}
