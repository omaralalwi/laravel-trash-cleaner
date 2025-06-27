<?php

namespace Omaralalwi\LaravelTrashCleaner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Omaralalwi\LaravelTrashCleaner\Commands\{CleanCommand, CleanUpAssets};

class LaravelTrashCleanerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $configFile = config_path('laravel-trash-cleaner.php');
            $packageConfig = __DIR__ . '/../config/config.php';

            // 🔥 Force delete the old config file if it exists
            if (file_exists($configFile)) {
                unlink($configFile);
            }

            // 📦 Publish the fresh config file
            $this->publishes([
                $packageConfig => $configFile,
            ], 'config');

            // 🧹 Register package commands
            $this->commands([
                CleanCommand::class,
                CleanUpAssets::class,
            ]);

            // 🗓️ Register scheduler task if enabled
            $this->app->afterResolving('events', function () {
                $this->scheduleCleanupTask($this->app->make(Schedule::class));
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php',
            'laravel-trash-cleaner'
        );
    }

    /**
     * Schedule the cleanup task based on the configuration.
     */
    protected function scheduleCleanupTask(Schedule $schedule)
    {
        $config = config('laravel-trash-cleaner');

        if (!empty($config['schedule'])) {
            $schedule->command('trash:clean')->{$config['frequency']}();
        }
    }
}
