<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\UniversityController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);

/**
 * Upload file
 */
Route::group(['prefix' => 'file'], function () {
    Route::post('/single', [FileController::class, 'single']);
    Route::post('/multi', [FileController::class, 'multi']);
});

/**
 * User section
 */
Route::group(['prefix' => 'university'], function () {
    Route::get('/', [UniversityController::class, 'getUniversity']);
    Route::get('/course', [UniversityController::class, 'getCourse']);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:api']], function () {
    Route::get('/', [UserController::class, 'detail']);
});
