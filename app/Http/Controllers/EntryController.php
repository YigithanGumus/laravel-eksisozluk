<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntryController extends Controller
{
    public function index()
    {
        $users = Entry::latest()->take(20)->get();

        return response([
            'data' => $users,
            'status' => true
        ], Response::HTTP_OK);
    }
}
