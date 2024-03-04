<?php

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';


?>



<!-- Modal for Change Password -->
<div class="modal fade" id="changepass" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="model/change_password.php">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" placeholder="Enter Name" readonly name="username" value="<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Current Password</label>
                        <input type="password" id="cur_pass" class="form-control" placeholder="Enter Current Password" name="cur_pass" required>
                        <span toggle="#cur_pass" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>New Password</label>
                        <input type="password" id="new_pass" class="form-control" placeholder="Enter New Password"
                            name="new_pass" pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required
                            oninvalid="this.setCustomValidity('Password must be at least 8 characters long and include at least one uppercase letter, one numeric digit, and one special character.')"
                            oninput="setCustomValidity('')">
                        <span toggle="#new_pass" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Confirm Password</label>
                        <input type="password" id="con_pass" class="form-control" placeholder="Confirm Password" name="con_pass"
                            pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required
                            oninvalid="this.setCustomValidity('Password must be at least 8 characters long and include at least one uppercase letter, one numeric digit, and one special character.')"
                            oninput="setCustomValidity('')">
                        <span toggle="#con_pass" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Change</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Restore Database -->
<div class="modal fade" id="restore" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Restore Database</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="model/restore.php" enctype="multipart/form-data">
                    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
                    <div class="form-group form-floating-label">
                        <label for="backup_file">Upload Encrypted SQL file (max size: 1 MB)</label>
                        <input type="file" class="form-control" accept=".enc" name="backup_file" required>
                    </div>
                    <div class="form-group form-floating-label">
                        <label for="encryption_key">Encryption Key</label>
                        <input type="password" class="form-control" name="encryption_key" required>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Restore</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Edit Profile -->
<div class="modal fade" id="edit_profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create System User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="model/edit_profile.php" enctype="multipart/form-data">
                    <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="size" value="1000000">
                    <div class="text-center">
                        <div id="my_camera" style="height: 250;" class="text-center">
                            <?php if (empty($_SESSION['avatar'])): ?>
                                <img src="assets/img/person.png" alt="..." class="img img-fluid" width="250">
                            <?php else: ?>
                                <img src="<?= preg_match('/data:image/i', $_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar'], ENT_QUOTES, 'UTF-8') : 'assets/uploads/avatar/' . htmlspecialchars($_SESSION['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="..." class="img img-fluid" width="250">
                            <?php endif ?>
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
            </div>
            <div class="modal-footer">
                <input type="hidden" value="<?= htmlspecialchars($_SESSION['id'], ENT_QUOTES, 'UTF-8'); ?>" name="id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>
