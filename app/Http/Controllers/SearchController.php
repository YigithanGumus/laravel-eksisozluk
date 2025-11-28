<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->get('q', '');

        $titles = Title::withMeta()
            ->where('title', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();

        $entries = Entry::withMeta()
            ->where('content', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();

        return response([
            'data' => [
                'titles' => $titles,
                'entries' => $entries,
            ],
            'status' => true
        ], Response::HTTP_OK);
    }
}
