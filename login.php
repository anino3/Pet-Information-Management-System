<?php 
    session_start(); 

    if(isset($_SESSION['username'])){
        header('Location: dashboard.php');
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Check if there is a login message
    $loginMessage = isset($_SESSION['login_message']) ? $_SESSION['login_message'] : '';

    // Unset the login message to avoid displaying it again
    unset($_SESSION['login_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php' ?>
    <title>Login - Purrfect Pet</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="login">
    <?php include 'templates/loading_screen.php' ?>
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <?php if(!empty($loginMessage)): ?>
                <div class="alert alert-danger bg-danger text-light" role="alert">
                    <?= $loginMessage; ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['success']; ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                    <?= $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <h3 class="text-center">Sign In Here</h3>
            <div class="login-form">
                <form method="POST" action="model/login.php">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group form-floating-label">
                        <input id="username" name="username" type="text" class="form-control input-border-bottom" required>
                        <label for="username" class="placeholder">Username</label>
                    </div>

                    <div class="form-group form-floating-label">
                        <input id="password" name="password" type="password" class="form-control input-border-bottom" required>
                        <label for="password" class="placeholder">Password</label>
                        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>

                    <!-- reCAPTCHA Challenge -->
                    <div class="g-recaptcha" data-sitekey="6LfhYnopAAAAAF8R5jMj13Tz5_RIu-foWmEL2Kg2"></div>

                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-primary btn-rounded btn-login">Sign In</button>
                    </div>
                </form>
                
                <div class="form-action mb-3">
                    <a href="forgot_password.php">Forgot Password</a>
                </div>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php' ?>
</body>
</html>
