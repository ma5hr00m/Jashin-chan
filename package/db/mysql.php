<?php
// declare parameters
$host = "localhost";
$user =  "val";
$pass = "val123!@#";
$dbname = "hello";

// create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// detect errors
if ($conn->connect_error) {
    die("Conect Error: " . $conn->connect_error);
};

?>