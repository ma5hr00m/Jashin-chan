<?php  
include '../db/mysql.php';

$username = $_POST["username"];
$password = $_POST['password'];

$sql = "SELECT * FROM `usrs` WHERE username = '$username'";  
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($row['password'] == $password) {
        $_SESSION['username'] = $username;
        $_SESSION['logged'] = true;

        $response = array(
            'status_code' => 1,
            'status_message' => 'success',
        );  
    } else {  
        $response = array(  
            'status_code' => 0,  
            'status_message' => 'failed',  
        );  
    }  
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Content-Type: application/json');

echo $username;