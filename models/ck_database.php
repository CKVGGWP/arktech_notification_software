<?php

class Database
{
    private $host = "localhost";
    private $dbName = "arktechdatabase";
    private $username = "root";
    private $password = "arktechdb";

    protected function connect()
    {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->dbName);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        } else {
            return $this->conn;
        }
    }
}
