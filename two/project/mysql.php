<?php
$iniFile = "config.ini";
$config = parse_ini_file($iniFile, true);

$host = $config['database']['host'];
$user = $config['database']['user'];
$pass = $config['database']['pass'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connect Error: " . $conn->connect_error);
}
?>