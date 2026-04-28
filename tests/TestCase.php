<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected static bool $tenantTestMigrated = false;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->configureTestingTenantConnections();

        // Executar migrações se necessário
        $this->runMigrationsIfNeeded();
    }

    protected function configureTestingTenantConnections(): void
    {
        if (!app()->environment('testing')) {
            return;
        }

        $database = env('DB_TEST_DATABASE', database_path('testing.sqlite'));

        config([
            'database.connections.tenant_credentials' => [
                'driver' => 'sqlite',
                'database' => $database,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.connections.tenant_content' => [
                'driver' => 'sqlite',
                'database' => $database,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);
    }
    
    protected function runMigrationsIfNeeded(): void
    {
        if (!app()->environment('testing')) {
            return;
        }

        if (self::$tenantTestMigrated) {
            return;
        }

        Artisan::call('migrate:fresh', [
            '--database' => 'tenant_content',
            '--path' => 'database/migrations/content',
            '--force' => true,
        ]);

        self::$tenantTestMigrated = true;
    }
}
