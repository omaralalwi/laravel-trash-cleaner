<?php

return [

    /**
     * Register the cleanup commands in Laravel's scheduler.
     */
    'schedule' => false,

    /**
     * Any Laravel schedule frequency method without arguments,
     * e.g. "daily", "hourly", "weekly", "everyFifteenMinutes".
     */
    'frequency' => 'daily',

    /**
     * Paths removed by `trash:clean-assets`, relative to the project root (glob supported).
     * Note: "public/build" holds your compiled Vite assets; remove it from this list
     * if you do not rebuild them right after cleaning.
     */
    'cleanup_paths' => [
        'storage/framework/views/*',
        'public/build',
        'node_modules/.vite',
    ],

    /**
     * Node package manager to use (e.g. "npm", "pnpm", or "yarn")
     */
    'package_manager' => 'npm',

    /**
     * Build commands, relative to the chosen package manager
     */
    'build_commands' => [
        'install',
        'run build',
    ],

    /**
     * Defaults for `trash:clean-logs`, which trims storage/logs/*.log in place.
     */
    'logs' => [
        // Logs larger than this are trimmed. Accepts bytes or a K, M or G suffix.
        'max_size' => '1M',

        // Number of most recent lines kept in a trimmed log.
        'keep_lines' => 500,

        // Run `trash:clean-logs` with the scheduled cleanup when "schedule" is enabled.
        'schedule' => true,
    ],
];
