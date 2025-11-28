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
        $user->loadCount(['followers', 'following'])
            ->load(['titles:id,user_id,title,slug', 'entries' => function ($q) {
                $q->withMeta()->where('is_deleted', false)->limit(20);
            }]);

        $user->is_following = Auth::user()
            ? Auth::user()->following()->where('followed_id', $user->id)->exists()
            : false;

        return response([
            'data' => $user,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function showByUsername($username)
    {
        $user = User::where('username', $username)
            ->withCount(['followers', 'following'])
            ->with(['titles:id,user_id,title,slug', 'entries' => function ($q) {
                $q->withMeta()->where('is_deleted', false)->limit(20);
            }])
            ->firstOrFail();

        $user->is_following = Auth::user()
            ? Auth::user()->following()->where('followed_id', $user->id)->exists()
            : false;

        return response([
            'data' => $user,
            'status' => true
        ], Response::HTTP_OK);
    }
}
