<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function __construct(protected InventoryQueryService $inventoryQuery)
    {
    }

    public function ask(string $message, array $history = []): string
    {
        try {
            $model = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->withSystemInstruction(Content::parse('You are a helpful inventory assistant. Use the available functions to get inventory data and provide accurate responses.'))
                ->withTool($this->createInventoryTool());

            $contents = [];
            foreach ($history as $msg) {
                $role = $msg['role'] === 'model' ? Role::MODEL : Role::USER;
                $contents[] = Content::parse($msg['text'], $role);
            }

            $contents[] = Content::parse($message, Role::USER);

            $response = $model->generateContent(...$contents);

            // Handle function calls
            if ($response->parts()[0]->functionCall !== null) {
                $functionCall = $response->parts()[0]->functionCall;
                $functionResponse = $this->handleFunctionCall($functionCall);

                // Send the function response back to continue the conversation
                $contents[] = Content::parse($functionResponse, Role::MODEL);
                $response = $model->generateContent(...$contents);
            }

            $reply = trim($response->text() ?? '');

            if ($reply === '') {
                Log::warning('Gemini returned empty reply.');
                return 'Sorry, I could not generate a response. Please try again.';
            }

            return $reply;
        } catch (\Throwable $e) {
            Log::error('Gemini chatbot error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (str_contains(strtolower($e->getMessage()), 'quota') || str_contains(strtolower($e->getMessage()), 'billing')) {
                return 'Sorry, I am currently unavailable due to service limitations. Please try again later.';
            }

            return 'Sorry, I am unavailable right now. Please try again later.';
        }
    }

    protected function createInventoryTool(): Tool
    {
        return new Tool(functionDeclarations: [
            new FunctionDeclaration(
                name: 'get_stock_summary',
                description: 'Get a summary of total inventory items, quantities, and stock status',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [],
                    required: []
                )
            ),
            new FunctionDeclaration(
                name: 'get_low_stock_items',
                description: 'Get items that are low on stock or out of stock',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [],
                    required: []
                )
            ),
            new FunctionDeclaration(
                name: 'get_expiring_items',
                description: 'Get items that are near expiry or expiring soon within a specified number of days',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'days' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Number of days to check for expiring items (default 30)'
                        ),
                    ],
                    required: []
                )
            ),
            new FunctionDeclaration(
                name: 'get_category_count',
                description: 'Get the count and total quantity of items in a specific category',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'category' => new Schema(
                            type: DataType::STRING,
                            description: 'The category name to search for'
                        ),
                    ],
                    required: ['category']
                )
            ),
            new FunctionDeclaration(
                name: 'get_item_quantity',
                description: 'Get the quantity of a specific item by name',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'item_name' => new Schema(
                            type: DataType::STRING,
                            description: 'The item name to search for'
                        ),
                    ],
                    required: ['item_name']
                )
            ),
            new FunctionDeclaration(
                name: 'get_all_inventory_items',
                description: 'Get a full list of all items in the inventory including their names, categories, and current stock levels',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [],
                    required: []
                )
            ),
            new FunctionDeclaration(
                name: 'get_all_categories',
                description: 'Get a list of all unique categories in the inventory',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [],
                    required: []
                )
            ),
        ]);
    }

    protected function handleFunctionCall($functionCall): string
    {
        $name = $functionCall->name;
        $args = $functionCall->args ?? [];

        $result = match ($name) {
            'get_all_inventory_items' => $this->inventoryQuery->getAllItems(),
            'get_stock_summary' => $this->inventoryQuery->getStockSummary(),
            'get_low_stock_items' => $this->inventoryQuery->getLowStockItems(),
            'get_expiring_items' => $this->inventoryQuery->getExpiringItems($args['days'] ?? 30),
            'get_category_count' => $this->inventoryQuery->getCategoryCount($args['category'] ?? ''),
            'get_item_quantity' => $this->inventoryQuery->getItemQuantity($args['item_name'] ?? ''),
            'get_all_categories' => $this->inventoryQuery->getAllCategories(),
            default => ['error' => 'Unknown function'],
        };

        return json_encode($result);
    }
}
