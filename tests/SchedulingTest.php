<?php

namespace Omaralalwi\LaravelTrashCleaner\Tests;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

class SchedulingTest extends TestCase
{
    private bool $scheduleEnabled = true;

    private bool $logsScheduled = true;

    protected function defineEnvironment($app)
    {
        $app['config']->set('laravel-trash-cleaner.schedule', $this->scheduleEnabled);
        $app['config']->set('laravel-trash-cleaner.frequency', 'hourly');
        $app['config']->set('laravel-trash-cleaner.logs.schedule', $this->logsScheduled);
    }

    public function test_enabled_schedule_registers_the_cleanup_commands(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('trash:clean ')
            ->expectsOutputToContain('trash:clean-logs')
            ->assertExitCode(0);

        foreach ($this->scheduledCommands() as $event) {
            $this->assertSame('0 * * * *', $event->expression);
        }
    }

    public function test_disabled_schedule_registers_nothing(): void
    {
        $this->scheduleEnabled = false;
        $this->refreshApplication();

        $this->assertSame([], $this->scheduledCommands());
    }

    public function test_log_cleaning_can_be_left_out_of_the_schedule(): void
    {
        $this->logsScheduled = false;
        $this->refreshApplication();

        $commands = $this->scheduledCommands();

        $this->assertCount(1, $commands);
        $this->assertStringEndsWith('trash:clean', $commands[0]->command);
    }

    /**
     * @return array<int, Event>
     */
    private function scheduledCommands(): array
    {
        $this->artisan('schedule:list');

        return array_values(array_filter(
            $this->app->make(Schedule::class)->events(),
            fn (Event $event) => strpos((string) $event->command, 'trash:') !== false
        ));
    }
}
