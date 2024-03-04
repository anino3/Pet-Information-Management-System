<?php
// fetch_medi_mat.php

include('../server/server.php');

$query = "SELECT * FROM tblmedi_mat";
$result = $conn->query($query);

$mediMatItems = array();
while ($row = $result->fetch_assoc()) {
    $mediMatItems[] = $row;
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($mediMatItems);
$conn->close();
?>
