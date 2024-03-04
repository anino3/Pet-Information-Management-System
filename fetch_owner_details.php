<?php
// fetch_owner_details.php

// Assuming you have a database connection established

// Get the selected ownerName from the request
$ownerName = $_GET['ownerName'];

// Fetch owner details from the database based on the selected ownerName
// Adjust this code based on your database structure and query method
$sql = "SELECT place, city, zip, mobileNo, email FROM owners WHERE ownerName = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $ownerName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ownerDetails = array(
        'place' => $row['place'],
        'city' => $row['city'],
        'zip' => $row['zip'],
        'mobileNo' => $row['mobileNo'],
        'email' => $row['email']
    );

    // Return the owner details as a JSON response
    echo json_encode($ownerDetails);
} else {
    // Handle the case when no owner details are found
    echo "No owner details found.";
}

$stmt->close();
$connection->close();
?>