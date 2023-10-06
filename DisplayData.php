<?php

/**
 * DisplayData class responsible to get data from data and pass to index.php
 */
class DisplayData
{
    private $dbConnection;

    public function __construct(DatabaseConnectionInterface $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function filterData($sql) {
        $this->dbConnection->connect();
        $data = $this->dbConnection->query($sql);
        $this->dbConnection->disconnect();
        return $data;
    }

    public function getTotalPrice($result) {
        $totalPrice = 0;

        while ($row = $result->fetch_assoc()) {
            $totalPrice += floatval($row['participation_fee']);
        }

        return number_format($totalPrice, 2);
    }
}