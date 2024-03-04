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
    <title>Change Password</title>
</head>
<body class="login">
    <?php include 'templates/loading_screen.php' ?>
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                    <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif ?>
            <h3 class="text-center">Change Password</h3>
            <div class="login-form">
                <form method="POST" action="model/change-password.php">

                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-group form-floating-label">
    <input id="password" type="password" name="password" class="form-control input-border-bottom" 
           pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required
           oninvalid="this.setCustomValidity('Password must be at least 8 characters long and include at least one uppercase letter, one numeric digit, and one special character.')"
           oninput="setCustomValidity('')">
    <label for="password" class="placeholder">New Password</label>
    <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
</div>

                    <div class="form-group form-floating-label">
                        <input id="confirm_password" name="confirm_password" type="password" class="form-control input-border-bottom" required>
                        <label for="confirm_password" class="placeholder">Confirm New Password</label>
                        <span toggle="#confirm_password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>

                    <?php if(isset($_GET['token'])): ?>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                        <p class="text-danger">Token not provided.</p>
                    <?php endif; ?>

                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-primary btn-rounded btn-login">Change Password</button>
                    </div>
                    <div class="form-action mb-3">
                       <a href="login.php">Go back to login </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php' ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.querySelector('#password');
        const confirmPassField = document.querySelector('#confirm_password');
        const passwordToggle = document.querySelector('#password + .toggle-password');

        passwordToggle.addEventListener('click', function () {
            togglePasswordVisibility(passwordField);
        });

        function togglePasswordVisibility(inputField) {
            const fieldType = inputField.type === 'password' ? 'text' : 'password';
            inputField.type = fieldType;
            passwordToggle.classList.toggle('fa-eye-slash');
            passwordToggle.classList.toggle('fa-eye');
        }

        passwordField.addEventListener('input', function () {
            confirmPassField.setAttribute('pattern', passwordField.value);
        });
    });
</script>

</body>
</html>
