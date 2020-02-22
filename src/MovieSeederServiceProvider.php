<?php

namespace le_54ba\MovieSeeder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use le_54ba\MovieSeeder\App\Jobs\SeedMoviesJob;

class MovieSeederServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->loadMigration();
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->publishes([
                    __DIR__ . '/docker/' => base_path('/'),
                ], 'docker');
        
 		$this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
       		$schedule->job(new SeedMoviesJob)->cron(config('MovieSeeder.configrable_interval_timer'));
        });
    }

     /**
     * Publish package config file to framework config folder
     */
    protected function publishConfig() {
        
        $this->publishes([
           __DIR__.'/config/MovieSeeder.php' => config_path('MovieSeeder.php')
        ]);
        
        $this->mergeConfigFrom(__DIR__.'/config/MovieSeeder.php', 'MovieSeeder');
        
    }

      /**
     * Load Application migration to create database
     */
    public function loadMigration() {
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
