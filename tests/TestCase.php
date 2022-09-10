<?php

namespace RCM\LaraHierarchy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
