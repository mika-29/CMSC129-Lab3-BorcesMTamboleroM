<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class GeminiService
{
    /**
     * Send a message to Gemini and get a response.
     */
    public function ask(string $message): string
    {
        try {

            $result = Gemini::geminiPro()->generateContent($message);

            return $result->text();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
