<?php

namespace Omaralalwi\LaravelTrashCleaner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Omaralalwi\LaravelTrashCleaner\Commands\{CleanUpDebugTrash,CleanUpAssets};

class LaravelTrashCleanerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $configFile = config_path('laravel-trash-cleaner.php');

            // Force delete the old config file if it exists
            if (file_exists($configFile)) {
                unlink($configFile);
            }

            $this->publishes([
                __DIR__.'/../config/laravel-trash-cleaner.php' => config_path('laravel-trash-cleaner.php'),
            ], 'laravel-trash-cleaner');

            // Register the command
            $this->commands([
                CleanUpDebugTrash::class,
                CleanUpAssets::class,
            ]);

            $this->app->afterResolving('events', function ($events) {
                $scheduler = $this->app->make(Schedule::class);
                $this->scheduleCleanupTask($scheduler);
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-trash-cleaner.php', 'laravel-trash-cleaner');
    }

    /**
     * Schedule the cleanup task based on the configuration.
     *
     * @param Schedule $schedule
     */
    protected function scheduleCleanupTask(Schedule $schedule)
    {
        $config = config('laravel-trash-cleaner');

        if ($config['schedule']) {
            $schedule->command('trash:clean')->{$config['frequency']}();
        }
    }
}
