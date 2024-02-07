<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('members/{id}', [MemberController::class, "memberList"]);
Route::post('members/{id}', [MemberController::class, "store"]);
Route::post('/members-update/{id}', [MemberController::class,"memberUpdate"]);