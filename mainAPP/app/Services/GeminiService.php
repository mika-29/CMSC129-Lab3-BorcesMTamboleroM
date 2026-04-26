<?php

namespace App\Services;

use Gemini\Data\Blob;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function __construct(
        protected PromptService $prompt,
        protected FunctionCallService $functionCall
    ) {}

    public function ask(string $message, array $history = []): string
    {
        try {
             // Handle simple greetings locally — no API call needed
            $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'];
            $trimmed = strtolower(trim($message));

            if (in_array($trimmed, $greetings)) {
                return "Hello! I'm your inventory assistant. How can I help you?";
            }

            $model = Gemini::generativeModel('gemini-2.5-flash')
                ->withSystemInstruction($this->prompt->systemInstruction())
                ->withTool($this->prompt->getTools());

            $contents = [];

            foreach ($history as $msg) {
                $role = $msg['role'] === 'model' ? Role::MODEL : Role::USER;
                $contents[] = Content::parse($msg['text'], $role);
            }

            $contents[] = Content::parse($message, Role::USER);

            $response = $model->generateContent(...$contents);

            // Handle function calls (loop to support chained calls)
            $maxIterations = 5;
            $i = 0;

            while ($i++ < $maxIterations) {
                $functionCallPart = null;

                foreach ($response->parts() as $part) {
                    if ($part->functionCall !== null) {
                        $functionCallPart = $part->functionCall;
                        break;
                    }
                }

                if ($functionCallPart === null) {
                    break; // No function call, we have our final response
                }

                Log::info('Gemini function call', ['name' => $functionCallPart->name, 'args' => $functionCallPart->args]);

                $result = $this->functionCall->execute($functionCallPart);

                // Append the model's function call turn
                $contents[] = new Content(
                    parts: $response->parts(),
                    role: Role::MODEL
                );

                // Append the model's function call turn
                $contents[] = new Content(
                    parts: $response->parts(),
                    role: Role::MODEL
                );

                // Append the function response turn — use array format instead of Part::fromFunctionResponse
                $contents[] = Content::parse(
                    json_encode([
                        'functionResponse' => [
                            'name' => $functionCallPart->name,
                            'response' => json_decode($result, true),
                        ]
                    ]),
                    Role::USER
                );

                $response = $model->generateContent(...$contents);
            }

            return $response->text() ?? "Sorry, I couldn't process that.";

        } catch (\Exception $e) {
            Log::error('GeminiService error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return "Sorry, I couldn't process that.";
        }
    }
}
