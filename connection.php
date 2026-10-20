<?php
// CONNECT TO MYSQL
$connection = mysqli_connect("localhost", "root", "");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// CREATE AND SELECT THE PROJECT DATABASE
if (!mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS `data-submission`")) {
    die("Database setup failed: " . mysqli_error($connection));
}

if (!mysqli_select_db($connection, "data-submission")) {
    die("Could not select the database: " . mysqli_error($connection));
}

mysqli_set_charset($connection, "utf8mb4");

// CREATE THE STUDENTS TABLE IF IT DOES NOT EXIST
$createTable = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    course VARCHAR(100) NOT NULL
)";

if (!mysqli_query($connection, $createTable)) {
    die("Table setup failed: " . mysqli_error($connection));
}
?>