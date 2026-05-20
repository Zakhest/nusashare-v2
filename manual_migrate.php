<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'nusashare';

$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS transactions (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    amount INT(11) NOT NULL,
    type ENUM('in', 'out') DEFAULT 'in',
    category VARCHAR(50) NOT NULL,
    reference_id INT(11) NULL,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($mysqli->query($sql) === TRUE) {
    echo "Table transactions created successfully\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
}

$mysqli->close();
