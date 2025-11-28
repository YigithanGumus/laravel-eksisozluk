<?php

namespace App\Http\Controllers;

use App\Http\Requests\TitleRequest\TitleStoreRequest;
use App\Http\Requests\TitleRequest\TitleUpdateRequest;
use App\Models\Title;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TitleController extends Controller
{
    public function index()
    {
        $titles = Title::withMeta()
            ->when(request('q'), function ($query) {
                $query->where('title', 'like', '%' . request('q') . '%');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_activity_at')
            ->paginate(20);

        return response([
            'data' => $titles,
            'message' => 'Başlıklar getirildi.',
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

            $entry = $title->entries()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            $title->update([
                'entry_count' => 1,
                'last_entry_id' => $entry->id,
                'last_activity_at' => $entry->created_at,
            ]);

            DB::commit();
            return response([
                'message' => 'Başlık ve entry oluşturuldu.',
                'data' => [
                    'title' => $title->load('user'),
                    'entry' => $entry->load('user'),
                ],
                'status' => true
            ], Response::HTTP_CREATED);
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

    public function update($uuid, TitleUpdateRequest $request)
    {
        if (!Auth::user()->is_moderator) {
            return response([
                'message' => 'Bu işlem için moderatör yetkisi gerekli.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {
            Title::where('uuid', $uuid)->update([
                'is_locked' => $request->is_locked,
                'is_pinned' => $request->is_pinned,
                'lock_reason' => $request->lock_reason,
                'pin_reason' => $request->pin_reason,
            ]);
            DB::commit();
            return response([
                'message' => 'Başlık güncellendi.',
                'status' => true
            ], Response::HTTP_CREATED);
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

    public function destroy($uuid)
    {
        if (!Auth::user()->is_moderator) {
            return response([
                'message' => 'Bu işlem için moderatör yetkisi gerekli.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {
            Title::where('uuid', $uuid)->delete();
            DB::commit();

            return response([
                'message' => 'Başlık ve bağlı entryler silindi.',
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

    public function show($slug)
    {
        $title = Title::with(['user:id,name,username'])
            ->where('slug', $slug)
            ->firstOrFail();

        $entries = $title->entries()
            ->withMeta()
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response([
            'data' => [
                'title' => $title,
                'entries' => $entries,
            ],
            'status' => true
        ], Response::HTTP_OK);
    }
}
