<?php
include '../server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: petowner.php");
    }
}


// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume the initial response is an error
    $response = array('success' => false, 'message' => 'Invalid request');

    // Check if the necessary data is provided
    if (isset($_SESSION['username'], $_POST['id'], $_POST['selectedAction'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $selectedAction = $conn->real_escape_string($_POST['selectedAction']);

        // Check if finishStatus or cancelStatus is set, and assign the appropriate variables
        if ($selectedAction === 'finish' || $selectedAction === 'cancel') {
            // Handle both finish and cancel operations
            $finishDate = $conn->real_escape_string($_POST['finishOperationDate']);
            $finishTime = $conn->real_escape_string($_POST['finishOperationTime']);

            // Set default values for cancel operation
            $status = 'Cancelled';
            $finishDetails = $conn->real_escape_string($_POST['cancelOperationDetails']);

            if ($selectedAction === 'finish') {
                // If it's a finish operation, update the values accordingly
                $status = 'Finished';
                $finishDetails = $conn->real_escape_string($_POST['finishOperationDetails']);
            }

            // Fetch the current operationCost value
            $currentOperationCostQuery = "SELECT `operationCost` FROM tbloperation WHERE id=?";
            $currentOperationCostStmt = $conn->prepare($currentOperationCostQuery);
            $currentOperationCostStmt->bind_param("i", $id);
            $currentOperationCostStmt->execute();
            $currentOperationCostStmt->bind_result($currentOperationCost);
            $currentOperationCostStmt->fetch();
            $currentOperationCostStmt->close();

            // Perform the database update for 'finish' or 'cancel' operation
            $query = "UPDATE tbloperation SET `finishDate`=?, `finishTime`=?, `status`=?, `finishDetails`=?, `operationCost`=? WHERE id=?";
            $stmt = $conn->prepare($query);

            // Calculate the new total cost by adding the current and calculated costs
            $newOperationCost = $currentOperationCost + calculateGrandTotal($filteredItems);

            $stmt->bind_param("ssssid", $finishDate, $finishTime, $status, $finishDetails, $newOperationCost, $id);
            $stmt->execute();

            // Check for successful update
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = 'Operation Updated!';
                $_SESSION['success'] = 'success';
            } else {
                $_SESSION['message'] = 'Something went wrong!';
                $_SESSION['success'] = 'danger';
            }

            $stmt->close();
        } elseif ($selectedAction === 'proceed') {
            // Handle the proceed operation
            $status = 'On-going';

            // Fetch the current operationCost value
            $currentOperationCostQuery = "SELECT `operationCost` FROM tbloperation WHERE id=?";
            $currentOperationCostStmt = $conn->prepare($currentOperationCostQuery);
            $currentOperationCostStmt->bind_param("i", $id);
            $currentOperationCostStmt->execute();
            $currentOperationCostStmt->bind_result($currentOperationCost);
            $currentOperationCostStmt->fetch();
            $currentOperationCostStmt->close();

            // Process selected items
            $selectedItems = json_decode($_POST['selectedItems'], true);
            $filteredItems = filterItemsWithNonZeroQuantity($selectedItems);
            $medimatUsed = implode(", ", array_map(function ($itemName, $quantity) {
                return "$itemName:$quantity";
            }, array_keys($filteredItems), $filteredItems));

            // Calculate the new total cost by adding the current and calculated costs
            $newOperationCost = $currentOperationCost + calculateGrandTotal($filteredItems);

            // Update the medimat_used and operationCost columns in the database for 'proceed' operation
            $updateQuery = "UPDATE tbloperation SET `medimat_used`=?, `operationCost`=?, `status`=? WHERE id=?";
            $updateStmt = $conn->prepare($updateQuery);

            $updateStmt->bind_param("sssi", $medimatUsed, $newOperationCost, $status, $id);
            $updateStmt->execute();

            // Deduct quantities from the corresponding items in tblmedi_mat
            foreach ($filteredItems as $itemName => $quantity) {
                deductQuantityFromItem($itemName, $quantity);
            }

            // Check for successful update
            if ($updateStmt->affected_rows > 0) {
                $_SESSION['message'] = 'Operation Updated!';
                $_SESSION['success'] = 'success';
            } else {
                $_SESSION['message'] = 'Please fill up the form completely!';
                $_SESSION['success'] = 'danger';
            }

            $updateStmt->close();
        }
    }
}

// Output the JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Function to calculate the grand total
function calculateGrandTotal($selectedItems)
{
    $grandTotal = 0;

    // Loop through selected items and calculate total cost
    foreach ($selectedItems as $itemName => $quantity) {
        $itemPrice = getItemPriceByName($itemName); // Implement this function to get the price by item name
        $totalCost = $quantity * $itemPrice;
        $grandTotal += $totalCost;
    }

    return $grandTotal;
}

// Function to filter items with a quantity greater than 0
function filterItemsWithNonZeroQuantity($items)
{
    return array_filter($items, function ($quantity) {
        return $quantity > 0;
    });
}

// Function to get item price by name (you need to implement this function based on your database structure)
function getItemPriceByName($itemName)
{
    global $conn; // Assuming $conn is your database connection

    // Escape the item name to prevent SQL injection
    $escapedItemName = $conn->real_escape_string($itemName);

    // Query to retrieve the item price by name
    $query = "SELECT itemPrice FROM tblmedi_mat WHERE itemName = '$escapedItemName'";

    // Execute the query
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Fetch the item price from the result set
        $row = $result->fetch_assoc();
        $itemPrice = $row['itemPrice'];

        // Free the result set
        $result->free_result();

        return $itemPrice;
    } else {
        // Handle the case when the item is not found
        return 0.0; // Placeholder value; you can customize this as needed
    }
}

// Function to deduct quantity from the corresponding item in tblmedi_mat
function deductQuantityFromItem($itemName, $quantity)
{
    global $conn; // Assuming $conn is your database connection

    // Escape the item name to prevent SQL injection
    $escapedItemName = $conn->real_escape_string($itemName);

    // Deduct the quantity from the item in tblmedi_mat
    $updateQuery = "UPDATE tblmedi_mat SET quantity = quantity - ? WHERE itemName = ?";
    $updateStmt = $conn->prepare($updateQuery);

    $updateStmt->bind_param("is", $quantity, $escapedItemName);
    $updateStmt->execute();

    $updateStmt->close();
}
?>