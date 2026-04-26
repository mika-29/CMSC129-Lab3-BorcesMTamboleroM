<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = $request->message;

        $history = session()->get('chat_history', []);

        $reply = $this->gemini->ask($message, $history);

        $history[] = ['role' => 'user', 'text' => $message];
        $history[] = ['role' => 'model', 'text' => $reply];

        $history = array_slice($history, -5);

        session()->put('chat_history', $history);

        return response()->json(['reply' => $reply]);
    }
}
