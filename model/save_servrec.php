<?php
include('../server/server.php');

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


// CSRF protection
// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: pet_services.php");
    }
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    exit(); // Ensure that the script stops execution if the user is not logged in
}

// Escape and retrieve form data
$petOwnerID = $conn->real_escape_string($_POST['petOwnerID']);
$petNameID = $conn->real_escape_string($_POST['petNameID']);
$serviceTypes = isset($_POST['serviceType']) ? $_POST['serviceType'] : array();
$date = $conn->real_escape_string($_POST['date']);
$paid = $conn->real_escape_string($_POST['paid']);

// Fetch service costs from the database
$totalCost = 0;

if (!empty($serviceTypes)) {
    // Trim and sanitize service types
    $trimmedServiceTypes = array_map('trim', $serviceTypes);

    // Implode without wrapping each service type in single quotes
    $serviceTypeString = implode(", ", $trimmedServiceTypes);

    // Prepare the statement
    $serviceCostsQuery = $conn->prepare("SELECT servicePrice FROM tblservices WHERE serviceName IN (" . str_repeat('?,', count($trimmedServiceTypes) - 1) . '?)');

    // Bind parameters
    $bindTypes = str_repeat('s', count($trimmedServiceTypes));
    $serviceCostsQuery->bind_param($bindTypes, ...$trimmedServiceTypes);

    // Execute the statement
    $serviceCostsQuery->execute();

    // Get the result
    $serviceCostsResult = $serviceCostsQuery->get_result();

    // Check for SQL query execution error
    if (!$serviceCostsResult) {
        die('Error fetching service costs: ' . $conn->error);
    }

    while ($row = $serviceCostsResult->fetch_assoc()) {
        $totalCost += $row['servicePrice'];
    }

    // Multiply the total cost by the number of selected pets
    $totalCost *= 1;

    // Close the prepared statement
    $serviceCostsQuery->close();
}

// Check if required fields are not empty
if (!empty($serviceTypes) && !empty($date) && !empty($petOwnerID) && !empty($petNameID) && !empty($paid) && $totalCost > 0) {

    // Use prepared statement to prevent SQL injection
    $insert = $conn->prepare("INSERT INTO tblrecordservice (`serviceTypes`, `date`, `OwnerID`, `petID`, `paid`, `totalCost`) 
                            VALUES (?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $insert->bind_param("ssssdd", $serviceTypeString, $date, $petOwnerID, $petNameID, $paid, $totalCost);

    // Execute the prepared statement
    if ($insert->execute()) {
        $_SESSION['message'] = 'Service Record added!';
        $_SESSION['success'] = 'success';
    } else {
        die('Error executing prepared statement: ' . $insert->error);
    }

    // Close the prepared statement
    $insert->close();
} else {
    $_SESSION['message'] = 'Please fill up the form completely!';
    $_SESSION['success'] = 'danger';
}

// Redirect back to the pet_services.php page
header("Location: ../pet_services.php");

// Close the database connection
$conn->close();
?>
