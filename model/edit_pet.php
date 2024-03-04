<?php 
	include '../server/server.php';

	if(!isset($_SESSION['username'])){
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

	$ownerid = $conn->real_escape_string($_POST['ownerid']);
    $id = $conn->real_escape_string($_POST['id']);
	$pnotes = $conn->real_escape_string($_POST['pnotes']);
	$pname = $conn->real_escape_string($_POST['pname']);
	$oname = $conn->real_escape_string($_POST['oname']);
    $bname = $conn->real_escape_string($_POST['bname']);
	$ocity = $conn->real_escape_string($_POST['ocity']);
	$bdate = $conn->real_escape_string($_POST['bdate']);
    $age = $conn->real_escape_string($_POST['age']);
    $zcode = $conn->real_escape_string($_POST['zcode']);
	$ptype = $conn->real_escape_string($_POST['ptype']);

   
	
    $gender = $conn->real_escape_string($_POST['pgender']);
    $email = $conn->real_escape_string($_POST['email']);
	$number = $conn->real_escape_string($_POST['onumber']);
	
    $oplace = $conn->real_escape_string($_POST['oplace']);
	$profile2 = $_FILES['img']['name'];

	// Change the name of profile2
	$newName = date('dmYHis') . str_replace(" ", "", $profile2);

	// Image file directory
	$target = "../assets/uploads/resident_profile/" . basename($newName);
	$check = "SELECT id FROM tblpet WHERE id=?";
	$stmtCheck = $conn->prepare($check);
	$stmtCheck->bind_param("i", $id);
	$stmtCheck->execute();
	$nat = $stmtCheck->get_result()->fetch_assoc();	
	if ($nat !== null && ($nat['id'] == $id || count($nat) <= 0)) {
		if (!empty($id)) {
			if (!empty($profile2)) {
				$query = "UPDATE tblpet SET  pet_name=?, `picture`=?, `pet_type`=?, `pet_breed`=?, `birthdate`=?, age=?, `gender`=?, `pet_notes`=? WHERE id=?";

				$query1 = "UPDATE tblowner SET OwnerName=?, `OwnerZip`=?, `OwnerMobileNo`=?, `OwnerCity`=?, `OwnerAddress`=?, `OwnerEmail`=? WHERE OwnerID=?";
				$stmtUpdate = $conn->prepare($query);
				$stmtUpdate->bind_param("ssssssssi",  $pname, $newName, $ptype, $bname, $bdate, $age, $gender, $pnotes, $id);

				$stmtUpdate1 = $conn->prepare($query1);
				$stmtUpdate1->bind_param("sssssss", $oname, $zcode, $number, $ocity, $oplace, $email, $ownerid);

				if ($stmtUpdate->execute() && $stmtUpdate1->execute()) {
					$_SESSION['message'] = 'Pet Information has been updated!';
					$_SESSION['success'] = 'success';

					if (move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
						$_SESSION['message'] = 'Pet Information has been updated!';
						$_SESSION['success'] = 'success';
					}
				}
			} else {
				$query = "UPDATE tblpet SET  pet_name=?, `pet_type`=?, `pet_breed`=?, `birthdate`=?, age=?, `gender`=?, `pet_notes`=? WHERE id=?";

				$query1 = "UPDATE tblowner SET OwnerName=?, `OwnerZip`=?, `OwnerMobileNo`=?, `OwnerCity`=?, `OwnerAddress`=?, `OwnerEmail`=? WHERE OwnerID=?";
				$stmtUpdate = $conn->prepare($query);
				$stmtUpdate->bind_param("sssssssi",  $pname, $ptype, $bname, $bdate, $age, $gender, $pnotes, $id);

				$stmtUpdate1 = $conn->prepare($query1);
				$stmtUpdate1->bind_param("ssssssi", $oname, $zcode, $number, $ocity, $oplace, $email, $ownerid);

				if ($stmtUpdate->execute() && $stmtUpdate1->execute()) {
					$_SESSION['message'] = 'Pet Information has been updated!';
					$_SESSION['success'] = 'success';
				}
			}
		} else {
			$_SESSION['message'] = 'Please complete the form!';
			$_SESSION['success'] = 'danger';
		}
	} else {
		$_SESSION['message'] = 'National ID is already taken. Please enter a unique national ID!';
		$_SESSION['success'] = 'danger';
	}
	header("Location: ../petowner.php");
	$conn->close();
?>
