<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "mutua_db";

$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$sqlCreateDb = "CREATE DATABASE IF NOT EXISTS `$database`";
if (!mysqli_query($conn, $sqlCreateDb)) {
    die("Failed to create database: " . mysqli_error($conn));
}

if (!mysqli_select_db($conn, $database)) {
    die("Failed to select database: " . mysqli_error($conn));
}

$sqlCreateTable = "CREATE TABLE IF NOT EXISTS `user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `FullName` VARCHAR(100) NOT NULL,
    `Email` VARCHAR(100) NOT NULL UNIQUE,
    `networktype` VARCHAR(50) DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `gender` VARCHAR(20) DEFAULT NULL,
    `Password` VARCHAR(255) NOT NULL
)";

if (!mysqli_query($conn, $sqlCreateTable)) {
    die("Failed to create users table: " . mysqli_error($conn));
}
?>

