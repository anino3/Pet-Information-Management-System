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
        $_SESSION['success'] = "danger";
        header("Location: forgot_password.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Get the email submitted by the user
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate the email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Invalid email address';
        $_SESSION['success'] = 'danger';
        header('location: ../forgot_password.php');
        exit();
    }

    // Check if the email exists in the database
    $query = "SELECT * FROM tbl_users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error in preparing SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error in executing SQL statement: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        // User exists, generate a unique token
        $token = bin2hex(random_bytes(32));

        // Store the token in the database along with the user's ID and expiration time
        $expiration_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $update_query = "UPDATE tbl_users SET reset_token=?, reset_token_expiration=? WHERE email=?";
        $update_stmt = $conn->prepare($update_query);

        if (!$update_stmt) {
            die("Error in preparing UPDATE statement: " . $conn->error);
        }

        $update_stmt->bind_param("sss", $token, $expiration_time, $email);

        if (!$update_stmt->execute()) {
            die("Error in executing UPDATE statement: " . $update_stmt->error);
        }

        // Include PHPMailer autoloader
        require '../vendor/autoload.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'franciabritz17@gmail.com'; // Replace with your email
            $mail->Password   = 'jnjozetrhvllvbhl'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('franciabritz17@gmail.com'); // Replace with your email
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $reset_link = "http://localhost/vetims/change_password.php?token=$token";

            $mail->Body = "You have requested to reset your password. Please click on the following link to reset your password:<br><a href='$reset_link'>$reset_link</a>";

            $mail->send();

            $_SESSION['message'] = 'Password reset link sent to ' . $email;
            $_SESSION['success'] = 'success';

            header('location: ../forgot_password.php');
            exit();

        } catch (PHPMailer\PHPMailer\Exception $e) {
            die("Message could not be sent. Mailer Error: " . $e->getMessage());
        }
    } else {
        // User does not exist with the provided email address
        $_SESSION['message'] = 'No user found with this email address';
        $_SESSION['success'] = 'danger';
        header('location: ../forgot_password.php');
        exit();
    }
} else {
    // Invalid request method or missing parameters
    http_response_code(400);
    echo "Bad Request";
}

$conn->close();
?>
