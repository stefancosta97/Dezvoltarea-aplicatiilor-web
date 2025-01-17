<?php
$servername = "sql207.infinityfree.com";
$username = "if0_38105775"; 
$password = "qakAqOXqMwllhA";     
$dbname = "if0_38105775_hypermarket";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}
?>
