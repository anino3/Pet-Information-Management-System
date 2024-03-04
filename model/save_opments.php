<?php
include('../server/server.php');

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: operation.php");
    }
}

if (!isset($_SESSION['username'])) {
    $referer = isset($_SERVER["HTTP_REFERER"]) ? filter_var($_SERVER["HTTP_REFERER"], FILTER_VALIDATE_URL) : null;
    if ($referer) {
        header("Location: " . $referer);
        exit();
    }
}

$user = $_SESSION['username'];

$amount = isset($_POST['amount']) ? filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT) : 0.0;
$date = date('Y-m-d H:i:s');  // Set $date to the current date and time
$details = isset($_POST['details']) ? htmlspecialchars($_POST['details'], ENT_QUOTES, 'UTF-8') : '';
$petOwner = isset($_POST['petOwner']) ? (int)$_POST['petOwner'] : 0; // Assuming you have a field for petOwner in the form
$operationID = isset($_POST['id']) ? (int)$_POST['id'] : 0; // Assuming you have a field for operation ID in the form

// Check if the paymentID in tbloperation already exists in tblpayments using Prepared Statements
$checkPaymentQuery = "SELECT id, amounts FROM tblpayments WHERE id = (SELECT paymentID FROM tbloperation WHERE id = ?)";
$stmtCheckPayment = $conn->prepare($checkPaymentQuery);
$stmtCheckPayment->bind_param("i", $operationID);
$stmtCheckPayment->execute();
$paymentResult = $stmtCheckPayment->get_result();

if ($paymentResult->num_rows > 0) {
    // Payment record already exists, update the amount
    $row = $paymentResult->fetch_assoc();
    $existingPaymentID = $row['id'];
    $existingAmount = $row['amounts'];

    // Update the existing payment record in tblpayments using Prepared Statements
    $updatePaymentQuery = "UPDATE tblpayments SET amounts = amounts + ? WHERE id = ?";
    $stmtUpdatePayment = $conn->prepare($updatePaymentQuery);
    $stmtUpdatePayment->bind_param("di", $amount, $existingPaymentID);
    if ($stmtUpdatePayment->execute() === FALSE) {
        $_SESSION['message'] = 'Failed to update payment record: ' . $conn->error;
        $_SESSION['success'] = 'danger';
        $stmtCheckPayment->close();
        $stmtUpdatePayment->close();
        $conn->close();
        header("Location: ../operation.php");
        exit();
    }
    $stmtUpdatePayment->close();
} else {
    // Payment record does not exist, insert a new payment record into tblpayments using Prepared Statements
    $insertPaymentQuery = "INSERT INTO tblpayments (`details`, `amounts`, `date`, `user`, `petOwner`) 
                            VALUES (?, ?, ?, ?, ?)";
    $stmtInsertPayment = $conn->prepare($insertPaymentQuery);
    $stmtInsertPayment->bind_param("sdssi", $details, $amount, $date, $user, $petOwner);
    if ($stmtInsertPayment->execute() === FALSE) {
        $_SESSION['message'] = 'Failed to insert payment record: ' . $conn->error;
        $_SESSION['success'] = 'danger';
        $stmtCheckPayment->close();
        $stmtInsertPayment->close();
        $conn->close();
        header("Location: ../operation.php");
        exit();
    }

    // Retrieve the last inserted ID from tblpayments
    $paymentID = $stmtInsertPayment->insert_id;
    $stmtInsertPayment->close();

    // Update tbloperation with the paymentID for the specific operation using Prepared Statements
    $updateOperationQuery = "UPDATE tbloperation SET paymentID = ? WHERE id = ? AND OwnerID = ?";
    $stmtUpdateOperation = $conn->prepare($updateOperationQuery);
    $stmtUpdateOperation->bind_param("iii", $paymentID, $operationID, $petOwner);
    if ($stmtUpdateOperation->execute() === FALSE) {
        $_SESSION['message'] = 'Failed to update payment: ' . $conn->error;
        $_SESSION['success'] = 'danger';
        $stmtCheckPayment->close();
        $stmtUpdateOperation->close();
        $conn->close();
        header("Location: ../operation.php");
        exit();
    }
    $stmtUpdateOperation->close();
}

$_SESSION['message'] = 'Payment has been saved!';
$_SESSION['success'] = 'success';

$stmtCheckPayment->close();
$conn->close();

// Redirect back to the previous page
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: ../operation.php");
    exit();
}
?>
