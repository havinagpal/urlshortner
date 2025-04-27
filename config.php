<?php
// Database configuration
$host = 'localhost'; // Usually 'localhost' on shared hosting
$dbname = 'webontre_link_shortener';
$username = 'webontre_link_shortener'; // Replace with your database username
$password = 'eRhd5tRLxQQXeSWHtCxq'; // Replace with your database password

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Base URL of your website
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/"; // Adjust if your site is in a subdirectory
?>

