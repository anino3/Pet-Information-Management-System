<?php
session_start();
include('../server/server.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['username'])) {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
}

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: ../users.php");
    }
}

$user = $conn->real_escape_string($_POST['username']);

// Check if password and password confirmation match and meet criteria
$pass = $_POST['pass'];
$pass_confirm = $_POST['pass_confirm'];

if ($pass !== $pass_confirm) {
    $_SESSION['message'] = 'Password and password confirmation do not match.';
    $_SESSION['success'] = 'danger';
    header("Location: ../users.php");
    exit();
}

if (strlen($pass) < 8 || !preg_match('/\d/', $pass)) {
    $_SESSION['message'] = 'Password must be at least 8 characters long and include a number.';
    $_SESSION['success'] = 'danger';
    header("Location: ../users.php");
    exit();
}

// Hash the password
$pass = password_hash($conn->real_escape_string($pass), PASSWORD_DEFAULT);
$usertype = $conn->real_escape_string($_POST['user_type']);
$profile2 = $_FILES['img']['name'];
$newName = date('dmYHis') . str_replace(" ", "", $profile2);

// image file directory
$target = "../assets/uploads/avatar/" . basename($newName);

$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';

// Validate the sanitized email
if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    if (!empty($user) && !empty($pass) && !empty($usertype)) {

        $code = md5(rand());  // Generate a verification code

        // Check if the email already exists
        $emailCheckQuery = "SELECT * FROM tbl_users WHERE email='$email'";
        $emailCheckResult = $conn->query($emailCheckQuery);

        if ($emailCheckResult->num_rows) {
            $_SESSION['message'] = 'Email address already exists!';
            $_SESSION['success'] = 'danger';
            header("Location: ../users.php");
            exit();
        }

        // Continue with the registration process
        $query = "SELECT * FROM tbl_users WHERE username='$user'";
        $res = $conn->query($query);

        if ($res->num_rows) {
            $_SESSION['message'] = 'Please enter a unique username!';
            $_SESSION['success'] = 'danger';
        } else {
            move_uploaded_file($_FILES['img']['tmp_name'], $target);

            // Include PHPMailer autoloader
            require '../vendor/autoload.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            try {
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'franciabritz17@gmail.com';
                $mail->Password   = 'jnjozetrhvllvbhl';
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('franciabritz17@gmail.com');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body    =  ' Here is the verification link: <b><a href="http://localhost/vetims/verification.php?verification_code='.$code.' ">http://localhost/vetims/verification.php?verification_code='.$code.'</a></b>';

                $mail->send();
                echo 'Message has been sent';

                // Insert user data into the database
                $insert = $conn->prepare("INSERT INTO tbl_users (`username`, `password`, `user_type`, `avatar`, `email`, `verification_code`, `verified`) VALUES (?, ?, ?, ?, ?, ?, 0)");
                $insert->bind_param("ssssss", $user, $pass, $usertype, $newName, $email, $code);
                $result = $insert->execute();

                if ($result === true) {
                    $_SESSION['message'] = 'User added! Please check your email for verification.';
                    $_SESSION['success'] = 'success';
                } else {
                    $_SESSION['message'] = 'Something went wrong!';
                    $_SESSION['success'] = 'danger';
                }
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                $_SESSION['success'] = 'danger';
            }
        }
    } else {
        $_SESSION['message'] = 'Please fill up the form completely!';
        $_SESSION['success'] = 'danger';
    }
} else {
    // Invalid email format
    $_SESSION['message'] = 'Please enter a valid email address.';
    $_SESSION['success'] = 'danger';
}

header("Location: ../users.php");
$conn->close();
?>
