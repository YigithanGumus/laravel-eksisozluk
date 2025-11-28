<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        $followerId = Auth::id();

        if ($user->id === $followerId) {
            return response([
                'message' => 'Kendinizi takip edemezsiniz.',
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }

        $existing = Follow::where('follower_id', $followerId)
            ->where('followed_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Follow::create([
                'follower_id' => $followerId,
                'followed_id' => $user->id,
            ]);
            $following = true;
        }

        return response([
            'data' => [
                'following' => $following,
                'followers_count' => $user->followers()->count(),
            ],
            'status' => true
        ], Response::HTTP_OK);
    }
}
