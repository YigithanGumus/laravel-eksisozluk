<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryRequest\EntryStoreRequest;
use App\Models\Entry;
use App\Models\Title;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index()
    {
        $users = Entry::paginate(10);

        return response([
            'data' => $users,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function store($slug, EntryStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $entry = Title::where('slug',$slug)->first();

            if (!$entry)
            {
                return response([
                    'message' => 'Bu entry bulunamamıştır!',
                    'status' => false
                ], Response::HTTP_BAD_REQUEST);
            }

            $entry->entries()->create([
                'user_id'=>Auth::id(),
                'title_id'=>$entry->title_id,
                'content'=>$request->content,
            ]);

            DB::commit();
            return response([
                'message' => 'Entry başarıyla oluşturuldu!',
                'status' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $th) {
            return response([
                'message' => 'Bu entry bulunamamıştır!',
                'error' => $th->getMessage(),
                'error_line' => $th->getLine(),
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
