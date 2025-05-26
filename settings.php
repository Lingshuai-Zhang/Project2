<?php
// Database connection settings
$host = "localhost";
$user = "root";  // Default XAMPP MySQL username
$password = "";  // Default XAMPP MySQL password
$dbname = "project2_db";  // Database name for the project

// Define database table names
$jobs_table = "jobs";
$eoi_table = "eoi";
$manager_table = "managers";

// Define attempt tracking session name
$attempt_session = "login_attempts";

// Try to connect to database without specifying a database name
$conn_without_db = @mysqli_connect($host, $user, $password);

// If basic connection is successful, check if database exists
if ($conn_without_db) {
    // Try to create the database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (mysqli_query($conn_without_db, $sql)) {
        // Database created or already exists
        $db_created = true;
    } else {
        // Database creation failed
        echo "Error creating database: " . mysqli_error($conn_without_db);
        $db_created = false;
    }
    
    // Close connection without database
    mysqli_close($conn_without_db);
} else {
    // Failed to connect to MySQL server
    echo "Failed to connect to MySQL server: " . mysqli_connect_error();
    $db_created = false;
}
?> 