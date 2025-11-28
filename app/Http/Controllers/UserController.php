<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['followers', 'following'])
            ->when(request('username'), function ($q) {
                $q->where('username', request('username'));
            })
            ->when(request('search'), function ($q) {
                $q->where(function ($sub) {
                    $sub->where('username', 'like', '%' . request('search') . '%')
                        ->orWhere('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%');
                });
            })
            ->paginate(20);

        return response([
            'data' => $users,
            'message' => 'Kullanıcılar getirildi.',
            'status' => true
        ], Response::HTTP_OK);
    }

    public function show(User $user)
    {
        $data = $this->buildProfilePayload($user);

        return response([
            'data' => $data,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function showByUsername($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $data = $this->buildProfilePayload($user);

        return response([
            'data' => $data,
            'status' => true
        ], Response::HTTP_OK);
    }

    protected function buildProfilePayload(User $user)
    {
        $user->loadCount(['followers', 'following', 'entries', 'titles', 'favorites']);
        $user->load([
            'titles' => fn($q) => $q->latest()->limit(10),
            'entries' => function ($q) {
                $q->withMeta()->where('is_deleted', false)->latest()->limit(10);
            },
            'favorites' => function ($q) {
                $q->with(['title:id,slug,title'])->latest()->limit(10);
            },
        ]);

        $user->is_following = Auth::user()
            ? Auth::user()->following()->where('followed_id', $user->id)->exists()
            : false;

        return [
            'user' => $user,
            'stats' => [
                'entries_count' => $user->entries_count,
                'titles_count' => $user->titles_count,
                'favorites_count' => $user->favorites_count,
                'followers_count' => $user->followers_count,
                'following_count' => $user->following_count,
            ],
            'recent_entries' => $user->entries,
            'recent_titles' => $user->titles,
            'recent_favorites' => $user->favorites,
        ];
    }
}
