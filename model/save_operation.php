<?php
// Start the session
session_start();

include '../server/server.php';

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: operation.php");
    }
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set initial session values
    $_SESSION['message'] = '';
    $_SESSION['success'] = '';

    // Check if the necessary data is provided
    if (isset($_SESSION['username'], $_POST['petOwner'], $_POST['petName'], $_POST['operationType'], $_POST['date'], $_POST['time'], $_POST['status'], $_POST['details'])) {
        // Validation and Sanitization
        $petOwner = $conn->real_escape_string($_POST['petOwner']);
        $petName = $conn->real_escape_string($_POST['petName']);
        $operationType = $conn->real_escape_string($_POST['operationType']);
        $date = $conn->real_escape_string($_POST['date']);
        $time = $conn->real_escape_string($_POST['time']);
        $status = $conn->real_escape_string($_POST['status']);
        $details = $conn->real_escape_string($_POST['details']);

        // Fetch petOwnerID and OwnerName from tblowner using a prepared statement
        $ownerQuery = "SELECT OwnerID, OwnerName FROM tblowner WHERE OwnerName = ? AND OwnerID IN (SELECT OwnerID FROM tblpet WHERE pet_name = ?)";
        $stmtOwner = $conn->prepare($ownerQuery);
        $stmtOwner->bind_param("ss", $petOwner, $petName);
        $stmtOwner->execute();
        $ownerResult = $stmtOwner->get_result();

        if ($ownerResult && $ownerResult->num_rows > 0) {
            $ownerRow = $ownerResult->fetch_assoc();
            $petOwnerID = $ownerRow['OwnerID'];
            $petOwnerName = $ownerRow['OwnerName'];

            // Fetch petID and pet_name from tblpet using OwnerID as a condition with a prepared statement
            $petQuery = "SELECT id AS PetID, pet_name FROM tblpet WHERE pet_name = ? AND OwnerID = ?";
            $stmtPet = $conn->prepare($petQuery);
            $stmtPet->bind_param("si", $petName, $petOwnerID);
            $stmtPet->execute();
            $petResult = $stmtPet->get_result();

            if ($petResult && $petResult->num_rows > 0) {
                $petRow = $petResult->fetch_assoc();
                $petNameID = $petRow['PetID'];
                $petName = $petRow['pet_name'];

                // Check if treatment plans are provided
                if (isset($_POST['treatmentPlans'])) {
                    $decodedTreatmentPlans = json_decode($_POST['treatmentPlans'], true);

                    if ($decodedTreatmentPlans === null && json_last_error() !== JSON_ERROR_NONE) {
                        $_SESSION['message'] = 'Select treatment item!';
                        $_SESSION['success'] = 'danger';
                    } elseif (is_array($decodedTreatmentPlans) && !empty($decodedTreatmentPlans)) {
                        // Insert into tbloperation using prepared statement
                        $insert = "INSERT INTO tbloperation (`OwnerID`, `petID`, `operationType`, `date`, `time`, `status`, `details`) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($insert);
                        $stmtInsert->bind_param("iisssss", $petOwnerID, $petNameID, $operationType, $date, $time, $status, $details);
                        $stmtInsert->execute();

                        if ($stmtInsert->affected_rows > 0) {
                            $operationID = $stmtInsert->insert_id; // Get the ID of the inserted operation

                            // Process treatment plans using prepared statement
                            $treatmentPlans = implode(', ', array_map(array($conn, 'real_escape_string'), $decodedTreatmentPlans));
                            $updateTreatmentQuery = "UPDATE tbloperation SET `treatment_used` = ? WHERE `id` = ?";
                            $stmtUpdateTreatment = $conn->prepare($updateTreatmentQuery);
                            $stmtUpdateTreatment->bind_param("si", $treatmentPlans, $operationID);
                            $stmtUpdateTreatment->execute();

                            // Calculate total cost using prepared statement
                            $totalCost = calculateTotalCost($decodedTreatmentPlans, $conn);
                            $updateCostQuery = "UPDATE tbloperation SET `operationCost` = ? WHERE `id` = ?";
                            $stmtUpdateCost = $conn->prepare($updateCostQuery);
                            $stmtUpdateCost->bind_param("di", $totalCost, $operationID);
                            $stmtUpdateCost->execute();

                            // If both queries succeeded, set success to true
                            if (empty($_SESSION['message'])) {
                                $_SESSION['message'] = 'Operation Record added!';
                                $_SESSION['success'] = 'success';
                            }
                        } else {
                            $_SESSION['message'] = 'Error inserting into tbloperation: ' . $stmtInsert->error;
                        }

                        $stmtInsert->close();
                        $stmtUpdateTreatment->close();
                        $stmtUpdateCost->close();
                    } else {
                        $_SESSION['message'] = 'Please select valid treatment plans!';
                        $_SESSION['success'] = 'danger';
                    }
                } else {
                    $_SESSION['message'] = 'Treatment plans are not set in the request!';
                }
            } else {
                $_SESSION['message'] = 'No matching pet found for PetName: ' . $petName . ' and OwnerID: ' . $petOwnerID;
            }

            $stmtPet->close();
        } else {
            $_SESSION['message'] = 'No matching pet owner found for OwnerName: ' . $petOwner;
        }

        $stmtOwner->close();
    } else {
        $_SESSION['message'] = 'Please fill up the form completely!';
    }
}

// Function to calculate total cost

function calculateTotalCost($selectedMachines, $conn) {
    $escapedMachines = array_map(array($conn, 'real_escape_string'), $selectedMachines);
    $inClause = implode("','", $escapedMachines);
    $query = "SELECT SUM(`machinePrice`) AS `totalCost` FROM tbltreatmentplan WHERE `machineName` IN ('$inClause')";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['totalCost'];
    } else {
        return 0;
    }
}

$conn->close();

// Redirect back to the previous page
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: ../operation.php");
    exit();
}
?>
