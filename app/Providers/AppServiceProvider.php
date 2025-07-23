<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use TomatoPHP\FilamentUsers\Facades\FilamentUser;

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
        FilamentColor::register([
            //'danger' => Color::hex('#00F'), // Example custom color
        ]);

        FilamentUser::register([
            \Filament\Resources\RelationManagers\RelationManager::make() // Replace with your custom relation manager
        ]);

        if(env('FORCE_HTTPS', false)){
            \Illuminate\Support\Facades\URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
