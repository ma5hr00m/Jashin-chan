<?php
include '../db/mysql.php';
include '../utils/corsSet.php';

if ($conn->connect_error) {
    $response = array(
        'status_code' => 0,
        'status_message' => 'failed to connect to database',
    );
} else {
    $response = array(
        'status_code' => 1,
        'status_message' => 'successfully connected to database',
    );
}

echo json_encode($response);
?>
