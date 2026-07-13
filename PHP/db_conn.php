<?php

$config = require __DIR__ . '/config.php';

$servername = $config['db_host'];
$username = $config['db_user'];
$password = $config['db_pass'];
$dbname = $config['db_name'];   

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("Internal server error.");
}
?>