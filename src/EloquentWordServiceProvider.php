<?php

namespace Ambengers\EloquentWord;

use Ambengers\EloquentWord\Console\EloquentWordMakeCommand;
use Illuminate\Support\ServiceProvider;

class EloquentWordServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/eloquent_word.php' => config_path('eloquent_word.php'),
        ], 'eloquent-word-config');

        if ($this->app->runningInConsole()) {
            $this->commands(EloquentWordMakeCommand::class);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/eloquent_word.php', 'eloquent_word');
    }
}
