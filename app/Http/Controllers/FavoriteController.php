<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Favorite;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Entry $entry)
    {
        $userId = Auth::id();

        $existing = Favorite::where('user_id', $userId)
            ->where('entry_id', $entry->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'entry_id' => $entry->id,
            ]);
            $favorited = true;
        }

        return response([
            'data' => [
                'favorited' => $favorited,
                'favorites_count' => $entry->favorites()->count(),
            ],
            'status' => true
        ], Response::HTTP_OK);
    }
}
