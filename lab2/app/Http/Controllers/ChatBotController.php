<?php

use App\Models\InventoryController;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatBotController extends InventoryController
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function __invoke(Request $request)
    {
        $userMessage = $request->input('message', 'Hello!');

        $aiResponse = $this->gemini->ask($userMessage);

        return response()->json([
            'status' => 'success',
            'reply' => $aiResponse
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Your GeminiService call from the previous step
        $response = $this->gemini->ask($request->message);

        return response()->json([
            'reply' => $response
        ]);
    }
}
