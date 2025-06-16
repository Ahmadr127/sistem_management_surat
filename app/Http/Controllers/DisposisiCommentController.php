<?php

namespace App\Http\Controllers;

use App\Models\DisposisiComment;
use App\Models\Disposisi;
use Illuminate\Http\Request;

class DisposisiCommentController extends Controller
{
    public function index(Disposisi $disposisi)
    {
        $comments = DisposisiComment::with(['user:id,name,foto_profile'])
            ->where('disposisi_id', $disposisi->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'message' => $comment->message,
                    'user_id' => $comment->user_id,
                    'sender_name' => $comment->user->name,
                    'foto_profile' => $comment->user->foto_profile,
                    'created_at' => $comment->created_at,
                    'is_read' => $comment->is_read
                ];
            });

        return response()->json($comments);
    }

    public function store(Request $request, Disposisi $disposisi)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'is_read' => 'boolean'
        ]);

        $comment = DisposisiComment::create([
            'disposisi_id' => $disposisi->id,
            'message' => $validated['message'],
            'user_id' => $validated['user_id'],
            'is_read' => $validated['is_read'] ?? false
        ]);

        $comment->load('user:id,name,foto_profile');

        return response()->json([
            'id' => $comment->id,
            'message' => $comment->message,
            'user_id' => $comment->user_id,
            'sender_name' => $comment->user->name,
            'foto_profile' => $comment->user->foto_profile,
            'created_at' => $comment->created_at,
            'is_read' => $comment->is_read
        ], 201);
    }
}
