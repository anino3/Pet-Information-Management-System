<?php

// Include your database connection
include 'server/server.php';

if (isset($_POST['ownerID'])) {
    $ownerID = $_POST['ownerID'];

    $query = "SELECT id, pet_name FROM tblpet WHERE OwnerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ownerID);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = '<option disabled selected>Select Pet Name</option>';
    while ($row = $result->fetch_assoc()) {
        $options .= '<option data-petid="' . $row['id'] . '">' . $row['pet_name'] . '</option>';
    }

    echo $options;

    $stmt->close();
} else {
    error_log('Invalid request - No ownerID received');
    echo 'Invalid request';
}

$conn->close();
?>