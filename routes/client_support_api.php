<?php

use App\Http\Controllers\API\ClientController;
use Illuminate\Support\Facades\Route;
use Spatie\FlareClient\Api;

Route::post('company/{id}/regnerate_access_token', [ClientController::class, "generateToken"]);
Route::post('get_attendance_logs', [ClientController::class, "getAttendanceLogs"]);
Route::get('download_postman_json', [ClientController::class, 'downloadPostmanJson']);
