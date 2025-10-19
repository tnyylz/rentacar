<?php

namespace App;

use mysqli;

class Database {
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $name = 'rentacar_db';

    // new Database() ile dışarıdan nesne oluşturulmasını engelle
    private function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->name);
        $this->connection->set_charset("utf8mb4");

        if ($this->connection->connect_error) {
            die("Veritabanı bağlantı hatası: " . $this->connection->connect_error);
        }
    }

    /**
     * Veritabanı bağlantısını statik olarak döndürür.
     * Eğer bağlantı daha önce kurulmamışsa, kurar.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Bağlantı nesnesini döndürür.
     */
    public function getConnection() {
        return $this->connection;
    }
}