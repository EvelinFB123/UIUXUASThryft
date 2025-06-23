<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $user = Auth::user();
        $userMessage = strtolower($request->input('input'));

        // Simpan pesan user ke database
        ChatMessage::create([
            'user_id' => $user->id,
            'message' => $userMessage,
            'is_user' => true,
        ]);

        try {
            $response = Http::post('http://127.0.0.1:5000/chat', [
                'message' => $userMessage
            ]);

            if ($response->successful()) {
                $botReply = $response->json()['response'];

                // Simpan balasan bot ke database
                ChatMessage::create([
                    'user_id' => $user->id,
                    'message' => $botReply,
                    'is_user' => false,
                ]);

                return response()->json(['reply' => $botReply]);
            } else {
                $fallbackReply = 'Maaf, saya tidak bisa merespon saat ini.';

                ChatMessage::create([
                    'user_id' => $user->id,
                    'message' => $fallbackReply,
                    'is_user' => false,
                ]);

                return response()->json(['reply' => $fallbackReply], 500);
            }
        } catch (\Exception $e) {
            $offlineReply = 'Maaf, chatbot sedang offline.';

            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $offlineReply,
                'is_user' => false,
            ]);

            return response()->json(['reply' => $offlineReply], 500);
        }
    }

    public function ai()
    {
        // Ambil chat history user yang login
        $messages = Auth::user()->chatMessages()->orderBy('created_at')->get();

        return view('ai', compact('messages')); // kirim ke blade
    }
}
