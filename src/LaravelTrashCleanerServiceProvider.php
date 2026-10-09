<?php

namespace Omaralalwi\LaravelTrashCleaner;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Omaralalwi\LaravelTrashCleaner\Commands\CleanUpAssets;
use Omaralalwi\LaravelTrashCleaner\Commands\CleanUpDebugTrash;
use Omaralalwi\LaravelTrashCleaner\Commands\CleanUpLogs;

class LaravelTrashCleanerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-trash-cleaner');
    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-trash-cleaner.php'),
        ], 'laravel-trash-cleaner');

        $this->commands([
            CleanUpDebugTrash::class,
            CleanUpAssets::class,
            CleanUpLogs::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $this->scheduleCleanupTasks($schedule);
        });
    }

    protected function scheduleCleanupTasks(Schedule $schedule): void
    {
        $config = $this->app['config'];

        if (! $config->get('laravel-trash-cleaner.schedule', false)) {
            return;
        }

        $frequency = (string) $config->get('laravel-trash-cleaner.frequency', 'daily');

        $this->applyFrequency($schedule->command('trash:clean'), $frequency);

        if ($config->get('laravel-trash-cleaner.logs.schedule', true)) {
            $this->applyFrequency($schedule->command('trash:clean-logs'), $frequency);
        }
    }

    protected function applyFrequency(Event $event, string $frequency): void
    {
        // An unknown frequency must not break `schedule:run` for every other task of the app.
        if ($frequency === '' || ! method_exists($event, $frequency)) {
            $frequency = 'daily';
        }

        $event->{$frequency}();
    }
}
