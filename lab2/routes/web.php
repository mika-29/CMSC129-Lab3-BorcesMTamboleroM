<?php
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('inventory.index');
});

Route::resource('inventory', InventoryController::class);

// Trash routes
Route::get('inventory/trashed', [InventoryController::class, 'trashed'])->name('inventory.trashed');
Route::patch('inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
Route::delete('inventory/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.forceDelete');
Route::get('inventory/critical', [InventoryController::class, 'critical'])->name('inventory.critical');