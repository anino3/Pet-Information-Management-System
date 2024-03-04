<?php
session_start();
include('../server/server.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $recordID = filter_input(INPUT_POST, 'recordID', FILTER_SANITIZE_STRING);
    $paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_STRING);

    if (!empty($recordID)) {
        // Use prepared statement to prevent SQL injection
        $updatePaidStatus = "UPDATE tblrecordservice SET paid = ? WHERE recordID = ?";
        $stmt = $conn->prepare($updatePaidStatus);
        $stmt->bind_param("ss", $paid, $recordID);
        $updateResult = $stmt->execute();

        if ($updateResult === true) {
            echo 'Paid status updated successfully.';
        } else {
            echo 'Failed to update paid status.';
        }
    } else {
        echo 'RecordID is missing.';
    }

    $conn->close();
} else {
    echo 'Invalid request.';
}
?>
