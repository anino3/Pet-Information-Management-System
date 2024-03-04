<?php

// Include your database connection
include 'server/server.php';

if (isset($_POST['operationID'])) {
    $operationID = $_POST['operationID'];

    // Modify the query to fetch id, operationCost, operationType, OwnerName, and paidAmount
    $query = "SELECT op.id, op.operationCost, op.operationType, op.OwnerID, ow.OwnerName,
              (SELECT SUM(amounts) FROM tblpayments WHERE paymentID = op.paymentID) AS paidAmount
              FROM tbloperation op
              LEFT JOIN tblowner ow ON op.OwnerID = ow.OwnerID
              WHERE op.id = ?";
    $stmt = $conn->prepare($query);

    // Bind the operationID parameter
    $stmt->bind_param("i", $operationID);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the operation details from the result set
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $operationCost = $row['operationCost'];
        $details = $row['operationType'];
        $ownerName = $row['OwnerName'];
        $petOwner = $row['OwnerID'];
        $paidAmount = $row['paidAmount'];

        // Return the operation details and paid amount as a JSON response
        $response = array(
            'id' => $id,
            'operationCost' => $operationCost,
            'operationType' => $details,
            'ownerName' => $ownerName,
            'petOwner' => $petOwner,
            'paidAmount' => $paidAmount
        );
        echo json_encode($response);
    } else {
        // Return a JSON response with an error message
        $errorResponse = array('error' => 'No results found for operationID = ' . $operationID);
        echo json_encode($errorResponse);
    }

    $stmt->close();
} else {
    // Return a JSON response with an error message
    $errorResponse = array('error' => 'Invalid request - No operationID received');
    echo json_encode($errorResponse);
}

$conn->close();
?>

