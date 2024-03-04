<?php 
	include('../server/server.php');

    // Check if the user is logged in
    if(!isset($_SESSION['username'])){
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }

    // CSRF protection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['message'] = "Invalid CSRF token. Please try again.";
            $_SESSION['success'] = "danger";
            header("Location: dashboard.php");
            exit;
        }
    }

	$id = $conn->real_escape_string($_POST['id']);
    $profile = $conn->real_escape_string($_POST['profileimg']); // base 64 image
	$profile2 = $_FILES['img']['name'];
    
    // Change profile2 name
    $newName = date('dmYHis').str_replace(" ", "", $profile2);

    // Image file directory
    $target = "../assets/uploads/avatar/".basename($newName);

    if(!empty($id)){
        $query = "SELECT * FROM tbl_users WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 0){
            $_SESSION['message'] = 'User not found!';
            $_SESSION['success'] = 'danger';

            if (isset($_SERVER["HTTP_REFERER"])) {
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
        } else {
            if(!empty($profile) && !empty($profile2)){
                $insert = "UPDATE tbl_users SET avatar=? WHERE id=?";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("si", $profile, $id);
                $stmt->execute();
                $_SESSION['avatar'] = $profile;
            } else if(!empty($profile) && empty($profile2)){
                $insert = "UPDATE tbl_users SET avatar=? WHERE id=?";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("si", $profile, $id);
                $stmt->execute();
                $_SESSION['avatar'] = $profile;
            } else {
                $insert = "UPDATE tbl_users SET avatar=? WHERE id=?";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("si", $newName, $id);
                $stmt->execute();
                move_uploaded_file($_FILES['img']['tmp_name'], $target);
                $_SESSION['avatar'] = $newName;
            }

            $_SESSION['message'] = "Profile has been updated! Please login again!";
            $_SESSION['success'] = 'success';
        }
        
    } else {
        $_SESSION['message'] = 'Please fill up the form completely!';
        $_SESSION['success'] = 'danger';
    }

    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
	$conn->close();
?>
