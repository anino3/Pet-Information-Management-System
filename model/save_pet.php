<?php
session_start();
include '../server/server.php';

if (!isset($_SESSION['username'])) {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
}

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: petowner.php");
    }
}

$pnotes = $conn->real_escape_string($_POST['pnotes']);
$pname = $conn->real_escape_string($_POST['pname']);
$oname = $conn->real_escape_string($_POST['oname']);
$bname = $conn->real_escape_string($_POST['bname']);
$ocity = $conn->real_escape_string($_POST['ocity']);
$bdate = $conn->real_escape_string($_POST['bdate']);
$age = $conn->real_escape_string($_POST['age']);
$zcode = $conn->real_escape_string($_POST['zcode']);
$pgender = $conn->real_escape_string($_POST['pgender']);
$email = $conn->real_escape_string($_POST['email']);
$number = $conn->real_escape_string($_POST['onumber']);
$ptype = $conn->real_escape_string($_POST['ptype']);
$oplace = $conn->real_escape_string($_POST['oplace']);
$ownerID = $conn->real_escape_string($_POST['ownerID']);
$profile2 = $_FILES['img']['name'];

// Change profile2 name
$newName = date('dmYHis') . str_replace(" ", "", $profile2);

// Image file directory
$target = "../assets/uploads/resident_profile/" . basename($newName);

// Check if the owner already exists
$checkOwner = "SELECT OwnerID FROM tblowner WHERE OwnerID='$ownerID'";
$resultOwner = $conn->query($checkOwner);

if ($resultOwner->num_rows === 0) {
    // Insert new owner
    $query1 = "INSERT INTO tblowner (`OwnerName`,`OwnerAddress`, `OwnerCity`, `OwnerZip`, `OwnerMobileNo`, `OwnerEmail`) 
               VALUES ('$oname','$oplace','$ocity','$zcode','$number','$email')";

    if ($conn->query($query1) === true) {
        $ownerID = $conn->insert_id; // Retrieve the auto-generated OwnerID
    }
}

// Check if the pet already exists
$checkPet = "SELECT id, picture FROM tblpet WHERE pet_name=? AND OwnerID=?";
$stmtCheckPet = $conn->prepare($checkPet);
$stmtCheckPet->bind_param("si", $pname, $ownerID);
$stmtCheckPet->execute();
$resultPet = $stmtCheckPet->get_result();

if ($resultPet->num_rows === 0) {
    // Insert new pet
    $query2 = "INSERT INTO tblpet (`pet_name`,`pet_type`, `pet_breed`, birthdate, `age`, `gender`, `pet_notes`,`picture`, `OwnerID`) 
               VALUES (?,?,?,?,?,?,?,?,?)";

    $stmtInsert = $conn->prepare($query2);
    $stmtInsert->bind_param("ssssssssi", $pname, $ptype, $bname, $bdate, $age, $pgender, $pnotes, $newName, $ownerID);

    if ($stmtInsert->execute()) {
        $_SESSION['message'] = 'Pet Information has been saved!';
        $_SESSION['success'] = 'success';

        if (!empty($profile2) && move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
            // Image uploaded successfully
        }
    }
} else {
    // Pet already exists, update the information
    $rowPet = $resultPet->fetch_assoc();
    $petID = $rowPet['id'];
    $oldImage = $rowPet['picture'];

    // Update existing pet information
    $queryUpdatePet = "UPDATE tblpet 
                       SET pet_type=?, pet_breed=?, birthdate=?, age=?,
                           gender=?, pet_notes=?, OwnerID=?";

    // Check if a new image was provided
    if (!empty($profile2)) {
        // Remove old image if it exists
        if (!empty($oldImage)) {
            unlink("../assets/uploads/resident_profile/$oldImage");
        }

        $queryUpdatePet .= ", picture=?";
    }

    $queryUpdatePet .= " WHERE id=?";
    $stmtUpdatePet = $conn->prepare($queryUpdatePet);

    // Bind parameters
    if (!empty($profile2)) {
        $stmtUpdatePet->bind_param("ssssssssi", $ptype, $bname, $bdate, $age, $pgender, $pnotes, $ownerID, $newName, $petID);
    } else {
        $stmtUpdatePet->bind_param("ssssssss", $ptype, $bname, $bdate, $age, $pgender, $pnotes, $ownerID, $petID);
    }

    if ($stmtUpdatePet->execute()) {
        $_SESSION['message'] = 'Pet Information has been updated!';
        $_SESSION['success'] = 'success';

        if (!empty($profile2) && move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
            // Image uploaded successfully
        }
    }
}

header("Location: ../petowner.php");
$conn->close();
?>
