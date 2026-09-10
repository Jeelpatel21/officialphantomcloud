<?php
// Google Sheet Database: https://docs.google.com/spreadsheets/d/1KivF1gWUzPHOsw3tLeBOrgunsdEjo5-UJLTjJRHyKuA/edit?usp=sharing
// Configuration details available in sheet_config.json & GOOGLE_SHEETS_SETUP.md
$host = "localhost";
$user = "root";
$pass = "";
$db   = "phantom_cloud";

$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>