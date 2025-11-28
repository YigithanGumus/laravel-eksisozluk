<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['Kullanıcı veya şifre hatalı.']
            ], Response::HTTP_BAD_REQUEST);
        }

        $userToken = $user->createToken('user-token')->plainTextToken;

        return response([
            'token' => $userToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'is_moderator' => $user->is_moderator,
            ]
        ], Response::HTTP_OK);
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->validated());
            DB::commit();
            return response([
                'message' => 'Kullanıcı oluşturuldu.',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Kullanıcı oluşturulurken hata oluştu.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Başarıyla çıkış yapıldı.',
        ], Response::HTTP_OK);
    }
}
