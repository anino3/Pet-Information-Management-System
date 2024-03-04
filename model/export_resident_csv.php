<?php

// Include the server file for database connection
require("../server/server.php");

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // You can redirect to a login page or handle the unauthorized access as needed
    exit("Unauthorized Access");
}

// Get Users
$query = "SELECT id,pet_name,pet_type,pet_breed,birthdate,age,gender,pet_notes,picture,OwnerID FROM tblpet";
if (!$result = $conn->query($query)) {
    exit($conn->error);
}

$users = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=pet.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, array('Pet ID', 'Pet Name', 'Pet Type', 'Pet Breed', 'Birthdate', 'Age', 'Gender', 'Pet Notes', 'Picture', 'OwnerID'));

// Write data rows to CSV
if (count($users) > 0) {
    foreach ($users as $row) {
        fputcsv($output, $row);
    }
}

// Close the output stream
fclose($output);

// Close the database connection
$conn->close();
?>
