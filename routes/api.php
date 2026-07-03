<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlueprintController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/blueprints', [BlueprintController::class, 'index']);
    Route::get('/blueprints/{blueprint}', [BlueprintController::class, 'show']);
     Route::post('/blueprints', [BlueprintController::class, 'store']);
    Route::put('/blueprints/{blueprint}', [BlueprintController::class, 'update']);
    Route::delete('/blueprints/{blueprint}', [BlueprintController::class, 'destroy']);
    });