<?php

namespace RCM\LaraHierarchy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RCM\LaraHierarchy\HierarchicalServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            HierarchicalServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
