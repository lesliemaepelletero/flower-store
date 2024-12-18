<?php

@include 'config.php';

session_start();

// Check if user session exists before logging the logout
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Log the logout action
    $action = 'Logout';
    $description = 'User logged out successfully';
    $log_query = "INSERT INTO activity_log (user_id, action, description) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $log_query, array($user_id, $action, $description));
}

// Unset all session variables and destroy the session
session_unset();
session_destroy();

// Redirect to the login page
header('location:login.php');
?>
