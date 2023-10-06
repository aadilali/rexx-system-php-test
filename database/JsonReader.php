<?php

require_once 'VersionComparator.php';

/**
 * JsonReader class responsible for reading events data from Json file
 * and storing the retrieved data into database
 */
class JsonReader {
    private $dbConnection;

    public function __construct(DatabaseConnectionInterface $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function uploadData() {
        $jsonData = file_get_contents('DEV_Events_Full.json');
        $data = json_decode($jsonData, true);

        $this->dbConnection->connect();
        $mysqli = $this->dbConnection->getMysqli();
       
        $this->emptyTables();

        // Insert JSON data into the database
        foreach ($data as $entry) {
            $participationId = $mysqli->real_escape_string($entry['participation_id']);
            $employeeName = $mysqli->real_escape_string($entry['employee_name']);
            $employeeMail = $mysqli->real_escape_string($entry['employee_mail']);
            $eventId = intval($entry['event_id']);
            $eventName = $mysqli->real_escape_string($entry['event_name']);
            $participationFee = floatval($entry['participation_fee']);
            $eventDate = $this->convertTimeZone($entry['event_date'], $entry['version']);
            $version = $mysqli->real_escape_string($entry['version']);

            // Insert data into the 'employees' table
            $employeeInsertSql = "INSERT INTO employees (name, email) VALUES ('$employeeName', '$employeeMail')";
            $this->dbConnection->query($employeeInsertSql);

            // Retrieve the auto-generated employee ID
            $employeeId = $mysqli->insert_id;

            // Insert data into the 'events' table
            $eventInsertSql = "INSERT INTO events (name) VALUES ('$eventName')";
            $this->dbConnection->query($eventInsertSql);

            // Retrieve the auto-generated event ID
            $eventId = $mysqli->insert_id;

            // Insert data into the 'participations' table
            $participationInsertSql = "INSERT INTO participations (participation_id, employee_id, event_id, participation_fee, event_date, version) VALUES ('$participationId', $employeeId, $eventId, $participationFee, '$eventDate', '$version')";
            $this->dbConnection->query($participationInsertSql);
        }

        $this->dbConnection->disconnect();
    }

    // Function to empty tables
    public function emptyTables() {
       
        // Disable foreign key checks
       $this->dbConnection->query('SET FOREIGN_KEY_CHECKS = 0');
   
       // Define the tables you want to empty
       $tables = ['employees', 'events', 'participations'];
   
       foreach ($tables as $table) {
           $sql = "TRUNCATE TABLE $table"; // You can use TRUNCATE or DELETE depending on your needs
           $this->dbConnection->query($sql);
       }
   
       // Re-enable foreign key checks
       $this->dbConnection->query('SET FOREIGN_KEY_CHECKS = 1');
   
    }

    public function convertTimeZone($dateString, $version) {
        // Compare version to determine the timezone
        $berlinTimeZoneVersion = '1.0.17+60';
    
        if (VersionComparator::compare($version, $berlinTimeZoneVersion) <= 0) {
            // Use Europe/Berlin timezone for older versions
            $timezone = new DateTimeZone('Europe/Berlin');
        } else {
            // Use UTC timezone for newer versions
            $timezone = new DateTimeZone('UTC');
        }
    
        $date = new DateTime($dateString);
        $date->setTimezone($timezone);
    
        return $date->format('Y-m-d H:i:s');
    }
}

