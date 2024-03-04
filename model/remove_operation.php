<?php
// Start or resume a session
session_start();

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger";
        header("Location: tbloperation.php");
        exit; // Added to prevent further execution
    }
}

include '../server/server.php';

// Check user authentication and role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit; // Added to prevent further execution
    }
}

// Retrieve and sanitize the operation record ID
$id = $conn->real_escape_string($_GET['id']);

if ($id != '') {
    // Perform the delete query
    $query = "DELETE FROM tbloperation WHERE id = '$id'";
    $result = $conn->query($query);

    if ($result === true) {
        $_SESSION['message'] = 'Operation record has been removed!';
        $_SESSION['success'] = 'danger';
    } else {
        $_SESSION['message'] = 'Something went wrong!';
        $_SESSION['success'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Missing Operation Record ID!';
    $_SESSION['success'] = 'danger';
}

// Redirect to the operation page
header("Location: ../operation.php");
$conn->close();
