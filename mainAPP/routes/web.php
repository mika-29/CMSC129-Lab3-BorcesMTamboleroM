<?php
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatBotController;

Route::get('/', function () {
    return redirect()->route('inventory.index');
});

// Trash routes
Route::get('inventory/trashed', [InventoryController::class, 'trashed'])->name('inventory.trashed');
Route::patch('inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
Route::delete('inventory/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.forceDelete');
Route::get('inventory/critical', [InventoryController::class, 'critical'])->name('inventory.critical');

Route::resource('inventory', InventoryController::class);

Route::post('/chatbot', [ChatBotController::class, 'sendMessage'])->name('chat');
