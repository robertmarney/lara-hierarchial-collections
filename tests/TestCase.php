<?php

namespace RCM\LaraHierarchy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RCM\LaraHierarchy\LaraHierarchyServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaraHierarchyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
