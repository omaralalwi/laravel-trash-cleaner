<?php

namespace Omaralalwi\LaravelTrashCleaner\Tests;

use Omaralalwi\LaravelTrashCleaner\LaravelTrashCleanerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [LaravelTrashCleanerServiceProvider::class];
    }
}
