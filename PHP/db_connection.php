<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "watch_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log('Connection failed: ' . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}
?>