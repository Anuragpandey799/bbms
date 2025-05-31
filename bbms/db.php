<?php
$host = "localhost";
$user = "root";         // your MySQL username
$pass = "";             // your MySQL password
$db = "blood_bank";     // your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
