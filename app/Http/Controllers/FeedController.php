<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class FeedController extends Controller
{
    public function following()
    {
        $user = Auth::user();
        $ids = $user->following()->pluck('followed_id');

        $entries = Entry::withMeta()
            ->whereIn('user_id', $ids)
            ->where('is_deleted', false)
            ->latest()
            ->paginate(20);

        return response([
            'data' => $entries,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function top(Request $request)
    {
        $range = $request->get('range', 'week'); // today|yesterday|week
        [$start, $end] = $this->dateRange($range);

        $entriesQuery = Entry::withMeta()
            ->withCount([
                'favorites',
                'votes as up_votes_count' => fn($q) => $q->where('value', 'up'),
                'votes as down_votes_count' => fn($q) => $q->where('value', 'down'),
            ])
            ->where('is_deleted', false)
            ->when($start, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->orderByDesc('favorites_count')
            ->orderByDesc('up_votes_count');

        $entries = $entriesQuery->limit(20)->get();

        $titles = Title::withMeta()
            ->when($start, fn($q) => $q->whereBetween('last_activity_at', [$start, $end]))
            ->orderByDesc('entry_count')
            ->orderByDesc('last_activity_at')
            ->limit(20)
            ->get();

        return response([
            'data' => [
                'entries' => $entries,
                'titles' => $titles,
            ],
            'status' => true
        ], Response::HTTP_OK);
    }

    private function dateRange(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            default => [$now->copy()->subDays(7)->startOfDay(), $now->copy()->endOfDay()],
        };
    }
}
