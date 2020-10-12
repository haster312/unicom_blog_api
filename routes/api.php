<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\UniversityController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\ArticleActionController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\FriendController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/social', [AuthController::class, 'socialRegister']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
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

Route::group(['prefix' => 'user'], function () {

    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/', [UserController::class, 'detail']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/password/change', [AuthController::class, 'changePassword']);
        Route::post('/check', [AuthController::class, 'checkValidToken']);
    });

    Route::get('/{username}', [UserController::class, 'getDetailByUsername']);
});

Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, 'list']);
    Route::get('/{slug}', [CategoryController::class, 'detail']);
    Route::get('/sub', [CategoryController::class, 'subList']);
});

Route::group(['prefix' => 'article'], function () {
    Route::get('/all', [ArticleController::class, 'allArticleWithSlug']);
    Route::get('/category/{categoryId}', [ArticleController::class, 'getListWithCategory']);
    Route::get('/slug/{slug}', [ArticleController::class, 'detailSlug']);
    Route::get('/user', [ArticleController::class, 'getUserArticle']);
    Route::get('/latest', [ArticleController::class, 'getLatest']);
    Route::get('/related', [ArticleController::class, 'getRelated']);
    Route::get('/popular/most', [ArticleController::class, 'getMostPopular']);
    Route::get('/popular/week', [ArticleController::class, 'getWeeklyPopular']);
    Route::get('/popular/month', [ArticleController::class, 'getMonthlyPopular']);
    Route::get('/popular/feature', [ArticleController::class, 'getFeature']);

    /**
     * Article action requires authentication
     */
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/self', [ArticleController::class, 'getSelfArticle']);
        Route::get('/{id}', [ArticleController::class, 'detail'])->where('id', '[0-9]+');
        Route::post('/', [ArticleController::class, 'new']);
        Route::post('/status/{id}', [ArticleController::class, 'publishArticle'])->where('id', '[0-9]+');
        Route::post('/{id}', [ArticleController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [ArticleController::class, 'delete'])->where('id', '[0-9]+');
    });

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/like', [ArticleActionController::class, 'likeArticle']);
        Route::get('/comment', [ArticleActionController::class, 'getComment']);
        Route::post('/comment', [ArticleActionController::class, 'commentArticle']);
        Route::delete('/comment/{id}', [ArticleActionController::class, 'deleteComment']);
    });
});

Route::group(['prefix' => 'tag'], function() {
    Route::get('/cloud', [TagController::class, 'getFooterTag']);
});

Route::group(['prefix' => 'search'], function() {
    Route::get('/', [SearchController::class, 'searchArticle']);
    Route::get('/advanced', [SearchController::class, 'advancedSearchArticle']);
});

Route::group(['prefix' => 'notification', 'middleware' => ['auth:api']], function() {
    Route::get('/', [NotificationController::class, 'getNotification']);
    Route::get('/latest', [NotificationController::class, 'getLatestNotification']);
    Route::post('/{id}', [NotificationController::class, 'seenNotification'])->where('id', '[0-9]+');
    Route::post('/token', [NotificationController::class, 'setNotificationToken']);
    Route::get('/chat', [NotificationController::class, 'getChatNotification']);
});

Route::group(['prefix' => 'friend', 'middleware' => ['auth:api']], function() {
    Route::get('/', [FriendController::class, 'getFriendList']);
    Route::post('/request', [FriendController::class, 'addRequest']);
    Route::get('/search', [FriendController::class, 'searchFriend']);
    Route::post('/accept', [FriendController::class, 'acceptRequest']);
    Route::post('/reject', [FriendController::class, 'rejectRequest']);
});
