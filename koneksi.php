<?php
if (!class_exists('Database')) {
    class Database {

        //hosting
        // private $host = "localhost";
        // private $db_name = "ewab5183_d-esdm";
        // private $username = "ewab5183_root";
        // private $password = "d-esdm123*";

        //hostlaptop
        private $host = "localhost";
        private $db_name = "sippinda";
        private $username = "root";
        private $password = "";

        public $conn;

        public function getConnection() {
            $this->conn = null;
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }
}
