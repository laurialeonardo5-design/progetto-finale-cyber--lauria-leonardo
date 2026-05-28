<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; 
use Illuminate\Auth\Events\Registered; 
use App\Listeners\LogRegisteredUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(Schema::hasTable('categories')){
            $categories = Category::all();
            View::share(['categories' => $categories]);
        }
        if(Schema::hasTable('tags')){
            $tags = Tag::all();
            View::share(['tags' => $tags]);
        }
        Event::listen(
            Registered::class,
            LogRegisteredUser::class
        );
    }
}
