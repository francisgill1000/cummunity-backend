<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FloorController;


Route::apiResource('/floor', FloorController::class);

