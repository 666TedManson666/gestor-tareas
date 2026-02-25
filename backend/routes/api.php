<?php

use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::apiResource('tasks', TaskController::class)->except(['show']);
Route::apiResource('tasks.items', ItemController::class)->except(['show', 'index']);
