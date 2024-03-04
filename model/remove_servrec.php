<?php
include '../server/server.php';

// CSRF protection
// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: pet_services.php");
    }
}

// Check user role and authentication
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    // Redirect the user if they are not an administrator
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
}

// Retrieve the recordID from the query parameter
$id = isset($_GET['recordID']) ? $conn->real_escape_string($_GET['recordID']) : '';

if ($id != '') {
    // Use prepared statement to prevent SQL injection
    $query = "DELETE FROM tblrecordservice WHERE recordID = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $id);

        // Execute the prepared statement
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Service Record has been removed!';
            $_SESSION['success'] = 'danger';
        } else {
            $_SESSION['message'] = 'Error: ' . $stmt->error;
            $_SESSION['success'] = 'danger';
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        $_SESSION['message'] = 'Error preparing the statement!';
        $_SESSION['success'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Missing Service Record ID!';
    $_SESSION['success'] = 'danger';
}

// Redirect back to the page where the user initiated the delete action
header("Location: " . $_SERVER["HTTP_REFERER"]);
$conn->close();
exit();
?>
