<?php 
	include '../server/server.php';

    $username 	= $conn->real_escape_string($_POST['username']);
	$cur_pass 	= $_POST['cur_pass'];
	$new_pass 	= $_POST['new_pass'];
    $con_pass 	= $_POST['con_pass'];

	if (!empty($username)) {

        if ($new_pass == $con_pass) {

            $check_query = "SELECT id, password FROM tbl_users WHERE username=?";
            $stmt_check = $conn->prepare($check_query);
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $res = $stmt_check->get_result();

            if ($res->num_rows) {
                $user_data = $res->fetch_assoc();
                $hashed_cur_pass = $user_data['password'];

                if (password_verify($cur_pass, $hashed_cur_pass)) {

                    $update_query = "UPDATE tbl_users SET `password`=? WHERE username=?";
                    $stmt_update = $conn->prepare($update_query);

                    // Use password_hash to securely hash the new password
                    $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

                    $stmt_update->bind_param("ss", $hashed_new_pass, $username);
                    $result = $stmt_update->execute();

                    if ($result === true) {
                        $_SESSION['message'] = 'Password has been updated!';
                        $_SESSION['success'] = 'success';
                    } else {
                        $_SESSION['message'] = 'Something went wrong!';
                        $_SESSION['success'] = 'danger';
                    }
                } else {
                    $_SESSION['message'] = 'Current Password is incorrect!';
                    $_SESSION['success'] = 'danger';
                }
            } else {
                $_SESSION['message'] = 'No matching user found!';
                $_SESSION['success'] = 'danger';
            }

        } else {
            $_SESSION['message'] = 'Password did not match!';
		    $_SESSION['success'] = 'danger';
        }
    } else {
		$_SESSION['message'] = 'No Username found!';
		$_SESSION['success'] = 'danger';
	}

    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

	$conn->close();
?>
