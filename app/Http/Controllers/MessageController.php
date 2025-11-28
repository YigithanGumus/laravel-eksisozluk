<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['sender:id,name,username', 'receiver:id,name,username'])
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->paginate(30);

        return response([
            'data' => $messages,
            'status' => true,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'receiver_username' => 'nullable|string',
            'content' => 'required|string|max:5000',
        ]);

        $receiverId = $data['receiver_id'] ?? null;
        if (!$receiverId && !empty($data['receiver_username'])) {
            $receiverId = User::where('username', $data['receiver_username'])->value('id');
        }

        if (!$receiverId) {
            return response([
                'message' => 'Alıcı bulunamadı.',
                'status' => false,
            ], Response::HTTP_BAD_REQUEST);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'content' => $data['content'],
        ]);

        return response([
            'data' => $message->load(['receiver:id,name,username', 'sender:id,name,username']),
            'status' => true,
            'message' => 'Mesaj gönderildi.',
        ], Response::HTTP_CREATED);
    }

    public function markRead(Message $message)
    {
        if ($message->receiver_id !== Auth::id()) {
            return response([
                'message' => 'Yetkisiz erişim.',
                'status' => false,
            ], Response::HTTP_FORBIDDEN);
        }

        $message->update(['read_at' => now()]);

        return response([
            'data' => $message,
            'status' => true,
        ], Response::HTTP_OK);
    }
}
