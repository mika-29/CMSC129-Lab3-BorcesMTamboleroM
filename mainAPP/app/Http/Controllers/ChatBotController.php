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
            'history' => 'nullable|array',
        ]);

        $history = collect($request->history ?? [])->takeLast(10)->values()->toArray();

        $reply = $this->gemini->ask($request->message, $history);

        return response()->json(['reply' => $reply]);
    }
}