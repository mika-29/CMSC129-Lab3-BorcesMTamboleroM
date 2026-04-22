<?php
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
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
}
