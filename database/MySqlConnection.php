<?php
require_once 'DatabaseConfig.php';
interface DatabaseConnectionInterface {
    public function connect();
    public function disconnect();
    public function query($sql);
}

/**
 * MySQL connection class responsible to connect, run queries and disconnect the connection 
 */
class MySqlConnection implements DatabaseConnectionInterface {
    private $connection;

    public function connect() {
        $this->connection = new mysqli(
            DatabaseConfig::DB_HOST,
            DatabaseConfig::DB_USER,
            DatabaseConfig::DB_PASS,
            DatabaseConfig::DB_NAME
        );

        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function disconnect() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    // Add a method to get the mysqli object
    public function getMysqli() {
        return $this->connection;
    }

}
