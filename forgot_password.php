<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure' => true,
        'httponly' => true,
    ]);
    session_start();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php' ?>
    <title>Forgot Password - Purrfect Pet</title>
</head>
<body class="login">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['success']; ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                    <?= $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif ?>
            <h3 class="text-center">Forgot Your Password?</h3>
            <div class="login-form">
            <form method="POST" action="model/reset_password.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-group form-floating-label">
                        <input id="email" name="email" type="email" class="form-control input-border-bottom" required>
                        <label for="email" class="placeholder">Email</label>
                    </div>

                    <div class="form-action mb-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-rounded btn-login">Reset Password</button>
                    </div>
                </form>
                <div class="form-action mb-3">
                       <a href="login.php">Go back to login </a>
                    
                    </div>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php' ?>
</body>
</html>