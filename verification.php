<?php 
session_start();
$database = 'petshop';
$username = 'root';
$host = 'localhost';
$password = '';

$conn = new mysqli($host, $username, $password, $database);


if (isset($_GET['verification_code'])) {
    $verification_code = $_GET['verification_code'];

    $query = "SELECT * FROM tbl_users WHERE verification_code = ? AND verified = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $update_query = "UPDATE tbl_users SET verified = 1, verification_code = '' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $row['id']);
        $update_result = $update_stmt->execute();

        if ($update_result) {
            $_SESSION['message'] = 'Account verification has been successfully completed.';
            $_SESSION['success'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update user record.';
            $_SESSION['success'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Invalid or expired verification code.';
        $_SESSION['success'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Invalid verification link.';
    $_SESSION['success'] = 'danger';
}

header("Location: login.php");
$conn->close();
?>
