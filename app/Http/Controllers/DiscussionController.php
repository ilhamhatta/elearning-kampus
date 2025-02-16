<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'content' => 'required|string',
        ]);

        // Buat diskusi baru
        $discussion = Discussion::create([
            'course_id' => $request->course_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json($discussion, 201);
    }

    /**
     * Membalas diskusi.
     */
    public function reply(Request $request, $discussionId)
    {
        // Validasi input
        $request->validate([
            'content' => 'required|string',
        ]);

        // Cari diskusi yang akan dibalas
        $discussion = Discussion::findOrFail($discussionId);

        // Buat balasan
        $reply = Reply::create([
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json($reply, 201);
    }
}
