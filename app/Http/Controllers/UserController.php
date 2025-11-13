<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function users()
    {
        $users = User::paginate(20);

        return response([
            'data' => $users,
            'message' => 'Kullanıcılar başarıyla getirildi.',
            'status' => true
        ], Response::HTTP_OK);
    }
}
