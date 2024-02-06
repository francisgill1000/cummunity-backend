<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TanentController;

Route::apiResource('/tanent', TanentController::class);
Route::post('/tanent-update/{id}', [TanentController::class,"tanentUpdate"]);
Route::post('/tanent-validate', [TanentController::class,"validateTanent"]);
Route::post('/tanent-update-validate/{id}', [TanentController::class,"validateUpdateTanent"]);



