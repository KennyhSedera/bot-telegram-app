<?php

use App\Http\Controllers\PdfController;
use App\Http\Controllers\StartBotController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\Route;

// Route::post('/telegram/webhook', [StartBotController::class, 'sendMessage']);
Route::post('/telegram/webhook', [TelegramBotController::class, 'handle']);

Route::get('/pdf', [PdfController::class, 'generate']);
