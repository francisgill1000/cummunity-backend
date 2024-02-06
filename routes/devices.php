<?php

use App\Http\Controllers\DeviceCameraModel2Controller;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceStatusController;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Device
Route::apiResource('device', DeviceController::class);
Route::get('device-list', [DeviceController::class, 'dropdownList']);
Route::get('device-mode-list', [DeviceController::class, 'modes']);




Route::get('device/search/{key}', [DeviceController::class, 'search']);
Route::get('device-by-user/{id}', [DeviceController::class, 'getDeviceByUserId']);
Route::post('device/details', [DeviceController::class, 'getDeviceCompany']);
Route::get('device/getLastRecordsByCount/{company_id}/{count}', [DeviceController::class, 'getLastRecordsByCount']);
Route::get('device/getLastRecordsHistory/{company_id}/{count}', [DeviceController::class, 'getLastRecordsHistory']);
//Route::get('device/getLastRecordsByCount', [DeviceController::class, 'getLastRecordsByCount']);
Route::post('device/delete/selected', [DeviceController::class, 'deleteSelected']);
Route::get('device_list', [DeviceController::class, 'getDeviceList']);
Route::get('devcie_count_Status/{company_id}', [DeviceController::class, 'devcieCountByStatus']);

Route::get('sync_device_date_time/{device_id}/{company_id}', [DeviceController::class, "sync_device_date_time"]);

//  Device Status
Route::apiResource('device_status', DeviceStatusController::class);
Route::get('device_status/search/{key}', [DeviceStatusController::class, 'search']);
Route::post('device_status/delete/selected', [DeviceStatusController::class, 'deleteSelected']);


Route::post('update_devices_active_settings/{key}', [DeviceController::class, 'updateActiveTimeSettings']);
Route::get('get_device_active_settings/{key}', [DeviceController::class, 'getActiveTimeSettings']);


Route::get('/check_device_health', [DeviceController::class, 'checkDeviceHealth']);
Route::get('get-device-person-details', [DeviceController::class, 'getDevicePersonDetails']);
Route::get('get-device-settings-from-sdk', [DeviceController::class, 'getDeviceSettingsFromSDK']);
Route::post('update-device-sdk-settings', [DeviceController::class, 'updateDeviceSettingsToSDK']);
Route::post('update-device-alarm-status', [DeviceController::class, 'updateDeviceAlarmToSDK']);




Route::get('get-device-camvii-settings-from-sdk',  [DeviceController::class, 'getDevicecamviiSettingsFromSDK']);
Route::post('update-device-camvii-sdk-settings', [DeviceController::class, 'updateDeviceCamVIISettingsToSDK']);




Route::get('/open_door', [DeviceController::class, 'openDoor']);
Route::get('/close_door', [DeviceController::class, 'closeDoor']);
Route::get('/open_door_always', [DeviceController::class, 'openDoorAlways']);


// Route::get('/open_door', function (Request $request) {

//     $curl = curl_init();

//     $device_id = $request->device_id;

//     // $device_id = 'OX-8862021010076';

//     curl_setopt_array($curl, array(
//         CURLOPT_URL => "http://139.59.69.241:5000/$device_id/OpenDoor",
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//     ));

//     $response = curl_exec($curl);

//     curl_close($curl);
//     echo $response;

//     // return "Awesome APIs";
// });


// Route::get('/open_door_always_old', function (Request $request) {

//     $curl = curl_init();

//     $device_id = $request->device_id;

//     // $device_id = 'OX-8862021010076';

//     curl_setopt_array($curl, array(
//         CURLOPT_URL => "http://139.59.69.241:5000/$device_id/HoldDoor",
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//     ));

//     $response = curl_exec($curl);

//     curl_close($curl);
//     echo $response;

//     // return "Awesome APIs";
// });

// Route::get('/close_door_old', function (Request $request) {

//     $curl = curl_init();

//     $device_id = $request->device_id;

//     // $device_id = 'OX-8862021010076';

//     curl_setopt_array($curl, array(
//         CURLOPT_URL => "http://139.59.69.241:5000/$device_id/CloseDoor",
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//     ));

//     $response = curl_exec($curl);

//     curl_close($curl);
//     echo $response;

//     // return "Awesome APIs";
// });
