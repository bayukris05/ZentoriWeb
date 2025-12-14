<?php

namespace App\config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function connect()
    {
        if (self::$instance === null) {
            $host = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASS;
            $port = defined('DB_PORT') ? DB_PORT : 3306;

            try {
                self::$instance = new PDO(
                    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                    $username,
                    $password
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage() . " (Host: $host, Port: $port)");
            }
        }

        return self::$instance;
    }
}
