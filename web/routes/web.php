<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ForumController as AdminForumController;
use App\Http\Controllers\Admin\MetricsController as AdminMetricsController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SetController as AdminSetController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Forum\MessageController;
use App\Http\Controllers\Forum\TopicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sets', [SetController::class, 'index'])->name('sets.index');
Route::get('/sets/series/{series:slug}', [SetController::class, 'bySeries'])->name('sets.series');
Route::get('/sets/{set}', [SetController::class, 'show'])->name('sets.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:login');
});

Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [TopicController::class, 'index'])->name('index');

    Route::middleware(['auth', 'not.banned', 'throttle:forum'])->group(function () {
        Route::get('/create', [TopicController::class, 'create'])->name('create');
        Route::post('/', [TopicController::class, 'store'])->name('store');
        Route::get('/{topic}/edit', [TopicController::class, 'edit'])->name('edit');
        Route::put('/{topic}', [TopicController::class, 'update'])->name('update');
        Route::delete('/{topic}', [TopicController::class, 'destroy'])->name('destroy');

        Route::post('/{topic}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
        Route::delete('/{topic}/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    });

    Route::get('/{topic}', [TopicController::class, 'show'])->name('show');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'not.banned', 'admin'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/metrics', [AdminMetricsController::class, 'index'])->name('metrics.index');

        Route::resource('news', AdminNewsController::class)->except(['show']);
        Route::resource('sets', AdminSetController::class)->except(['show']);

        Route::get('/forum', [AdminForumController::class, 'index'])->name('forum.index');
        Route::get('/forum/{topic}/messages', [AdminForumController::class, 'messages'])->name('forum.messages');
        Route::put('/forum/topics/{topic}', [AdminForumController::class, 'updateTopic'])->name('forum.topics.update');
        Route::delete('/forum/{topic}', [AdminForumController::class, 'destroyTopic'])->name('forum.destroy');
        Route::put('/forum/messages/{message}', [AdminForumController::class, 'updateMessage'])->name('forum.messages.update');
        Route::delete('/forum/{topic}/messages/{message}', [AdminForumController::class, 'destroyMessage'])->name('forum.messages.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
        Route::post('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
        Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    });
