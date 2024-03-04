<?php
session_start();
$database = 'petshop';
$username = 'root';
$host = 'localhost';
$password = '';

$conn = new mysqli($host, $username, $password, $database);

// Verify reCAPTCHA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptcha_secret = '6LfhYnopAAAAAFmjZhXO3xJLT0g0f0Jv_txs2cOE'; // Replace with your reCAPTCHA Secret Key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR'],
    ];

    $recaptcha_options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($recaptcha_data),
        ],
    ];

    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_data = json_decode($recaptcha_result, true);

    if (!$recaptcha_data['success']) {
        // reCAPTCHA verification failed
        $_SESSION['message'] = 'reCAPTCHA verification failed.';
        $_SESSION['success'] = 'danger';
        header('location: ../login.php');
        exit;
    }
}

// Continue with CSRF protection
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;

if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    $_SESSION['message'] = 'Invalid CSRF token.';
    $_SESSION['success'] = 'danger';
    header('location: ../login.php');
    exit;
}
// User Account Lockout Configuration
$max_attempts = 5; // Maximum number of login attempts allowed
$lockout_duration = 300; // Lockout duration in seconds (300 seconds = 5 minutes)

// Continue with your existing login logic below
$username = $conn->real_escape_string($_POST['username']);
$password = $conn->real_escape_string($_POST['password']);

$login_successful = false; // Initialize the flag

if ($username != '' && $password != '') {
    // Check if the user is currently locked out
    $check_lockout_query = "SELECT * FROM login_attempts WHERE user_id = (SELECT id FROM tbl_users WHERE username = ?) AND last_attempt_timestamp > NOW() - INTERVAL $lockout_duration SECOND";
    $stmt_lockout = $conn->prepare($check_lockout_query);
    $stmt_lockout->bind_param("s", $username);
    $stmt_lockout->execute();
    $result_lockout = $stmt_lockout->get_result();

    if ($result_lockout->num_rows >= $max_attempts) {
        // User is locked out
        $remaining_lockout_time = strtotime($result_lockout->fetch_assoc()['last_attempt_timestamp']) + $lockout_duration - time();

        // Check if the remaining time is not already set in the session
        if (!isset($_SESSION['lockout_end_time'])) {
            // Calculate the end time for the lockout period
            $_SESSION['lockout_end_time'] = time() + $lockout_duration;
        }

        // Calculate the dynamic remaining lockout time
        $dynamic_remaining_time = $_SESSION['lockout_end_time'] - time();

        // Update the session message with the dynamic countdown
        $_SESSION['message'] = "Too many failed login attempts. Please try again after $dynamic_remaining_time seconds.";
        $_SESSION['success'] = 'danger';
        header('location: ../login.php');
        exit;
    }

    // Proceed with login attempts
    $query = "SELECT * FROM tbl_users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                if ($row['verified'] == 1) {
                    // Account is verified, allow login
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['user_type'];
                    $_SESSION['avatar'] = $row['avatar'];
                    $_SESSION['user_email'] = $row['email'];

                    // Reset login attempts on successful login
                    $reset_attempts_query = "DELETE FROM login_attempts WHERE user_id = ?";
                    $stmt_reset_attempts = $conn->prepare($reset_attempts_query);
                    $stmt_reset_attempts->bind_param("i", $row['id']);
                    $stmt_reset_attempts->execute();

                    // Reset the lockout end time in the session
                    unset($_SESSION['lockout_end_time']);

                    $_SESSION['message'] = 'You have successfully logged in to Purrfect Clinic Management System!';
                    $_SESSION['success'] = 'success';
                    $login_successful = true;
                    header('location: ../dashboard.php');
                    break;
                } else {
                    // Account is not verified, prompt user to verify email
                    $_SESSION['message'] = 'Please verify your email before logging in.';
                    $_SESSION['success'] = 'info';
                    header('location: ../login.php');
                    exit;
                }
            }
        }
    }

    // Check the flag and update login attempts
    if (!$login_successful) {
        // Check if the user is not already locked out
        if ($result_lockout->num_rows < $max_attempts) {
            // Increment login attempts
            $update_attempts_query = "INSERT INTO login_attempts (user_id, attempts, last_attempt_timestamp) VALUES ((SELECT id FROM tbl_users WHERE username = ?), 1, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE attempts = attempts + 1";
            $stmt_update_attempts = $conn->prepare($update_attempts_query);
            $stmt_update_attempts->bind_param("s", $username);
            $stmt_update_attempts->execute();
        }

        // Check if the user is now locked out
        $check_lockout_query = "SELECT * FROM login_attempts WHERE user_id = (SELECT id FROM tbl_users WHERE username = ?) AND last_attempt_timestamp > NOW() - INTERVAL $lockout_duration SECOND";
        $stmt_lockout = $conn->prepare($check_lockout_query);
        $stmt_lockout->bind_param("s", $username);
        $stmt_lockout->execute();
        $result_lockout = $stmt_lockout->get_result();

        if ($result_lockout->num_rows >= $max_attempts) {
            // User is locked out after the current attempt
            if ($result_lockout->fetch_assoc()['attempts'] <= $max_attempts) {
                // Check if the remaining time is not already set in the session
                if (!isset($_SESSION['lockout_end_time'])) {
                    // Calculate the end time for the lockout period
                    $_SESSION['lockout_end_time'] = time() + $lockout_duration;
                }

                // Calculate the dynamic remaining lockout time
                $dynamic_remaining_time = $_SESSION['lockout_end_time'] - time();

                // Update the session message with the dynamic countdown
                $_SESSION['message'] = "Too many failed login attempts. Please try again after $dynamic_remaining_time seconds.";
                $_SESSION['success'] = 'danger';
                header('location: ../login.php');
                exit;
            }
        } else {
            // User still has attempts left
            $remaining_attempts = $max_attempts - $result_lockout->num_rows;
            $_SESSION['message'] = "Username or password is incorrect! $remaining_attempts attempts remaining.";
            $_SESSION['success'] = 'danger';
            header('location: ../login.php');
        }
    }
} else {
    $_SESSION['message'] = 'Username or password is empty!';
    $_SESSION['success'] = 'danger';
    header('location: ../login.php');
}

$conn->close();
?>