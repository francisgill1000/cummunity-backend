<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('members/{id}', [MemberController::class, "memberList"]);
Route::post('members/{id}', [MemberController::class, "store"]);
