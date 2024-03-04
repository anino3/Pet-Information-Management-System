<?php
session_start();

$database = 'petshop';
$username = 'root';
$host = 'localhost';
$password = '';

$conn = new mysqli($host, $username, $password, $database);

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: change_password.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'], $_POST['token'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match";
    } else {
        // Check if the token exists in the database
        $query = "SELECT * FROM tbl_users WHERE reset_token = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            $_SESSION['message'] = "Error in preparing SQL statement: " . $conn->error;
            $_SESSION['success'] = 'danger';

            
        } else {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                $_SESSION['message'] = "Error in executing SQL statement: " . $stmt->error;
                $_SESSION['success'] = 'danger';

                header('location: ../change_password.php');
                exit(); // Stop execution after redirection
                
            } else {
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $user_id = $user['id'];

                    // Update the user's password and clear the reset token
                    $update_query = "UPDATE tbl_users SET password = ?, reset_token = NULL, reset_token_expiration = NULL WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);

                    if (!$update_stmt) {
                        $_SESSION['message'] = "Error in preparing UPDATE statement: " . $conn->error;
                        $_SESSION['success'] = 'danger';

                        header('location: ../change_password.php');
                        exit(); // Stop execution after redirection
                        
                        
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $update_stmt->bind_param("si", $hashed_password, $user_id);

                        if (!$update_stmt->execute()) {
                            $_SESSION['message'] = "Error in preparing UPDATE statement: " . $conn->error;
                            $_SESSION['success'] = 'danger';
                            header('location: ../change_password.php');
                            exit(); // Stop execution after redirection
                            
                        } else {
                            $_SESSION['message'] = "Password updated successfully!";
                            $_SESSION['success'] = 'success';
                            header('location: ../change_password.php');
                            exit(); // Stop execution after redirection
                            
                        }
                    }
                } else {
                    $_SESSION['message'] = "Invalid or expired token";
                    $_SESSION['success'] = 'danger';
                    header('location: ../change_password.php');
                    exit(); // Stop execution after redirection
                    
                }
            }
        }
    }
} else {
    // Invalid request method or missing parameters
 
    $_SESSION['message'] = "Bad Request";
    $_SESSION['success'] = 'danger';
    header('location: ../change_password.php');
    exit(); // Stop execution after redirection
                    
    
}

$conn->close();
?>
