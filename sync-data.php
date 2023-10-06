<?php

require_once 'database/MySqlConnection.php';
require_once 'database/TableCreator.php';
require_once 'database/JsonReader.php';

$dbConnection = new MySqlConnection();
$tableCreator = new TableCreator($dbConnection);
$tableCreator->createTables();

// Read Json data and save into database

$jsonReader = new JsonReader($dbConnection);
$jsonReader->uploadData();

echo "<pre>Data is successfully imported into Database</pre>";
echo "<a href='/'>Home Page</a>";
