<?php

namespace Omaralalwi\LaravelTrashCleaner\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Omaralalwi\LaravelTrashCleaner\Support\Bytes;

class CleanUpDebugTrash extends Command
{
    protected $signature = 'trash:clean';

    protected $description = 'Clean the debug files in the storage/debugbar and storage/clockwork folders with progress bar.';

    public function handle()
    {
        $debugbarFiles = $this->debugFiles(storage_path('debugbar'));
        $clockworkFiles = $this->debugFiles(storage_path('clockwork'));
        $total = count($debugbarFiles) + count($clockworkFiles);

        if ($total > 0) {
            $this->output->progressStart($total);
        }

        $debugbarSize = $this->deleteFiles($debugbarFiles);
        $clockworkSize = $this->deleteFiles($clockworkFiles);

        if ($total > 0) {
            $this->output->progressFinish();
        }

        $this->info(sprintf(
            'Cleared %d debug bar files and %d clockwork files. Freed up %s.',
            count($debugbarFiles),
            count($clockworkFiles),
            Bytes::format($debugbarSize + $clockworkSize)
        ));

        return self::SUCCESS;
    }

    /**
     * @return array<int, \Symfony\Component\Finder\SplFileInfo>
     */
    protected function debugFiles(string $path): array
    {
        if (! File::isDirectory($path)) {
            return [];
        }

        return array_values(array_filter(File::allFiles($path), function ($file) {
            return $file->getFilename() !== '.gitignore' && $file->getExtension() === 'json';
        }));
    }

    /**
     * @param  array<int, \Symfony\Component\Finder\SplFileInfo>  $files
     */
    protected function deleteFiles(array $files): int
    {
        $size = 0;

        foreach ($files as $file) {
            $size += $file->getSize();
            File::delete($file->getPathname());
            $this->output->progressAdvance();
        }

        return $size;
    }
}
