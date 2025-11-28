<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function vote(Request $request, Entry $entry)
    {
        $data = $request->validate([
            'value' => 'required|in:up,down',
        ]);

        $vote = Vote::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'entry_id' => $entry->id,
            ],
            ['value' => $data['value']]
        );

        return response([
            'data' => [
                'vote' => $vote->value,
                'up_votes_count' => $entry->votes()->where('value', 'up')->count(),
                'down_votes_count' => $entry->votes()->where('value', 'down')->count(),
            ],
            'status' => true
        ], Response::HTTP_OK);
    }
}
