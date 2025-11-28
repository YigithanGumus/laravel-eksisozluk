<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FeedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Public data
Route::get('titles', [TitleController::class, 'index']);
Route::get('titles/{slug}', [TitleController::class, 'show']);
Route::get('entries', [EntryController::class, 'index']);
Route::get('search', SearchController::class);
Route::get('feed/top', [FeedController::class, 'top']);
Route::get('users/by-username/{username}', [UserController::class, 'showByUsername']);
Route::get('users/{user}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'username' => $request->user()->username,
                'is_moderator' => $request->user()->is_moderator,
            ]
        ], 200);
    });

    Route::post('titles', [TitleController::class, 'store']);
    Route::patch('titles/{uuid}', [TitleController::class, 'update']);
    Route::delete('titles/{uuid}', [TitleController::class, 'destroy']);

    Route::post('entries/{slug}/store', [EntryController::class, 'store']);
    Route::patch('entries/{entry}', [EntryController::class, 'update']);
    Route::delete('entries/{entry}', [EntryController::class, 'destroy']);
    Route::get('entries/{entry}/history', [EntryController::class, 'history']);

    Route::post('entries/{entry}/favorite', [FavoriteController::class, 'toggle']);
    Route::post('entries/{entry}/vote', [VoteController::class, 'vote']);

    Route::post('users/{user}/follow', [FollowController::class, 'toggle']);
    Route::resource('users', UserController::class)->only(['index']);

    Route::post('reports', [ReportController::class, 'store']);
    Route::get('reports', [ReportController::class, 'index']);
    Route::post('reports/{report}/resolve', [ReportController::class, 'resolve']);

    Route::get('feed/following', [FeedController::class, 'following']);

    Route::get('messages', [MessageController::class, 'index']);
    Route::post('messages', [MessageController::class, 'store']);
    Route::post('messages/{message}/read', [MessageController::class, 'markRead']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    Route::post('auth/logout',[AuthController::class,'logout']);
});
