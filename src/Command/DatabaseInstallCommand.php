<?php

namespace Aloe\Command;

use \Aloe\Command;

class DatabaseInstallCommand extends Command
{
    protected static $defaultName = 'db:install';
    public $description = 'Create new database from .env variables';
    public $help = 'Create new database from .env variables';

    protected function handle()
    {
        $dbConnection = _env('DB_CONNECTION', MvcConfig('database')['default']);
        $host = _env('DB_HOST', MvcConfig('database')['connections'][$dbConnection]['host']);
        $user = _env('DB_USERNAME', MvcConfig('database')['connections'][$dbConnection]['username']);
        $password = _env('DB_PASSWORD', MvcConfig('database')['connections'][$dbConnection]['password']);
        $database = _env('DB_DATABASE', MvcConfig('database')['connections'][$dbConnection]['database']);
        $port = _env('DB_PORT', MvcConfig('database')['connections'][$dbConnection]['port']);

        if ($dbConnection === 'sqlite') {
            if (!file_exists($database)) {
                file_put_contents($database, '');
            }
            $this->info("$database created successfully.");
            return 0;
        }

        db()->connect([
            'dbtype' => MvcConfig('database')['connections'][$dbConnection]['driver'] ?? 'mysql',
            'host' => $host,
            'username' => $user,
            'password' => $password,
            'port' => $port,
        ]);

        if (db()->create($database)->execute()) {
            $this->info("$database created successfully.");
            return 0;
        }

        $this->error("$database could not be created.");
        return 1;
    }
}
