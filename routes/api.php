<?php

use App\Http\Controllers\EntryController;
use App\Http\Controllers\TitleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('auth/login', [AuthController::class, 'login']);

Route::post('auth/register', [AuthController::class, 'register']);

Route::get('/entries', [EntryController::class,'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ]
        ], 200);
    });


    Route::resource('users', UserController::class);

    Route::resource('titles', TitleController::class);

    Route::prefix('entries')->group(function () {

        Route::post('{slug}/store',[EntryController::class,'store']);

    });

    Route::post('auth/logout',[AuthController::class,'logout']);
});

