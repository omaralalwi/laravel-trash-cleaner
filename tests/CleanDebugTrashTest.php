<?php

namespace Omaralalwi\LaravelTrashCleaner\Tests;

use Illuminate\Support\Facades\File;

class CleanDebugTrashTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('debugbar'));
        File::deleteDirectory(storage_path('clockwork'));

        parent::tearDown();
    }

    public function test_it_succeeds_when_no_debug_folders_exist(): void
    {
        File::deleteDirectory(storage_path('debugbar'));
        File::deleteDirectory(storage_path('clockwork'));

        $this->artisan('trash:clean')
            ->expectsOutputToContain('Freed up 0 B')
            ->assertExitCode(0);
    }

    public function test_it_reports_zero_bytes_instead_of_nan_when_folders_are_empty(): void
    {
        File::ensureDirectoryExists(storage_path('debugbar'));
        File::put(storage_path('debugbar/.gitignore'), "*\n");

        $this->artisan('trash:clean')
            ->doesntExpectOutputToContain('NAN')
            ->expectsOutputToContain('Freed up 0 B')
            ->assertExitCode(0);
    }

    public function test_it_deletes_json_debug_files_and_keeps_others(): void
    {
        File::ensureDirectoryExists(storage_path('debugbar'));
        File::ensureDirectoryExists(storage_path('clockwork'));
        File::put(storage_path('debugbar/a.json'), str_repeat('x', 1024));
        File::put(storage_path('debugbar/.gitignore'), "*\n");
        File::put(storage_path('clockwork/b.json'), str_repeat('x', 1024));
        File::put(storage_path('clockwork/index'), 'keep');

        $this->artisan('trash:clean')
            ->expectsOutputToContain('Cleared 1 debug bar files and 1 clockwork files. Freed up 2 KB.')
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(storage_path('debugbar/a.json'));
        $this->assertFileDoesNotExist(storage_path('clockwork/b.json'));
        $this->assertFileExists(storage_path('debugbar/.gitignore'));
        $this->assertFileExists(storage_path('clockwork/index'));
    }
}
