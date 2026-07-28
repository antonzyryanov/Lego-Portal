<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BanUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $users = User::query()
            ->with('role')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function promote(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Cannot change an admin role.']);
        }

        $moderatorRole = Role::query()->where('slug', Role::MODERATOR)->firstOrFail();
        $user->update(['role_id' => $moderatorRole->id]);

        return back()->with('status', $user->name.' is now a moderator.');
    }

    public function demote(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Cannot demote an admin.']);
        }

        $userRole = Role::query()->where('slug', Role::USER)->firstOrFail();
        $user->update(['role_id' => $userRole->id]);

        return back()->with('status', $user->name.' is now a regular user.');
    }

    public function ban(BanUserRequest $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Cannot ban an admin.']);
        }

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot ban yourself.']);
        }

        $days = $request->integer('days');
        $user->update([
            'banned_until' => now()->addDays($days),
        ]);

        return back()->with('status', $user->name.' banned for '.$days.' day(s).');
    }

    public function unban(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->canModerateForum(), 403);

        $user->update(['banned_until' => null]);

        return back()->with('status', $user->name.' has been unbanned.');
    }
}
