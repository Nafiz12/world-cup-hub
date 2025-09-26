<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorldcupHistoryController;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/world-cup-history', [WorldcupHistoryController::class, 'index']);

Route::post('/chat', [ChatController::class, 'chat']);        // main endpoint for your Vue widget
Route::get('/test-openai', [ChatController::class, 'test']);  // quick probe in browser

