<?php

namespace App\Providers;

use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Models\FriendRequest;
use App\Models\Image;
use App\Observers\CommentObserver;
use App\Observers\FriendRequestObserver;
use App\Observers\ImageObserver;
use App\Observers\LikeObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'https://localhost:3000',
            'http://localhost:8080',
            'https://localhost:8080',
            'https://localhost:5000',
            'http://localhost:5000',
            'http://myunicoms.com',
            'https://myunicoms.com',
            'http://www.myunicoms.com',
            'https://www.myunicoms.com'
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;

        if (in_array($origin, $allowedOrigins)) {
            header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        }


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ArticleLike::observe(LikeObserver::class);
        ArticleComment::observe(CommentObserver::class);
        FriendRequest::observe(FriendRequestObserver::class);
        Schema::defaultStringLength(200);
    }
}
