<?php 
	include('../server/server.php');

    // CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: medicines.php");
    }
}

    if(!isset($_SESSION['username'])){
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }

	$itemName    = $conn->real_escape_string($_POST['itemName']);
	$itemType    = $conn->real_escape_string($_POST['itemType']);
	$itemCode    = $conn->real_escape_string($_POST['itemCode']);
	$quantity    = $conn->real_escape_string($_POST['quantity']);
	$itemPrice   = $conn->real_escape_string($_POST['itemPrice']);
	$expirationDate = $conn->real_escape_string($_POST['date']);

    // Base64 encoded image data
    $itemImageBase64 = $conn->real_escape_string($_POST['profileimg']);

    // File upload
    $itemImageFile = $_FILES['img']['name'];
    $newImageName = date('dmYHis').str_replace(" ", "", $itemImageFile);
    $target = "../assets/uploads/avatar/".basename($newImageName);

    if (!empty($itemName) && !empty($itemType) && !empty($itemCode) && !empty($quantity) && !empty($itemPrice) && !empty($expirationDate)) {

        // Check if item with the same code already exists
        $checkQuery = "SELECT * FROM tblmedi_mat WHERE itemCode='$itemCode'";
        $checkResult = $conn->query($checkQuery);

        if($checkResult->num_rows){
            $_SESSION['message'] = 'Medicine with the same code already exists!';
            $_SESSION['success'] = 'danger';
        } else {
            if (!empty($itemImageBase64) && !empty($itemImageFile)) {
                // Insert with base64 image
                $insertQuery = "INSERT INTO tblmedi_mat (`itemName`, `typeName`, `itemCode`, `quantity`, `expirationDate`, `itemPrice`, `itemImage`) 
                                VALUES ('$itemName', '$itemType', '$itemCode', '$quantity', '$expirationDate', '$itemPrice', '$itemImageBase64')";
                $result = $conn->query($insertQuery);

                if ($result === true) {
                    $_SESSION['message'] = 'Medicine added!';
                    $_SESSION['success'] = 'success';
                } else {
                    $_SESSION['message'] = 'Something went wrong!';
                    $_SESSION['success'] = 'danger';
                }
            } else if (!empty($itemImageBase64) && empty($itemImageFile)) {
                // Insert with base64 image
                $insertQuery = "INSERT INTO tblmedi_mat (`itemName`, `typeName`, `itemCode`, `quantity`, `expirationDate`, `itemPrice`, `itemImage`) 
                                VALUES ('$itemName', '$itemType', '$itemCode', '$quantity', '$expirationDate', '$itemPrice', '$itemImageBase64')";
                $result = $conn->query($insertQuery);

                if ($result === true) {
                    $_SESSION['message'] = 'Medicine added!';
                    $_SESSION['success'] = 'success';
                } else {
                    $_SESSION['message'] = 'Something went wrong!';
                    $_SESSION['success'] = 'danger';
                }
            } else if (empty($itemImageBase64) && !empty($itemImageFile)) {
                // Insert with file upload
                $insertQuery = "INSERT INTO tblmedi_mat (`itemName`, `typeName`, `itemCode`, `quantity`, `expirationDate`, `itemPrice`, `itemImage`) 
                                VALUES ('$itemName', '$itemType', '$itemCode', '$quantity', '$expirationDate', '$itemPrice', '$newImageName')";
                $result = $conn->query($insertQuery);

                move_uploaded_file($_FILES['img']['tmp_name'], $target);

                if ($result === true) {
                    $_SESSION['message'] = 'Medicine added!';
                    $_SESSION['success'] = 'success';
                } else {
                    $_SESSION['message'] = 'Something went wrong!';
                    $_SESSION['success'] = 'danger';
                }
            } else {
                // Insert without image
                $insertQuery = "INSERT INTO tblmedi_mat (`itemName`, `typeName`, `itemCode`, `quantity`, `expirationDate`, `itemPrice`) 
                                VALUES ('$itemName', '$itemType', '$itemCode', '$quantity', '$expirationDate', '$itemPrice')";
                $result = $conn->query($insertQuery);

                if ($result === true) {
                    $_SESSION['message'] = 'Medicine added!';
                    $_SESSION['success'] = 'success';
                } else {
                    $_SESSION['message'] = 'Something went wrong!';
                    $_SESSION['success'] = 'danger';
                }
            }
        }
        
    } else {
        $_SESSION['message'] = 'Please fill up the form completely!';
        $_SESSION['success'] = 'danger';
    }

    header("Location: ../medicines.php");

	$conn->close();
?>
