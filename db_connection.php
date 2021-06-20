<?php

//constant variables for connection to the database
define('DATABASE_HOST', 'localhost');
define('DATABASE_USERNAME', 'root');
define('DATABASE_PASSWORD', 'usbw');
define('DATABASE_NAME', 'userdb_33388');

try {

    //creating a connection to the database
    $DNS = 'mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME;
    $connection = new PDO($DNS, DATABASE_USERNAME, DATABASE_PASSWORD);

} catch (PDOException $ex) {
    //throw exception if database connection fails
    throw $ex;
    exit;
}