<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::latest()->paginate(10);
        return view('inventory.index', compact('items'));
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

    public function edit(Inventory $item)
    {
        return view('inventory.edit', compact('item'));
    }

    public function update(Request $request, Inventory $item)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'expiration_date' => 'nullable|date',
        ]);

        $item->update($request->all());

        return redirect()->route('inventory.index')
            ->with('success', 'Supply updated!');
    }

    public function destroy(Inventory $item)
    {
        $item->delete();

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
