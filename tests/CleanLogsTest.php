<?php

namespace Omaralalwi\LaravelTrashCleaner\Tests;

use Illuminate\Support\Facades\File;

class CleanLogsTest extends TestCase
{
    private string $logs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logs = storage_path('logs');
        File::ensureDirectoryExists($this->logs);
        $this->removeLogs();
    }

    protected function tearDown(): void
    {
        $this->removeLogs();

        parent::tearDown();
    }

    public function test_large_logs_are_truncated_to_their_last_lines(): void
    {
        $this->writeLines('laravel.log', 2000);

        $this->artisan('trash:clean-logs', ['--max' => '1K', '--keep' => 3])->assertExitCode(0);

        $this->assertSame("line 1998\nline 1999\nline 2000\n", file_get_contents($this->logs.'/laravel.log'));
    }

    public function test_a_log_without_a_trailing_newline_keeps_its_last_lines(): void
    {
        file_put_contents($this->logs.'/laravel.log', str_repeat("noise\n", 500)."a\nb\nc");

        $this->artisan('trash:clean-logs', ['--max' => '1', '--keep' => 2])->assertExitCode(0);

        $this->assertSame("b\nc", file_get_contents($this->logs.'/laravel.log'));
    }

    public function test_lines_longer_than_the_read_buffer_are_kept_whole(): void
    {
        $long = str_repeat('y', 20000);
        file_put_contents($this->logs.'/laravel.log', str_repeat("noise\n", 100).$long."\nend\n");

        $this->artisan('trash:clean-logs', ['--max' => '1', '--keep' => 2])->assertExitCode(0);

        $this->assertSame($long."\nend\n", file_get_contents($this->logs.'/laravel.log'));
    }

    public function test_small_logs_are_left_alone(): void
    {
        $this->writeLines('small.log', 10);
        $before = file_get_contents($this->logs.'/small.log');

        $this->artisan('trash:clean-logs', ['--max' => '1M', '--keep' => 3])->assertExitCode(0);

        $this->assertSame($before, file_get_contents($this->logs.'/small.log'));
    }

    public function test_all_empties_every_log_without_deleting_it(): void
    {
        $this->writeLines('a.log', 5);
        $this->writeLines('b.log', 5);

        $this->artisan('trash:clean-logs', ['--all' => true])->assertExitCode(0);

        $this->assertSame(0, filesize($this->logs.'/a.log'));
        $this->assertSame(0, filesize($this->logs.'/b.log'));
    }

    public function test_an_open_append_handle_keeps_writing_after_truncation(): void
    {
        $this->writeLines('laravel.log', 2000);
        $handle = fopen($this->logs.'/laravel.log', 'a');

        $this->artisan('trash:clean-logs', ['--max' => '1K', '--keep' => 1])->assertExitCode(0);
        fwrite($handle, "after\n");
        fclose($handle);

        $this->assertSame("line 2000\nafter\n", file_get_contents($this->logs.'/laravel.log'));
    }

    public function test_it_reports_freed_bytes(): void
    {
        file_put_contents($this->logs.'/laravel.log', str_repeat("0123456789\n", 1000));

        $this->artisan('trash:clean-logs', ['--max' => '1K', '--keep' => 0])
            ->expectsOutputToContain('Freed up 10.74 KB')
            ->assertExitCode(0);
    }

    public function test_defaults_come_from_the_config(): void
    {
        config()->set('laravel-trash-cleaner.logs.max_size', '1K');
        config()->set('laravel-trash-cleaner.logs.keep_lines', 2);
        $this->writeLines('laravel.log', 2000);

        $this->artisan('trash:clean-logs')->assertExitCode(0);

        $this->assertSame("line 1999\nline 2000\n", file_get_contents($this->logs.'/laravel.log'));
    }

    public function test_non_log_files_are_ignored(): void
    {
        file_put_contents($this->logs.'/notes.txt', str_repeat("x\n", 2000));

        $this->artisan('trash:clean-logs', ['--all' => true])->assertExitCode(0);

        $this->assertSame(4000, filesize($this->logs.'/notes.txt'));
    }

    public function test_an_invalid_max_size_fails(): void
    {
        $this->artisan('trash:clean-logs', ['--max' => 'lots'])->assertExitCode(1);
    }

    private function writeLines(string $file, int $count): void
    {
        $handle = fopen($this->logs.'/'.$file, 'w');
        for ($i = 1; $i <= $count; $i++) {
            fwrite($handle, "line {$i}\n");
        }
        fclose($handle);
    }

    private function removeLogs(): void
    {
        foreach (array_merge(glob($this->logs.'/*.log') ?: [], glob($this->logs.'/*.txt') ?: []) as $file) {
            unlink($file);
        }
    }
}
