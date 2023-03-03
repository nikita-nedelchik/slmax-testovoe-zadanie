<?php

namespace App\Class;

final class PeopleDatabase
{
    private $connection;
    public static ?self $instance = null;
    private static string $adminName = 'root';
    private static string $adminPassword = '';

    public function __wakeup()
    {
        throw new \Exception("We are use Database class as Singleton");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        try {
            $dsn = 'mysql:host=test;port=3306;dbname=test;charset=utf8';
            $this->connection = new \PDO($dsn, self::$adminName, self::$adminPassword);
            $this->connection->exec('
                CREATE TABLE IF NOT EXISTS people (
                    `id` int unsigned PRIMARY KEY AUTO_INCREMENT,
                    `first_name` varchar(100) not null,
                    `last_name` varchar(100) not null,
                    `birth_date` date not null,
                    `gender` int not null ,
                    `birth_place` varchar (100) not null
             )');

        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        }
        return $this->connection;
    }

    public static function getWhereStatement(array $data): string
    {
        $where = '';
        $where = count($data) === 1 ? "WHERE id = $data[0]" : '';
        if ($where === "") {
            foreach ($data as $key => $value) {
                if ($key === 0) {
                    $value = (int)$value;
                    $where .= "WHERE id = $value";
                } else {
                    $value = (int)$value;
                    $where .= " OR id = $value";
                }
            }
        }

        return $where;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
