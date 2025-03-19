<?php

namespace Telemedizin\TelemedizinBundle\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Telemedizin\TelemedizinBundle\TelemedizinServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withFactories(__DIR__ . '/../src/Database/Factories');
        
        // Migrationen ausführen
        $this->loadMigrationsFrom(__DIR__ . '/../src/Database/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            TelemedizinServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Datenbank-Konfiguration für Tests
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
} 