<?php
// Start or resume a session
session_start();

// Include server.php for database connection
include '../server/server.php';

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger";
        header("Location: petowner.php");
        exit; // Added to prevent further execution
    }
}

// Check user authentication and role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit; // Added to prevent further execution
    }
}

// Retrieve and sanitize the pet ID
$id = $conn->real_escape_string($_GET['id']);

if ($id != '') {
    // Perform the delete query using prepared statement
    $query = "DELETE FROM tblpet WHERE id = ?";
    $stmtDelete = $conn->prepare($query);
    $stmtDelete->bind_param("i", $id);

    if ($stmtDelete->execute()) {
        $_SESSION['message'] = 'Pet has been removed!';
        $_SESSION['success'] = 'danger';
    } else {
        $_SESSION['message'] = 'Something went wrong!';
        $_SESSION['success'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Missing Pet ID!';
    $_SESSION['success'] = 'danger';
}

// Redirect to the petowner page
header("Location: ../petowner.php");
$conn->close();
