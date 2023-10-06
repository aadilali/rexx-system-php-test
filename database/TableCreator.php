<?php

require_once 'MySqlConnection.php';

/**
 * TableCreator class responsible for creating our database schema
 */
class TableCreator {
    private $dbConnection;

    public function __construct(DatabaseConnectionInterface $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function createTables() {
        $queries = [
            "CREATE TABLE IF NOT EXISTS employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS participations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                participation_id VARCHAR(255) NOT NULL,
                employee_id INT NOT NULL,
                event_id INT NOT NULL,
                participation_fee DECIMAL(10, 2) NOT NULL,
                event_date DATETIME NOT NULL,
                version VARCHAR(255) NOT NULL,
                FOREIGN KEY (employee_id) REFERENCES employees (id),
                FOREIGN KEY (event_id) REFERENCES events (id)
            )"
        ];
    

        $this->dbConnection->connect();
        foreach ($queries as $query) {
            $this->dbConnection->query($query);
        }
        $this->dbConnection->disconnect();
    }
}

