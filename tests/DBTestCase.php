<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\DB;

abstract class DBTestCase extends TestCase
{
    /**
     * Indicates if migrations ran.
     *
     * @var bool
     */
    protected static $migrated = false;

    public function setUp(): void
    {
        parent::setUp();

        if (! static::$migrated) {
            $this->artisan('migrate:fresh', [
                '--path' => __DIR__.'/database/migrations',
                '--realpath' => true,
            ]);

            static::$migrated = true;
        }

        // Ensure we start from a clean slate each time
        // We cannot use transactions, as they do not reset autoincrement
        $databaseName = env('LIGHTHOUSE_TEST_DB_DATABASE') ?? 'lighthouse';
        $columnName = "Tables_in_{$databaseName}";
        foreach (DB::select('SHOW TABLES') as $table) {
            DB::table($table->{$columnName})->truncate();
        }

        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make(ConfigRepository::class);

        $config->set('database.default', 'mysql');
        $config->set('database.connections.mysql', $this->mysqlOptions());
    }

    /**
     * @return array<string, mixed>
     */
    protected function mysqlOptions(): array
    {
        $mysqlOptions = [
            'driver' => 'mysql',
            'database' => env('LIGHTHOUSE_TEST_DB_DATABASE', 'test'),
            'username' => env('LIGHTHOUSE_TEST_DB_USERNAME', 'root'),
            'password' => env('LIGHTHOUSE_TEST_DB_PASSWORD', ''),
        ];

        $socket = env('LIGHTHOUSE_TEST_DB_UNIX_SOCKET') ?? null;
        if (is_string($socket)) {
            $mysqlOptions['unix_socket'] = $socket;
        } else {
            $mysqlOptions['host'] = env('LIGHTHOUSE_TEST_DB_HOST', 'mysql');
            $mysqlOptions['port'] = env('LIGHTHOUSE_TEST_DB_PORT', '3306');
        }

        return $mysqlOptions;
    }
}
