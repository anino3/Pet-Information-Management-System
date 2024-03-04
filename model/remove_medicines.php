<?php
// Start the session
session_start();

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: medicines.php");
    }
}

// Check if the user is not logged in or is not an administrator
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'administrator')) {
    // Redirect to the referring page or any desired location
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        // Redirect to a default page if HTTP_REFERER is not set
        header("Location: index.php");
    }
    exit(); // Ensure that the script stops executing after the redirect
}

// Check if medID is provided in the URL
if (isset($_GET['id'])) {
    $medID = $_GET['id'];

    // Include your database connection file
    include '../server/server.php';

    // Prepare and execute the SQL query to delete the medicine with the given medID
    $query = "DELETE FROM tblmedi_mat WHERE medID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $medID);

    if ($stmt->execute()) {
        // Medicine successfully deleted
        $_SESSION['message'] = 'Medicine has been removed!';
        $_SESSION['success'] = 'success';
    } else {
        // Error in deletion
        $_SESSION['message'] = 'Failed to remove medicine. Please try again.';
        $_SESSION['success'] = 'danger';
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // If medID is not provided in the URL, redirect to an error page or the referring page
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['success'] = 'danger';
}

// Redirect to the referring page or any desired location
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
} else {
    // Redirect to a default page if HTTP_REFERER is not set
    header("Location: medicines.php");
}
?>
