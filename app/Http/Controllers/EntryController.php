<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryRequest\EntryStoreRequest;
use App\Models\Entry;
use App\Models\EntryEdit;
use App\Models\Notification;
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
        $entries = Entry::withMeta()
            ->where('is_deleted', false)
            ->latest()
            ->paginate(20);

        return response([
            'data' => $entries,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function store($slug, EntryStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $title = Title::where('slug', $slug)->first();

            if (!$title) {
                return response([
                    'message' => 'Başlık bulunamadı.',
                    'status' => false
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($title->is_locked && !Auth::user()->is_moderator) {
                return response([
                    'message' => 'Başlık kilitli, entry eklenemez.',
                    'status' => false
                ], Response::HTTP_FORBIDDEN);
            }

            $entry = $title->entries()->create([
                'user_id'=>Auth::id(),
                'content'=>$request->content,
            ]);

            $title->update([
                'entry_count' => $title->entries()->count(),
                'last_entry_id' => $entry->id,
                'last_activity_at' => $entry->created_at,
            ]);

            $this->notifyMentions($entry);

            DB::commit();
            return response([
                'message' => 'Entry oluşturuldu.',
                'data' => $entry->load('user'),
                'status' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $th) {
            DB::rollBack();
            return response([
                'message' => 'Entry eklenemedi.',
                'error' => $th->getMessage(),
                'error_line' => $th->getLine(),
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Entry $entry, Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        if ($entry->is_locked && !Auth::user()->is_moderator) {
            return response([
                'message' => 'Kilitli entry düzenlenemez.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        if ($entry->user_id !== Auth::id() && !Auth::user()->is_moderator) {
            return response([
                'message' => 'Düzenleme yetkiniz yok.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {
            EntryEdit::create([
                'entry_id' => $entry->id,
                'user_id' => Auth::id(),
                'content_before' => $entry->content,
                'content_after' => $request->content,
            ]);

            $entry->update([
                'content' => $request->content,
            ]);

            $this->notifyMentions($entry);

            DB::commit();
            return response([
                'data' => $entry->load('user'),
                'status' => true,
                'message' => 'Entry güncellendi.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response([
                'message' => 'Güncellenemedi.',
                'error' => $e->getMessage(),
                'status' => false
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(Entry $entry, Request $request)
    {
        if ($entry->is_locked && !Auth::user()->is_moderator) {
            return response([
                'message' => 'Kilitli entry silinemez.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        if ($entry->user_id !== Auth::id() && !Auth::user()->is_moderator) {
            return response([
                'message' => 'Silme yetkiniz yok.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        $reason = $request->get('reason');

        $entry->update([
            'is_deleted' => true,
            'deleted_reason' => $reason,
            'content' => '[silindi]',
        ]);

        return response([
            'message' => 'Entry silindi.',
            'status' => true
        ], Response::HTTP_OK);
    }

    public function history(Entry $entry)
    {
        if ($entry->user_id !== Auth::id() && !Auth::user()->is_moderator) {
            return response([
                'message' => 'Geçmişi görüntüleme yetkiniz yok.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        $edits = $entry->edits()->with('user:id,name,username')->latest()->get();

        return response([
            'data' => $edits,
            'status' => true
        ], Response::HTTP_OK);
    }

    protected function notifyMentions(Entry $entry): void
    {
        preg_match_all('/@([A-Za-z0-9_\\.\\-]+)/', $entry->content, $matches);
        $usernames = collect($matches[1] ?? [])->unique();
        if ($usernames->isEmpty()) {
            return;
        }

        $mentionedUsers = \App\Models\User::whereIn('username', $usernames)->get();
        foreach ($mentionedUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'mention',
                'data' => [
                    'entry_id' => $entry->id,
                    'title_slug' => $entry->title->slug,
                    'from_user' => Auth::user()->only(['id', 'username', 'name']),
                ],
            ]);
        }
    }
}
