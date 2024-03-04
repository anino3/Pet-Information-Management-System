<?php
include '../server/server.php';
require '../vendor/autoload.php';  // Include PHPMailer autoloader

// Use environment variable for encryption key
$encryptionKey = getenv('ENCRYPTION_KEY');

// Fallback to a secure random key if the environment variable is not set
if (empty($encryptionKey)) {
    $encryptionKey = bin2hex(random_bytes(32)); // 32 bytes = 256 bits
    putenv("ENCRYPTION_KEY=$encryptionKey"); // Save the encryption key to an environment variable
}

// Store the encryption key in a session variable for later retrieval during restoration
$_SESSION['backup_encryption_key'] = $encryptionKey;

$conn->set_charset("utf8");

$sqlScript = "";
$sqlScript .= "# ABMS : MySQL database backup\n";
$sqlScript .= "# Generated: " . date('l j. F Y') . "\n";
$sqlScript .= "# Hostname: " . $host . "\n";
$sqlScript .= "# Database: " . $database . "\n";
$sqlScript .= "# --------------------------------------------------------\n";

// Get All Table Names From the Database
$tables = array();
$result = $conn->query('SHOW TABLES');

while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    // Add SQL statement to drop existing table
    $sqlScript .= "\n";
    $sqlScript .= "\n";
    $sqlScript .= "#\n";
    $sqlScript .= "# Delete any existing table `" . $table . "`\n";
    $sqlScript .= "#\n";
    $sqlScript .= "\n";
    $sqlScript .= "DROP TABLE IF EXISTS `" . $table . "`;\n";

    /* Table Structure */

    // Comment in SQL-file
    $sqlScript .= "\n";
    $sqlScript .= "\n";
    $sqlScript .= "#\n";
    $sqlScript .= "# Table structure of table `" . $table . "`\n";
    $sqlScript .= "#\n";
    $sqlScript .= "\n";

    // Prepare SQL script for creating table structure
    $query = "SHOW CREATE TABLE $table";
    $result = $conn->query($query);
    $row = $result->fetch_row();

    $sqlScript .= "\n\n" . $row[1] . ";\n\n";

    /* Dump Data */

    // SELECT query to fetch all data from the table
    $selectQuery = "SELECT * FROM $table";
    $result = $conn->query($selectQuery);

    $columnCount = mysqli_num_fields($result);

    // Prepare SQL script for dumping data for each table
    while ($row = $result->fetch_row()) {
        $sqlScript .= "INSERT INTO $table VALUES(";

        for ($j = 0; $j < $columnCount; $j++) {
            if (isset($row[$j])) {
                $sqlScript .= '"' . $conn->real_escape_string($row[$j]) . '"';
            } else {
                $sqlScript .= '""';
            }
            if ($j < ($columnCount - 1)) {
                $sqlScript .= ',';
            }
        }

        $sqlScript .= ");\n";
    }

    $sqlScript .= "\n";
}

if (!empty($sqlScript)) {
    // Generate a secure random IV
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Encrypt the SQL script
    $encryptedSqlScript = openssl_encrypt($sqlScript, 'aes-256-cbc', $encryptionKey, 0, $iv);

    // Save the encrypted SQL script and IV to a backup file
    $backupFileName = $database . '_backup_' . time() . '.enc';
    file_put_contents($backupFileName, $iv . $encryptedSqlScript);

    // Fetch the user's email from the session
    $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

    // Send email to the user with the encryption key
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'franciabritz17@gmail.com';  // Change this to your email address
        $mail->Password   = 'jnjozetrhvllvbhl'; // Change this to your email password
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('franciabritz17@gmail.com'); // Change this to your name and email
        $mail->addAddress($userEmail);  // Use the user's email obtained from the session

        $mail->isHTML(true);
        $mail->Subject = 'Encryption Key for Database Backup';
        $mail->Body    = 'Your encryption key is: ' . $encryptionKey;

        $mail->send();
        $_SESSION['message'] = 'Encryption code sent to the user\'s email!';
        $_SESSION['success'] = 'success';
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $_SESSION['message'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['success'] = 'danger';
    }

    // Download the encrypted SQL backup file to the browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backupFileName));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backupFileName));
    ob_clean();
    flush();
    readfile($backupFileName);
    unlink($backupFileName); // Remove the temporary file
}
?>
