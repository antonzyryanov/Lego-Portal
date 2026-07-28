<?php

namespace App\Policies;

use App\Models\LegoSet;
use App\Models\User;

class LegoSetPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, LegoSet $legoSet): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canCreateContent();
    }

    public function update(User $user, LegoSet $legoSet): bool
    {
        return $user->canManageContent();
    }

    public function delete(User $user, LegoSet $legoSet): bool
    {
        return $user->canManageContent();
    }
}
