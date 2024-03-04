<?php
$database   = 'petshop';
$username   = 'root';
$host       = 'localhost';
$password   = '';

ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | E_DEPRECATED | E_STRICT);
// error_reporting(0);

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error());
}

if (!isset($_SESSION)) {
    session_start();
}



// Check if the user is attempting to log in
if (isset($_POST['login'])) {
    $userEmail = $_POST['email']; // Assuming you have an input field named 'email'
    $userPassword = $_POST['password']; // Assuming you have an input field named 'password'

    // Your login validation logic here
    $loginSuccessful = validateLogin($userEmail, $userPassword);

    if ($loginSuccessful) {
        $_SESSION['user_email'] = $userEmail;
        header("Location: ../dashboard.php");
    } else {
        // Handle login failure
        $_SESSION['login_message'] = 'Invalid username or password';
        header("Location: ../login.php");
    }
}

if (!isset($_SESSION['user_email'])) {
    // Set a message to be displayed on the login page
    $_SESSION['login_message'] = 'You need to log in to access this page.';
    
    // Redirect to the login page
    header("Location: ../vetims/login.php");
    exit();
}