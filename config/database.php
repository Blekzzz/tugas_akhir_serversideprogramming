<?php
    class Database {
        protected $conn;

        public function __construct() {
            $this->connect();
        }

        protected function connect() {
            $host = "127.0.0.1";
            $user = "root";
            $pass = "";
            $db   = "fixit_db";
            $port = 3306;

            $this->conn = new mysqli($host, $user, $pass, $db, $port);

            if ($this->conn->connect_error) {
                die("Koneksi gagal: " . $this->conn->connect_error);
            }
        }

        public function getConnection() {
            return $this->conn;
        }
    }
?>