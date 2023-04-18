<?php
$host = "localhost";
$user = "val";
$pass = "val123!@#";
$dbname = "hello";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connect Error: " . $conn->connect_error);
}

?>