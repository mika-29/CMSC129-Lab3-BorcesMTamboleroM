<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
   public function index(Request $request)
    {
        $query = Inventory::query();

        // Search across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                ->orWhere('category', 'ILIKE', "%{$search}%")
                ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter 1: by status
        if ($request->filled('status')) {
        if ($request->status === 'out_stock') {
            $query->where('quantity', '<=', 0);
        } elseif ($request->status === 'low_stock') {
            $query->where('quantity', '>', 0)
                  ->whereColumn('quantity', '<=', 'minimum_stock');
        } elseif ($request->status === 'in_stock') {
            $query->whereColumn('quantity', '>', 'minimum_stock');
        }
    }

        // Filter 2: category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $criticalCount = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->count();
        $criticalItems = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->get();
        $categories = Inventory::select('category')
                    ->distinct()
                    ->whereNotNull('category')
                    ->orderBy('category')
                    ->pluck('category');

        return view('inventory.index', compact('items', 'criticalCount', 'criticalItems', 'categories'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'expiration_date' => 'nullable|date',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')
            ->with('success', 'Supply added successfully!');
    }

    public function show(Inventory $item)
    {
        return view('inventory.show', compact('item'));
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', ['inventory' => $inventory]);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'expiration_date' => 'nullable|date',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')
            ->with('success', 'Supply updated!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Moved to trash.');
    }
    public function critical()
    {
        $items = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->paginate(10);
        return view('inventory.critical', compact('items'));
    }

    // TRASH
    public function trashed()
    {
        $items = Inventory::onlyTrashed()->paginate(10);
        return view('inventory.trashed', compact('items'));
    }

    public function restore($id)
    {
        Inventory::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with('success', 'Restored successfully.');
    }

    public function forceDelete($id)
    {
        Inventory::onlyTrashed()->findOrFail($id)->forceDelete();

        return redirect()->back()->with('success', 'Deleted permanently.');
    }
}
