<?php

namespace Bosun\PhpProject9;

class Database
{
    private static ?Database $connection = null;

    protected function __construct()
    {
    }

    public function connect()
    {
        $databaseUrl = parse_url($_ENV('DATABASE_URL'));
        $username = $databaseUrl['user'];
        $password = $databaseUrl['pass'];
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'];
        $dbName = ltrim($databaseUrl['path'], '/');
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbName}";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
        try {
            $pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $pdo;
    }

    public static function get(): ?Database
    {
        if (static::$connection === null) {
            static::$connection = new self();
        }
        return static::$connection;
    }
}
