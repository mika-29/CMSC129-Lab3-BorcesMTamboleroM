<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Support\Facades\Http;

class GeminiService 
{

    public function ask(string $message, array $history = []): string
    {
        try {
            $inventory = Inventory::all()->map(function ($item) {
                return [
                    'name' => $item->name,
                    'category' => $item->category,
                    'quantity' => $item->quantity,
                    'minimum_stock'   => $item->minimum_stock,
                    'expiration_date' => $item->expiration_date ?? 'No expiration',
                    'status' => $item->quantity <= 0 ? 'Out of Stock' 
                                                    : ($item->is_low_stock ? 'Low Stock' : 'In Stock'),
                ];
            });

            $systemPrompt = "You are a helpful inventory assistant for an Emergency Inventory Management System.
You have access to the current inventory data below. Answer questions accurately based on this data.
Be concise, friendly, and helpful. If asked something unrelated to inventory, politely redirect the conversation.

Current Inventory Data:
" . json_encode($inventory, JSON_PRETTY_PRINT) . "

You can answer questions like:
- How many items are in stock?
- Which items are low or out of stock?
- What items are expiring soon?
- How many items are in a specific category?
- What is the total quantity of a specific item?
- How many critical items are there?";

            // Build conversation history
            $contents = [];
            foreach ($history as $msg) {
                $contents[] = [
                    'role'  => $msg['role'],
                    'parts' => [['text' => $msg['text']]],
                ];
            }

            // Add current user message
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $message]],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => $contents,
            ]);

            if ($response->failed()) {
                return 'Sorry, I am unavailable right now. Please try again later.';
            }

            return $response->json('candidates.0.content.parts.0.text')
                ?? 'Sorry, I could not generate a response.';

        } catch (\Exception $e) {
            return 'Sorry, something went wrong. Please try again.';
        }
    }
}
    