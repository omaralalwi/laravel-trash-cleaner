<?php

namespace Omaralalwi\LaravelTrashCleaner\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanCommand extends Command
{
    protected $signature = 'trash:clean';

    protected $description = 'Clean the debug files in the storage/debugbar and storage/clockwork folders with progress bar.';

    public function handle()
    {
        $debugbarPath = storage_path('debugbar');
        $debugbarCount = 0;
        $debugbarSize = 0;
        if (File::exists($debugbarPath)) {
            $this->output->progressStart(count(File::allFiles($debugbarPath)));
            list($debugbarCount, $debugbarSize) = $this->clearCacheFiles($debugbarPath);
        }

        $clockworkPath = storage_path('clockwork');
        $clockworkCount = 0;
        $clockworkSize = 0;
        if (File::exists($clockworkPath)) {
            $this->output->progressStart(count(File::allFiles($clockworkPath)));
            list($clockworkCount, $clockworkSize) = $this->clearCacheFiles($clockworkPath);
        }

        $this->output->progressFinish();
        $this->info("Cleared $debugbarCount debug bar files and $clockworkCount clockwork files. Freed up " . $this->formatBytes($debugbarSize + $clockworkSize) . ".");
    }

    protected function clearCacheFiles($path)
    {
        $count = 0;
        $size = 0;
        $files = File::allFiles($path);
        foreach ($files as $file) {
            if ($file->getFilename() !== '.gitignore' && $file->getExtension() === 'json') {
                $count++;
                $size += $file->getSize();
                File::delete($file->getPathname());
                $this->output->progressAdvance();
            }
        }
        return [$count, $size];
    }

    protected function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)] . 'B';
    }
}
