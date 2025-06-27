<?php

namespace Omaralalwi\LaravelTrashCleaner\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CleanUpAssets extends Command
{
    protected $signature = 'trash:clean-assets {--build : Also run frontend build commands}';
    protected $description = 'Clean compiled views, Vite cache, public/build and optionally rebuild frontend assets';

    public function handle(): int
    {
        $this->info('🧼 Starting cleanup...');

        // Read from config
        $paths = config('laravel-trash-cleaner.cleanup_paths', []);
        $packageManager = config('laravel-trash-cleaner.package_manager', 'npm');
        $buildCommands = config('laravel-trash-cleaner.build_commands', []);

        // Clean paths
        foreach ($paths as $relativePath) {
            $absolutePath = base_path($relativePath);
            $command = ['rm', '-rf', $absolutePath];
            $this->runProcess($command);
        }

        // Optional build
        if ($this->option('build')) {
            $this->info("🔧 Running frontend build with: {$packageManager}");

            foreach ($buildCommands as $cmd) {
                $full = explode(' ', trim("{$packageManager} {$cmd}"));
                $this->runProcess($full);
            }
        }

        $this->info('✅ Done!');
        return self::SUCCESS;
    }

    private function runProcess(array $command): void
    {
        $this->info('➤ ' . implode(' ', $command));
        $process = new Process($command, base_path());
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('⛔ Failed: ' . implode(' ', $command));
            $this->error(trim($process->getErrorOutput()));
        } else {
            $this->info('✅ Success');
        }
    }
}
