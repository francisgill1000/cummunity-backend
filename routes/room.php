<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomCategoryController;


Route::apiResource('/room', RoomController::class);
Route::get('/room-by-floor-id', [RoomController::class, "getRoomsByFloorId"]);
Route::get('/tanents-and-members-by-room-id', [RoomController::class, "getTanentsAndMembersByRoomsId"]);
Route::apiResource('/room-category', RoomCategoryController::class);
