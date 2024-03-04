<?php
include('../server/server.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: ../medicines.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['medID'])) {
        // Retrieve data from the form
        $medID = $conn->real_escape_string($_POST['medID']);
        $itemName = $conn->real_escape_string($_POST['editItemName']);
        $itemType = $conn->real_escape_string($_POST['editItemType']);
        $itemCode = $conn->real_escape_string($_POST['editItemCode']);
        $quantity = $conn->real_escape_string($_POST['editQuantity']);
        $itemPrice = $conn->real_escape_string($_POST['editItemPrice']);
        $expirationDate = $conn->real_escape_string($_POST['editDate']);

        // Handle image upload (if any)
        if (!empty($_FILES['editImg']['name'])) {
            // File upload
            $editItemImageFile = $_FILES['editImg']['name'];
            $newEditImageName = date('dmYHis') . str_replace(" ", "", $editItemImageFile);
            $editTarget = "../assets/uploads/avatar/" . basename($newEditImageName);

            // Move uploaded file to the destination
            move_uploaded_file($_FILES['editImg']['tmp_name'], $editTarget);

            // Update the database field accordingly
            $updateImageQuery = "UPDATE tblmedi_mat SET `itemImage`='$newEditImageName' WHERE `medID`='$medID'";
            $result = $conn->query($updateImageQuery);

            // Check the result and handle accordingly
            if ($result === true) {
                // Image updated successfully
                // Additional logic if needed
            } else {
                // Image update failed
                // Additional logic if needed
            }
        }

        // Perform the update query
        $updateQuery = "UPDATE tblmedi_mat SET 
                        `itemName`='$itemName', 
                        `typeName`='$itemType', 
                        `itemCode`='$itemCode', 
                        `quantity`='$quantity', 
                        `itemPrice`='$itemPrice', 
                        `expirationDate`='$expirationDate' 
                        WHERE `medID`='$medID'";

        $result = $conn->query($updateQuery);

        if ($result === true) {
            $_SESSION['message'] = 'Medicine updated!';
            $_SESSION['success'] = 'success';
        } else {
            $_SESSION['message'] = 'Something went wrong!';
            $_SESSION['success'] = 'danger';
        }
    }
}

header("Location: ../medicines.php");
$conn->close();
?>
