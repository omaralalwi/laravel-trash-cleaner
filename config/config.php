<?php

/**
 * Configuration for the Trash Cleaner package.
 *
 * @package Omaralalwi\LaravelTrashCleaner
 */

return [
    /**
     * Determines if the scheduled task for cleaning up debug files should be enabled.
     *
     * Set to true to enable the automatic cleanup task.
     *
     * @var bool
     */

    'schedule' => false,

    /**
     * Defines the frequency at which the cleanup task should run.
     *
     * Accepts any valid frequency string supported by Laravel's task scheduler, such as 'hourly', 'daily', etc.
     *
     * @var string
     */

    'frequency' => 'daily',
];
