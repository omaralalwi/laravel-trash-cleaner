<?php

namespace Omaralalwi\LaravelTrashCleaner;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Omaralalwi\LaravelTrashCleaner\Skeleton\SkeletonClass
 */
class LaravelTrashCleanerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-trash-cleaner';
    }
}
