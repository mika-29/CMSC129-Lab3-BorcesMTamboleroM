<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;

class PromptService
{
    public function systemInstruction(): Content
    {
        return Content::parse("
        Your job is to help users by answering questions about inventory data.

        IMPORTANT RULES:
        - ALWAYS use the provided functions when the question involves inventory data.
        - DO NOT guess or fabricate inventory values.
        - If the question is unclear, ask for clarification.
        - If no function applies, respond conversationally.

        You can handle:
        - Overall stock summaries (total items, quantities, out-of-stock counts)
        - Low stock and critical items that need restocking
        - Items expiring soon (within a configurable number of days)
        - Inventory breakdown by category
        - Specific item quantities by name

        Keep responses clear, concise, and user-friendly.
        When presenting lists, format them in a readable way.
        If a function returns an error, explain it clearly to the user.
        ");
    }

    public function getTools(): Tool
    {
        return new Tool(functionDeclarations: [

            new FunctionDeclaration(
                name: 'get_stock_summary',
                description: 'Returns overall inventory statistics including total items, total quantity, out-of-stock count, and low stock count. Use when the user asks for an inventory overview or summary.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_low_stock_items',
                description: 'Returns items that are at or below their minimum stock level and need restocking. Use when the user asks about low stock, critical items, or what needs to be reordered.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_item_quantity',
                description: 'Returns the current quantity of a specific inventory item by name. Use when the user asks how many of a specific item are in stock.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'item_name' => new Schema(
                            type: DataType::STRING,
                            description: 'The name or partial name of the inventory item to look up'
                        ),
                    ],
                    required: ['item_name']
                )
            ),

            new FunctionDeclaration(
                name: 'get_expiring_items',
                description: 'Returns items that are expiring within a given number of days. Use when the user asks about expiring items, items about to expire, or expiration dates.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'days' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Number of days to look ahead for expiring items. Defaults to 30 if not specified.'
                        ),
                    ]
                )
            ),

            new FunctionDeclaration(
                name: 'get_category_count',
                description: 'Returns item count, total quantity, and low stock count for a specific inventory category. Use when the user asks about a specific product category.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'category' => new Schema(
                            type: DataType::STRING,
                            description: 'The category name to look up'
                        ),
                    ],
                    required: ['category']
                )
            ),

        ]);
    }
}
