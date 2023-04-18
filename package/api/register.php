<?php
session_start();
include '../db/mysql.php';
include '../utils/corsSet.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $response = array(
        'status_code' => 0,
        'status_message' => 'The username has already been taken',
    );  
} else {
    $sql = "INSERT INTO `users` (username, password) VALUES ('$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $response = array(
            'status_code' => 1,
            'status_message' => 'success',
        );  
    } else {
        $response = array(
            'status_code' => 0,
            'status_message' => 'Register failed',
        );  
    }
}

echo json_encode($response);
//echo 'username: ' . $username;