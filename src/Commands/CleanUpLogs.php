<?php

namespace Omaralalwi\LaravelTrashCleaner\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Omaralalwi\LaravelTrashCleaner\Support\Bytes;
use RuntimeException;

class CleanUpLogs extends Command
{
    private const CHUNK = 65536;

    protected $signature = 'trash:clean-logs
        {--max= : Trim logs larger than this size, e.g. 500K, 1M, 2G (default: config logs.max_size)}
        {--keep= : Number of most recent lines to keep (default: config logs.keep_lines)}
        {--all : Empty every log file regardless of size}';

    protected $description = 'Trim the log files in storage/logs in place, keeping only their most recent lines.';

    public function handle()
    {
        $all = (bool) $this->option('all');

        try {
            $max = Bytes::parse((string) ($this->option('max') ?? config('laravel-trash-cleaner.logs.max_size', '1M')));
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $keep = $all ? 0 : $this->option('keep') ?? config('laravel-trash-cleaner.logs.keep_lines', 500);

        if (! is_numeric($keep) || (int) $keep < 0) {
            $this->error("Invalid --keep value [{$keep}]. Use a non-negative integer.");

            return self::FAILURE;
        }

        $keep = (int) $keep;
        $files = glob(storage_path('logs').DIRECTORY_SEPARATOR.'*.log') ?: [];
        $trimmed = 0;
        $freed = 0;

        foreach ($files as $path) {
            clearstatcache(true, $path);
            $size = (int) @filesize($path);

            if (! $all && $size <= $max) {
                continue;
            }

            try {
                $released = $this->trim($path, $keep);
            } catch (RuntimeException $e) {
                $this->warn($e->getMessage());

                continue;
            }

            if ($released > 0) {
                $trimmed++;
                $freed += $released;
                $this->line(sprintf('Trimmed %s (%s freed).', basename($path), Bytes::format($released)));
            }
        }

        $this->info(sprintf('Trimmed %d log files. Freed up %s.', $trimmed, Bytes::format($freed)));

        return self::SUCCESS;
    }

    /**
     * Keep the last $keep lines of the file, rewriting it in place so handles held by
     * running workers (opened in append mode) keep writing to the same inode.
     *
     * @return int bytes released
     */
    protected function trim(string $path, int $keep): int
    {
        $handle = @fopen($path, 'r+');

        if ($handle === false) {
            throw new RuntimeException("Unable to open {$path}.");
        }

        try {
            flock($handle, LOCK_EX);
            $size = fstat($handle)['size'];
            $start = $keep === 0 ? $size : $this->tailOffset($handle, $size, $keep);

            if ($start === 0) {
                return 0;
            }

            $written = 0;
            $read = $start;

            while (true) {
                // Re-read the size so lines appended while copying are carried over.
                $end = fstat($handle)['size'];

                if ($read >= $end) {
                    break;
                }

                fseek($handle, $read);
                $chunk = (string) fread($handle, min(self::CHUNK, $end - $read));

                if ($chunk === '') {
                    break;
                }

                fseek($handle, $written);
                fwrite($handle, $chunk);
                $read += strlen($chunk);
                $written += strlen($chunk);
            }

            fflush($handle);
            ftruncate($handle, $written);

            return $read - $written;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /**
     * Offset where the last $keep lines begin, scanning backwards in fixed-size chunks
     * so multi-gigabyte logs are never loaded into memory. Returns 0 when the file
     * has no more than $keep lines.
     *
     * @param  resource  $handle
     */
    protected function tailOffset($handle, int $size, int $keep): int
    {
        $position = $size;
        $newlines = 0;
        $skipTrailing = true;

        while ($position > 0) {
            $length = min(self::CHUNK, $position);
            $position -= $length;
            fseek($handle, $position);
            $chunk = (string) fread($handle, $length);

            for ($i = strlen($chunk) - 1; $i >= 0; $i--) {
                if ($chunk[$i] !== "\n") {
                    $skipTrailing = false;

                    continue;
                }

                if ($skipTrailing) {
                    $skipTrailing = false;

                    continue;
                }

                if (++$newlines === $keep) {
                    return $position + $i + 1;
                }
            }
        }

        return 0;
    }
}
