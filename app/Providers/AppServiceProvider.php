<?php

namespace App\Providers;

use App\Models\Enterprise;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        setlocale(LC_TIME, 'es_ES.utf8', 'es_ES', 'es');
        // Usar un View Composer para compartir variables
        View::composer('layouts.app', function ($view) {
            $enterprise = Enterprise::findOrFail(1);
            $view->with(['enterprise' => $enterprise]);
        });

        View::composer('homepage.app', function ($view) {
            $enterprise = Enterprise::findOrFail(1);
            $recentPost = Post::where('id', '<>', 21)->orderBy('id', 'desc')->limit(2)->get();
            $view->with(['enterprise' => $enterprise, 'recentPost' => $recentPost]);
        });

        Carbon::setLocale('es'); // Español global
    }
}
