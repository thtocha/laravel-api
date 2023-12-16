<?php

use App\Http\Controllers\Api\V1\DeskController;
use App\Http\Controllers\Api\V1\DeskListController;
use App\Http\Controllers\Api\V1\CardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('desks', DeskController::class);
Route::apiResource('desk-lists', DeskListController::class);
Route::apiResource('cards', CardController::class);
