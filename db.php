<?php
$hostname="localhost";
$username="root";       
$password= "";
$database= "collab_db";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    // echo "Connected successfully to the database.";
}

?>
