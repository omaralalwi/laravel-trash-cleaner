<?php

namespace Omaralalwi\LaravelTrashCleaner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Omaralalwi\LaravelTrashCleaner\Commands\CleanCommand;

class LaravelTrashCleanerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-trash-cleaner.php'),
            ], 'config');

            // Register the command
            $this->commands([
                CleanCommand::class,
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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-trash-cleaner');

      /*
        $this->app->singleton('laravel-trash-cleaner', function () {
            return new LaravelTrashCleaner;
        });
      */
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
