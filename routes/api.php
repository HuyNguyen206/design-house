<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Design\UploadController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\SettingController;
use App\Http\Controllers\User\UserController;
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
//Public routes
Route::get('me', [MeController::class, 'getMe']);
Route::get('users', [UserController::class, 'index']);
Route::get('designs', [UploadController::class, 'index']);
Route::get('designs/{id}', [UploadController::class, 'findDesignById']);

//Route for logined user
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::put('setting/profile', [SettingController::class, 'updateProfile']);
    Route::put('setting/password', [SettingController::class, 'updatePassword']);
    Route::post('designs', [UploadController::class, 'upload']);
    Route::put('designs/{design}', [UploadController::class, 'updateDesignInfo']);
    Route::delete('designs/{design}', [UploadController::class, 'deleteDesign']);

});

Route::middleware('guest')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('api.password.reset');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
