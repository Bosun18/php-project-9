<?php

namespace Bosun\PhpProject9;

class Database
{
    protected \PDO $pdo;

    public function __construct()
    {
        $databaseUrl = parse_url(getenv('DATABASE_URL'));
        $username = $databaseUrl['user'];
        $password = $databaseUrl['pass'];
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'];
        $dbName = ltrim($databaseUrl['path'], '/');
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbName};user={$username};password={$password}";
        $this->pdo = new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
