<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "fixit_db";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed to Database: " . $conn->connect_error);
}