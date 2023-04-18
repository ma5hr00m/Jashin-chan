<?php  
session_start();
include '../db/mysql.php';
include '../utils/corsSet.php';

$username = $_POST["username"];
$password = $_POST['password'];

$sql = "SELECT * FROM `users` WHERE username = '$username'";  
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($row['password'] == $password) {
        $_SESSION['username'] = $username;
        $_SESSION['logged'] = true;

        $response = array(
            'status_code' => 1,
            'username' => $username,
            'status_message' => 'success',
        );  
    } else {  
        $response = array(  
            'status_code' => 0,  
            'status_message' => 'failed',  
        );  
    }  
} else {  
    $response = array(
        'status_code' => 0,  
        'status_message' => 'failed',  
    );  
}  

echo json_encode($response);
//echo 'username: ' . $username;