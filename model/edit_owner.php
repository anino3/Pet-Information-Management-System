<?php
include '../server/server.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
}

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: owner_info.php");
        exit;
    }
}

// Escape user inputs to prevent SQL injection
$ownerid = $conn->real_escape_string($_POST['OwnerID']);
$oname = $conn->real_escape_string($_POST['oname']);
$oplace = $conn->real_escape_string($_POST['oplace']);
$ocity = $conn->real_escape_string($_POST['ocity']);
$zcode = $conn->real_escape_string($_POST['zcode']);
$onumber = $conn->real_escape_string($_POST['onumber']);
$email = $conn->real_escape_string($_POST['email']);

// Use prepared statements for SQL query
$query = "UPDATE tblowner SET OwnerName=?, OwnerAddress=?, OwnerCity=?, OwnerZip=?, OwnerMobileNo=?, OwnerEmail=? WHERE OwnerID=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssdsi", $oname, $oplace, $ocity, $zcode, $onumber, $email, $ownerid);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['message'] = 'Owner Information has been updated!';
    $_SESSION['success'] = 'success';
} else {
    $_SESSION['message'] = 'Failed to update owner information.';
    $_SESSION['success'] = 'danger';
}

header("Location: ../owner_info.php");
$conn->close();
?>
