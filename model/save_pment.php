<?php
include('../server/server.php');

// Session Fixation Protection
session_regenerate_id(true);

if (!isset($_SESSION['username'])) {
    $referer = isset($_SERVER["HTTP_REFERER"]) ? filter_var($_SERVER["HTTP_REFERER"], FILTER_VALIDATE_URL) : null;
    if ($referer) {
        header("Location: " . $referer);
        exit();
    }
}

$user = $_SESSION['username'];

// Validation and Sanitization
$OwnerID = isset($_POST['OwnerID']) ? (int)$_POST['OwnerID'] : 0;
$amount  = isset($_POST['amount']) ? filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT) : 0.0;
$date    = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
$details = isset($_POST['details']) ? htmlspecialchars($_POST['details'], ENT_QUOTES, 'UTF-8') : '';
$paid    = isset($_POST['paid']) && $_POST['paid'] == 'true' ? 'true' : 'false';
$recordID = isset($_POST['recordID']) ? (int)$_POST['recordID'] : 0;

// Retrieve OwnerID based on OwnerName
$ownerIDQuery = "SELECT OwnerID FROM tblowner WHERE OwnerID = ?";
$stmtOwnerID = $conn->prepare($ownerIDQuery);
$stmtOwnerID->bind_param("i", $OwnerID);
$stmtOwnerID->execute();
$ownerIDResult = $stmtOwnerID->get_result();

if ($ownerIDResult->num_rows > 0) {
    $ownerIDRow = $ownerIDResult->fetch_assoc();
    $ownerID = $ownerIDRow['OwnerID'];

    // Insert payment record using Prepared Statements
    $insertPaymentQuery = "INSERT INTO tblpayments (`details`, `amounts`, `date`, `user`, `petOwner`) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertPayment = $conn->prepare($insertPaymentQuery);
    $stmtInsertPayment->bind_param("sdsss", $details, $amount, $date, $user, $ownerID);
    $stmtInsertPayment->execute();

    if ($stmtInsertPayment->affected_rows > 0) {
        $_SESSION['message'] = 'Payment has been saved!';
        $_SESSION['success'] = 'success';

        // Update paid status in tblrecordservice using Prepared Statements
        $updatePaidStatusQuery = "UPDATE tblrecordservice SET paid = ? WHERE recordID = ?";
        $stmtUpdatePaidStatus = $conn->prepare($updatePaidStatusQuery);
        $stmtUpdatePaidStatus->bind_param("si", $paid, $recordID);
        $stmtUpdatePaidStatus->execute();

        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"] . '&closeModal');
            exit();
        }
    } else {
        $_SESSION['message'] = 'Failed to insert payment record!';
        $_SESSION['success'] = 'danger';

        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit();
        }
    }
} else {
    $_SESSION['message'] = 'Failed to retrieve OwnerID!';
    $_SESSION['success'] = 'danger';

    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
}

$stmtOwnerID->close();
$stmtInsertPayment->close();
$stmtUpdatePaidStatus->close();
$conn->close();
?>
