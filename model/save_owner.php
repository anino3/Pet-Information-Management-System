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
        header("Location: owner_info.php");
    }
}

	$randomNumber = rand(100000, 999999);
	$national_id = $conn->real_escape_string($_POST['national']);
	$pnotes = $conn->real_escape_string($_POST['pnotes']);
	$pname = $conn->real_escape_string($_POST['pname']);
	$oname = $conn->real_escape_string($_POST['oname']);
	$bname = $conn->real_escape_string($_POST['bname']);
	$ocity = $conn->real_escape_string($_POST['ocity']);
	$bplace = $conn->real_escape_string($_POST['bplace']);
	$bdate = $conn->real_escape_string($_POST['bdate']);
	$age = $conn->real_escape_string($_POST['age']);
	$zcode = $conn->real_escape_string($_POST['zcode']);
	$pnotes = $conn->real_escape_string($_POST['pnotes']);
	$vstatus = $conn->real_escape_string($_POST['vstatus']);
	$pgender = $conn->real_escape_string($_POST['pgender']);
	$email = $conn->real_escape_string($_POST['email']);
	$number = $conn->real_escape_string($_POST['onumber']);
	$ptype = $conn->real_escape_string($_POST['ptype']);
	$oplace = $conn->real_escape_string($_POST['oplace']);
	$profile 	= $conn->real_escape_string($_POST['profileimg']); // base 64 image
	$profile2 	= $_FILES['img']['name'];

	// change profile2 name
	$newName = date('dmYHis').str_replace(" ", "", $profile2);

	// image file directory
	$target = "../assets/uploads/resident_profile/".basename($newName);

	$query1 = "INSERT INTO tblowner (`OwnerName`,`OwnerAddress`, `OwnerCity`, `OwnerZip`, `OwnerMobileNo`, `OwnerEmail`) 
			VALUES ('$oname','$oplace','$ocity','$zcode','$number','$email')";

	if ($conn->query($query1) === true) {
		$ownerID = $conn->insert_id; // Retrieve the auto-generated OwnerID

		$query2 = "INSERT INTO tblresident (`national_id`,`pet_name`,`pet_type`, `pet_breed`, birthdate, `age`, `gender`, `pet_notes`,`picture`, `OwnerID`) 
				VALUES ('$national_id','$pname','$ptype','$bname','$bdate','$age','$pgender','$pnotes', '$profile', '$ownerID')";

		if ($conn->query($query2) === true) {
			$_SESSION['message'] = 'Resident Information has been saved!';
			$_SESSION['success'] = 'success';

			if (!empty($profile2) && move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
				$_SESSION['message'] = 'Resident Information has been saved!';
				$_SESSION['success'] = 'success';
			}
		}
	} else {
		$_SESSION['message'] = 'Please complete the form!';
		$_SESSION['success'] = 'danger';
	}

	header("Location: ../owner_info.php");

	$conn->close();
?>
