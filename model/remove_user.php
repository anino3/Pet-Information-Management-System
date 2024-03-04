<?php
// Include server.php for database connection
include '../server/server.php';

// Check if the user is not logged in as an administrator
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    // Redirect to the previous page or handle as needed
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    exit(); // Added to prevent further execution
}

// Use isset() to check if 'id' is set in the GET parameters
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'Missing or invalid User ID!';
    $_SESSION['success'] = 'danger';
    header("Location: ../users.php");
    exit();
}

// Use prepared statement to prevent SQL injection
$id = $conn->real_escape_string($_GET['id']);

// Use prepared statement to delete the user
$query = "DELETE FROM tbl_users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

// Check affected rows before closing the statement
if ($stmt->affected_rows > 0) {
    $_SESSION['message'] = 'User has been removed!';
    $_SESSION['success'] = 'danger';
} else {
    $_SESSION['message'] = 'Something went wrong!';
    $_SESSION['success'] = 'danger';
}

$stmt->close(); // Close the statement after checking affected rows

// Redirect to the users.php page
header("Location: ../users.php");
$conn->close();
?>
