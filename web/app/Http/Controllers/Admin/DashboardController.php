<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumMessage;
use App\Models\ForumTopic;
use App\Models\LegoSet;
use App\Models\News;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'summary' => [
                'users' => User::query()->count(),
                'sets' => LegoSet::query()->count(),
                'news' => News::query()->count(),
                'topics' => ForumTopic::query()->count(),
                'messages' => ForumMessage::query()->count(),
            ],
        ]);
    }
}
