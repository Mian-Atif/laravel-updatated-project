<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post("signup",[UserController::class,"signUp"]);
Route::get("emailConfirmation/{email}",[UserController::class,"emailConfirmation"]);
Route::post("signin",[UserController::class,"signIn"]);
Route::post("logout",[UserController::class,"logOut"])->middleware('UserAuthentication');
Route::post("resource",[UserController::class,"resource"]);
