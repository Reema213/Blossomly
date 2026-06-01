<?php
$conn = "mysql:host=localhost;dbname=blossomly";
$username = "root";
$password = ""; 

try {
    $pdo = new PDO($conn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>