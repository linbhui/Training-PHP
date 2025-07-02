<?php
class Database {
    // Set database properties
    public $connection;
    protected $servername = "localhost";
    protected $username = "testMe";
    protected $password = "Je/Y5Xi_k*aai6n@";
    protected $dbname = "training";

    // Construct database
    function __construct() {
        $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->connection->connect_error) {
            echo $this->connection->connect_error;
        }

    }
}
