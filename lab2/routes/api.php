<?php

use App\Http\Controllers\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('/chat/send', [ChatBotController::class, 'sendMessage']);
