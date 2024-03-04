<?php
	include '../server/server.php';

	// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: owner_info.php");
    }
}

	// Check if the user is logged in as an administrator
	if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
		// Redirect to the previous page or handle as needed
		header("Location: " . $_SERVER["HTTP_REFERER"]);
		exit();
	}

	// Use isset() to check if 'OwnerID' is set in the GET parameters
	if (!isset($_GET['OwnerID']) || empty($_GET['OwnerID'])) {
		$_SESSION['message'] = 'Missing or invalid Pet Owner ID!';
		$_SESSION['success'] = 'danger';
		header("Location: ../owner_info.php");
		exit();
	}

	// Use prepared statement to prevent SQL injection
	$id = $conn->real_escape_string($_GET['OwnerID']);
	
	// Use prepared statement to delete the pet owner
	$query = "DELETE FROM tblowner WHERE OwnerID = ?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $id);
	$stmt->execute();

	// Check affected rows before closing the statement
	if ($stmt->affected_rows > 0) {
		$_SESSION['message'] = 'Pet Owner has been removed!';
		$_SESSION['success'] = 'danger';
	} else {
		$_SESSION['message'] = 'Error: Cannot delete if a pet owner has an existing records in Revenues'; // Capture the error message
		$_SESSION['success'] = 'danger';
	}

	$stmt->close(); // Close the statement after checking affected rows

	// Redirect to the owner_info.php page
	header("Location: ../owner_info.php");
	$conn->close();
?>
