<?php
include 'server/server.php';

// Start or resume a session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict' // Add SameSite attribute to mitigate CSRF attacks
    ]);
    session_start();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Sanitize and validate user input
$user = isset($_SESSION['username']) ? filter_var($_SESSION['username'], FILTER_SANITIZE_STRING) : null;


// Check if 'csrf_token' is set in the session
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

// Prepare and execute SQL query
$query = "SELECT * FROM tbl_users WHERE NOT username=? ORDER BY `created_at` DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php'; ?>
    <title>User Management - Purrfect Clinic Information Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-dco0wX3U1D6Dc3RaxrVZdC7r3N+gSRL71LXC/FA9ziCOQof1HT9saDq2NYPFI/djn1l2DbcFe+o3TM/G+WylMw==" crossorigin="anonymous" />

</head>

<body>
    <?php include 'templates/loading_screen.php'; ?>
    <div class="wrapper">
        <!-- Main Header -->
        <?php include 'templates/main-header.php'; ?>
        <!-- End Main Header -->

        <!-- Sidebar -->
        <?php include 'templates/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="content">
                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white fw-bold">Settings</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">

                            <?php if (isset($_SESSION['message'])) : ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">User Management</div>
                                        <div class="card-tools">
                                            <a href="#add" data-toggle="modal" class="btn btn-info btn-border btn-round btn-sm">
                                                <i class="fa fa-plus"></i>
                                                User
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Username</th>
                                                    <th scope="col">User Type</th>
                                                    <th scope="col">Created At</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($users)) : ?>
                                                    <?php $no = 1;
                                                    foreach ($users as $row) : ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td>
                                                                <div class="avatar avatar-xs">
                                                                    <img src="<?= preg_match('/data:image/i', $row['avatar']) ? htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') : 'assets/uploads/avatar/' . htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="User Profile" class="avatar-img rounded-circle">
                                                                </div>
                                                                <?= htmlspecialchars(ucwords($row['username']), ENT_QUOTES, 'UTF-8') ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['user_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td>
                                                                <div class="form-button-action">
                                                                    <a type="button" data-toggle="tooltip" href="model/remove_user.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-link btn-danger" data-original-title="Remove">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php $no++;
                                                    endforeach ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No Available Data</td>
                                                    </tr>
                                                <?php endif ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Username</th>
                                                    <th scope="col">User Type</th>
                                                    <th scope="col">Created At</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create System User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/save_user.php" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

                                <input type="hidden" name="size" value="1000000">
                                <div class="text-center">
                                    <div id="my_camera" style="height: 250;" class="text-center">
                                        <img src="assets/img/person.png" alt="..." class="img img-fluid" width="250">
                                    </div>
                                    <div class="form-group d-flex justify-content-center">
                                        <button type="button" class="btn btn-danger btn-sm mr-2" id="open_cam">Open Camera</button>
                                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="save_photo()">Capture</button>
                                    </div>
                                    <div id="profileImage">
                                        <input type="hidden" name="profileimg">
                                    </div>
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="img" accept="image/*">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" placeholder="Enter Username" name="username" required>
                                </div>
                                <div class="form-group">
    <label>Password</label>
    <div class="input-group">
        <input type="password" class="form-control" placeholder="Enter Password" name="pass" id="passwordField" required>
        <div class="input-group-append">
            <span class="input-group-text" id="togglePassword">
                <i class="fas fa-eye-slash" id="toggleIcon"></i>
            </span>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Password Confirm</label>
    <div class="input-group">
        <input type="password" class="form-control" placeholder="Confirm Password" name="pass_confirm" id="confirmPasswordField" required>
        <div class="input-group-append">
            <span class="input-group-text" id="toggleConfirmPassword">
                <i class="fas fa-eye-slash" id="toggleConfirmIcon"></i>
            </span>
        </div>
    </div>
</div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" placeholder="Enter Email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label>User Type</label>
                                    <select class="form-control" id="pillSelect" required name="user_type">
                                        <option disabled selected>Select User Type</option>
                                        <option value="staff">Staff</option>
                                        <option value="administrator">Administrator</option>
                                    </select>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Footer -->
            <?php include 'templates/main-footer.php'; ?>
            <!-- End Main Footer -->
        </div>
    </div>
    <?php include 'templates/footer.php'; ?>
    

    <script>
         document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('form').addEventListener('submit', function (e) {
            const password = document.querySelector('input[name="pass"]').value;
            const confirmPassword = document.querySelector('input[name="pass_confirm"]').value;

            // Validate password complexity
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/;
            if (!passwordRegex.test(password)) {
                alert('Password must contain at least one capital letter, numeric digit, special character, and the password must be atleast 8 characters.');
                e.preventDefault(); // Prevent form submission
                return;
            }

            // Check if password and password confirmation match
            if (password !== confirmPassword) {
                alert('Password and password confirmation do not match.');
                e.preventDefault(); // Prevent form submission
            }
        });

        const togglePassword = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');
        const passwordField = document.getElementById('passwordField');

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');
        const confirmPasswordField = document.getElementById('confirmPasswordField');

        togglePassword.addEventListener('click', function () {
            togglePasswordVisibility(passwordField, toggleIcon);
        });

        toggleConfirmPassword.addEventListener('click', function () {
            togglePasswordVisibility(confirmPasswordField, toggleConfirmIcon);
        });

        function togglePasswordVisibility(inputField, icon) {
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                inputField.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    });
    </script>
</body>

</html>
