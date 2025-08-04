<?php

namespace Services\Database;

class SLDatabase
{
    private $servername = 'sopnolikhi.com';
    private $database_user = 'toufikhasan_sl_users_user';
    private $database_password = 'XH)@#)3?TWxK';
    private $database_name = 'toufikhasan_sl_users';

    protected function connect(): \PDO | \Exception
    {
        try {
            $database_dsn = "mysql:host={$this->servername};dbname={$this->database_name};charset=utf8";



            $pdo = new \PDO($database_dsn, $this->database_user, $this->database_password, [
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false
            ]);

            return $pdo;
        } catch (\PDOException $exception) {

            return $exception;
            die();
        }
    }
}