<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Support\Str;

class InventoryQueryService
{
    public function resolveQuery(string $message): array
    {
        $text = Str::lower($message);

        if ($this->matches(['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'], $text)) {
            return [
                'type' => 'greeting',
                'summary' => 'Hello! I am your inventory assistant. Ask me about inventory stock, low stock items, expiring items, category counts, or specific item quantities.',
                'details' => [],
            ];
        }

        if ($this->matches(['low stock', 'out of stock', 'critical items', 'needs restock', 'reorder', 'running low'], $text)) {
            $data = $this->getLowStockItems();
            return [
                'type' => 'low_stock',
                'summary' => "There are {$data['count']} critical items that need attention.",
                'details' => $data,
            ];
        }

        if ($this->matches(['expiring', 'expiration', 'expire soon', 'expires soon', 'about to expire'], $text)) {
            $data = $this->getExpiringItems();
            return [
                'type' => 'expiring_soon',
                'summary' => "There are {$data['count']} items expiring in the next 30 days.",
                'details' => $data,
            ];
        }

        if ($this->matches(['category', 'in the category', 'items in', 'how many in'], $text)) {
            return $this->categorySummary($text);
        }

        if ($this->matches(['total quantity', 'how many of', 'quantity of', 'count of'], $text)) {
            return $this->itemQuantitySummary($text);
        }

        if ($this->matches(['how many items', 'total items', 'total quantity', 'inventory total', 'items in stock', 'available'], $text)) {
            $data = $this->getStockSummary();
            return [
                'type' => 'stock_summary',
                'summary' => "There are {$data['totalItems']} inventory items with a total quantity of {$data['totalQuantity']}. {$data['outOfStock']} items are out of stock and {$data['lowStock']} items are low on stock.",
                'details' => $data,
            ];
        }

        return [
            'type' => 'unknown',
            'summary' => 'I could not identify a supported inventory query from this request.',
            'details' => [
                'supported_queries' => [
                    'total stock counts',
                    'low stock and out-of-stock items',
                    'items expiring soon',
                    'item quantity by name',
                    'items count by category',
                    'critical inventory items',
                ],
            ],
        ];
    }

    protected function matches(array $patterns, string $text): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function getStockSummary(): array
    {
        $totalItems = Inventory::count();
        $totalQuantity = Inventory::sum('quantity');
        $outOfStock = Inventory::where('quantity', '<=', 0)->count();
        $lowStock = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->where('quantity', '>', 0)->count();
        $inStock = Inventory::where('quantity', '>', 0)->whereColumn('quantity', '>', 'minimum_stock')->count();

        return [
            'totalItems' => $totalItems,
            'totalQuantity' => $totalQuantity,
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'inStock' => $inStock,
        ];
    }

    public function getLowStockItems(): array
    {
        $items = Inventory::whereColumn('quantity', '<=', 'minimum_stock')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get(['name', 'category', 'quantity', 'minimum_stock']);

        return [
            'items' => $items->toArray(),
            'count' => $items->count(),
        ];
    }

    public function getExpiringItems(): array
    {
        $items = Inventory::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->orderBy('expiration_date', 'asc')
            ->limit(10)
            ->get(['name', 'category', 'quantity', 'expiration_date']);

        return [
            'items' => $items->toArray(),
            'count' => $items->count(),
        ];
    }

    public function getCategoryCount(string $category): array
    {
        $count = Inventory::where('category', $category)->count();
        $totalQuantity = Inventory::where('category', $category)->sum('quantity');

        return [
            'category' => $category,
            'count' => $count,
            'totalQuantity' => $totalQuantity,
        ];
    }

    public function getItemQuantity(string $itemName): array
    {
        $item = Inventory::where('name', 'like', '%' . $itemName . '%')->first(['name', 'category', 'quantity']);

        if (!$item) {
            return ['error' => 'Item not found'];
        }

        return [
            'name' => $item->name,
            'quantity' => $item->quantity,
            'category' => $item->category,
        ];
    }

    protected function categorySummary(string $message): array
    {
        $categories = Inventory::query()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        foreach ($categories as $category) {
            if (Str::contains($message, Str::lower($category))) {
                $data = $this->getCategoryCount($category);
                return [
                    'type' => 'category_count',
                    'summary' => "There are {$data['count']} items in the {$category} category with a total quantity of {$data['totalQuantity']}.",
                    'details' => $data,
                ];
            }
        }

        return [
            'type' => 'unknown_category',
            'summary' => 'Please specify a category name so I can look up the correct inventory count.',
            'details' => ['available_categories' => $categories->toArray()],
        ];
    }

    protected function itemQuantitySummary(string $message): array
    {
        $items = Inventory::all(['name', 'category', 'quantity']);
        $match = null;

        foreach ($items as $item) {
            if (Str::contains($message, Str::lower($item->name))) {
                if ($match !== null) {
                    return [
                        'type' => 'ambiguous_item',
                        'summary' => 'The request matches more than one item name. Please specify the item name clearly.',
                        'details' => ['matches' => [$match->name, $item->name]],
                    ];
                }

                $match = $item;
            }
        }

        if ($match === null) {
            return [
                'type' => 'unknown_item',
                'summary' => 'Please specify the exact item name so I can look up its quantity.',
                'details' => ['example_items' => $items->pluck('name')->take(8)->values()->toArray()],
            ];
        }

        return [
            'type' => 'item_quantity',
            'summary' => "The inventory quantity for {$match->name} is {$match->quantity}.",
            'details' => ['name' => $match->name, 'quantity' => $match->quantity, 'category' => $match->category],
        ];
    }
}
