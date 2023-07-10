<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signUp']);


Route::group(['middleware' => ['auth:api']], static function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('check_permissions:impersonate')->post('impersonate', [AuthController::class, 'impersonate']);
    Route::middleware('check_permissions:revoke-impersonate')->post('revoke-impersonate', [AuthController::class, 'revokeImpersonate']);
});

