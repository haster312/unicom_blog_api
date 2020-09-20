<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\UniversityController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
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
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/password/change', [UserController::class, 'changePassword']);
});

Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, 'list']);
    Route::get('/{slug}', [CategoryController::class, 'detail']);
    Route::get('/sub', [CategoryController::class, 'subList']);
});

Route::group(['prefix' => 'article'], function () {
    Route::get('/category/{categoryId}', [ArticleController::class, 'getListWithCategory']);
    Route::get('/{slug}', [ArticleController::class, 'detail']);

    Route::group(['prefix' => 'popular'], function () {
        Route::get('/all', [ArticleController::class, 'getMostPopular']);
        Route::get('/week', [ArticleController::class, 'getWeeklyPopular']);
        Route::get('/month', [ArticleController::class, 'getMonthlyPopular']);
    });

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/', [ArticleController::class, 'new']);
        Route::post('/{id}', [ArticleController::class, 'update']);
        Route::delete('/{id}', [ArticleController::class, 'delete']);
    });
});
