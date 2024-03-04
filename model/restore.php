<?php
include '../server/server.php';

if (!empty($_FILES)) {
    // Validating SQL file type by extensions
    $allowedExtensions = array("enc");
    $fileExtension = strtolower(pathinfo($_FILES["backup_file"]["name"], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['message'] = 'Invalid File Type';
        $_SESSION['success'] = 'danger';

        header("Location: ../dashboard.php");
    } else {
        if (is_uploaded_file($_FILES["backup_file"]["tmp_name"])) {
            // Check if the provided encryption key matches
            $inputEncryptionKey = isset($_POST['encryption_key']) ? $_POST['encryption_key'] : '';

            // Retrieve the stored encryption key from a secure location
            $storedEncryptionKey = $_SESSION['backup_encryption_key'];

            if (!hash_equals($inputEncryptionKey, $storedEncryptionKey)) {
                $_SESSION['message'] = 'Incorrect Encryption Key';
                $_SESSION['success'] = 'danger';

                header("Location: ../dashboard.php");
            }

            $response = restoreEncryptedMysqlDB($_FILES["backup_file"]["tmp_name"], $inputEncryptionKey, $conn);

            if ($response) {
                $_SESSION['message'] = 'Database restored successfully.';
                $_SESSION['success'] = 'success';
                header("Location: ../dashboard.php");
            } else {
                $_SESSION['message'] = 'Database not restored completely.';
                $_SESSION['success'] = 'danger';
                header("Location: ../dashboard.php");
            }
        } else {
            $_SESSION['message'] = 'Error uploading the file.';
            $_SESSION['success'] = 'danger';
            header("Location: ../dashboard.php");
        }
    }
}


function restoreEncryptedMysqlDB($filePath, $encryptionKey, $conn)
{
    $error = '';

    if (file_exists($filePath)) {
        // Read the content of the encrypted file
        $encryptedContent = file_get_contents($filePath);

        // Extract IV and encrypted SQL script
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($encryptedContent, 0, $ivLength);
        $encryptedSqlScript = substr($encryptedContent, $ivLength);

        // Decrypt the SQL script
        $sqlScript = openssl_decrypt($encryptedSqlScript, 'aes-256-cbc', $encryptionKey, 0, $iv);

        // Trim whitespaces
        $sqlScript = trim($sqlScript);

        // Disable foreign key checks
        $conn->query('SET foreign_key_checks = 0');

        // Execute the SQL script
        $queries = explode(';', $sqlScript);
        foreach ($queries as $query) {
            // Trim each query to avoid empty queries
            $query = trim($query);

            if (!empty($query)) {
                $result = $conn->query($query);

                if (!$result) {
                    $error .= $conn->error . "\n";
                }
            }
        }

        // Re-enable foreign key checks
        $conn->query('SET foreign_key_checks = 1');

        if ($error) {
            $response = false;
            // Log or display the error
            error_log($error);
        } else {
            $response = true;
        }
    } else {
        $response = false;
    }

    return $response;
}
?>
