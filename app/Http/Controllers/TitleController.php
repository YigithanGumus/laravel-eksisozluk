<?php

namespace App\Http\Controllers;

use App\Http\Requests\TitleRequest\TitleStoreRequest;
use App\Http\Requests\TitleRequest\TitleUpdateRequest;
use App\Models\Title;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TitleController extends Controller
{
    public function index()
    {
        $users = Title::latest()->take(20)->get();

        return response([
            'data' => $users,
            'message' => 'Kullanıcılar başarıyla getirildi.',
            'status' => true
        ], Response::HTTP_OK);
    }

    public function store(TitleStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $title = Title::create([
                'user_id'=>Auth::id(),
                'title'=>$request->title,
            ]);

            $title->entries()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            DB::commit();
            return response([
                'message' => 'Başlık ve Entry başarıyla oluşturuldu!',
                'status' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $th) {
            return response([
                'message' => 'Error!',
                'error' => $th->getMessage(),
                'error_line' => $th->getLine(),
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($uuid)
    {
        DB::beginTransaction();
        try {
            Title::where('uuid', $uuid)->delete();
            DB::commit();

            return response([
                'message' => 'Başlık ve bağlı entryler başarıyla silindi!',
                'status' => true
            ], Response::HTTP_OK);
        } catch (Exception $th) {
            DB::rollBack();
            return response([
                'message' => 'Error!',
                'error' => $th->getMessage(),
                'error_line' => $th->getLine(),
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($uuid)
    {
        $entry = Title::with('entries')->where('uuid', $uuid)->firstOrFail();

        return response([
            'data' => $entry,
            'status' => true
        ], Response::HTTP_OK);
    }
}
