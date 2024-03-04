<?php
include 'server/server.php';

if (isset($_POST['petOwner']) && isset($_POST['id'])) {
    $petOwner = $_POST['petOwner'];
    $id = $_POST['id'];

    $query = "SELECT amounts FROM tblpayments 
              WHERE petOwner = ? AND id IN (SELECT paymentID FROM tbloperation WHERE id = ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $petOwner, $id);

    $stmt->execute();
    $result = $stmt->get_result();

    $amounts = array();
    while ($row = $result->fetch_assoc()) {
        $amounts[] = $row;
    }

    echo json_encode($amounts);

    $stmt->close();
} else {
    $errorResponse = array('error' => 'Invalid request - Missing petOwner or id');
    echo json_encode($errorResponse);
}

$conn->close();
?>
