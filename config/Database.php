<?php

class Database {
    private $host = "localhost";
    private $db_name = "unity_care_clinic";
    private $username = "root";
    private $password = "";
    private $conn;

    public function dbConnection() {
        try{
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name" , $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }        
    }
}
