<?php

$servername = "127.0.0.1";  
$username = "root";          
$password = ""; 
$dbname = "VetAssist";       

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("Internal server error.");
}
?>