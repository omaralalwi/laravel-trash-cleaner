<?php

return [

    'schedule' => false,
    'frequency' => 'daily',

    /**
     * Path cleanup targets (glob supported)
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
];
