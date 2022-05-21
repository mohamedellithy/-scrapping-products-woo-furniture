<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        //$this->app->register('Wn\Generators\CommandsServiceProvider');

        // Defined "ide-helper" namespace.
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind('path.public', function() {
            return base_path('../public_html');
        });

        Schema::defaultStringLength(191);
    }
}
