<?php // function to get the current page name
function PageName() {
  return substr( $_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"],"/") +1);
}

$current_page = PageName();
?>
<div class="sidebar sidebar-style-2">			
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    <?php if(!empty($_SESSION['avatar'])): ?>
                        <img src="<?= preg_match('/data:image/i', $_SESSION['avatar']) ? $_SESSION['avatar'] : 'assets/uploads/avatar/'.$_SESSION['avatar'] ?>" alt="..." class="avatar-img rounded-circle">
                    <?php else: ?>
                        <img src="assets/img/person.png" alt="..." class="avatar-img rounded-circle">
                    <?php endif ?>
                   
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="<?= isset($_SESSION['username']) && $_SESSION['role']=='administrator' ? '#collapseExample' : 'javascript:void(0)' ?>" aria-expanded="true">
                        <span>
                        <?= isset($_SESSION['username']) ? ucfirst($_SESSION['username']) : 'Guest User' ?>
                            <span class="user-level"><?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Guest' ?></span>
                        <?= isset($_SESSION['username']) && $_SESSION['role']=='administrator' ? '<span class="caret"></span>' : null ?> 
                        </span>
                    </a>
                    <div class="clearfix"></div>
                    <div class="collapse in" id="collapseExample">
                        <ul class="nav">
                            <li>
                                <a href="#edit_profile" data-toggle="modal">
                                    <span class="link-collapse">Edit Profile</span>
                                </a>
                                <a href="#changepass" data-toggle="modal">
                                    <span class="link-collapse">Change Password</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ul class="nav nav-primary">
                <li class="nav-item <?= $current_page=='dashboard.php' ? 'active' : null ?>">
                    <a href="dashboard.php" >
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu</h4>
                </li>
               
                <li class="nav-item <?= $current_page=='petowner.php' ? 'active' : null ?>">
                    <a href="petowner.php">
                        <i class="icon-people"></i>
                        <p>Pet Management</p>
                    </a>
                </li>
                <li class="nav-item <?= $current_page=='owner_info.php' ? 'active' : null ?>">
                    <a href="owner_info.php">
                        <i class="icon-badge"></i>
                        <p>Pet Owner Management</p>
                    </a>
                </li>
                
                <li class="nav-item <?= $current_page=='pet_services.php' ? 'active' : null ?>">
                    <a href="pet_services.php">
                        <i class="icon-doc"></i>
                        <p>Purrfect Clinic Services</p>
                    </a>
                </li>
                
                <li class="nav-item <?= $current_page=='operation.php' ? 'active' : null ?>">
                    <a href="operation.php">
                        <i class="icon-layers"></i>
                        <p>Clinical Records</p>
                    </a>
                </li>
                <li class="nav-item <?= $current_page=='medicines.php'  ? 'active' : null ?>">
                    <a href="medicines.php">
                        <i class="icon-layers"></i>
                        <p>Medicine Records</p>
                    </a>
                </li>

                <?php if(isset($_SESSION['username']) && $_SESSION['role']=='staff'): ?>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">System</h4>
                    </li>
                    <li class="nav-item">
                        <a href="#support" data-toggle="modal">
                            <i class="fas fa-flag"></i>
                            <p>Support</p>
                        </a>
                    </li>
                <?php endif ?>
                <?php if(isset($_SESSION['username']) && $_SESSION['role']=='administrator'): ?>
                <li class="nav-item <?= $current_page=='revenue.php' ? 'active' : null ?>">
                    <a href="revenue.php">
                        <i class="fas fa-dollar-sign"></i>
                        <p>Revenues</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">System</h4>
                </li>
                <li class="nav-item <?= $current_page=='users.php'  || $current_page=='backup.php' || $current_page=='restore.php' ? 'active' : null ?>">
                    <a href="#settings" data-toggle="collapse" class="collapsed" aria-expanded="false">
                        <i class="icon-wrench"></i>
                            <p>Settings</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?= $current_page=='users.php'  || $current_page=='backup.php' || $current_page=='restore.php' ? 'show' : null ?>" id="settings">
                        <ul class="nav nav-collapse">
                            
                           
                            
                
                            
                            <?php if($_SESSION['role']=='staff'):?>
                                
                            <?php else: ?>
                               
                                <li class="<?= $current_page=='users.php' ? 'active' : null ?>">
                                    <a href="users.php">
                                        <span class="sub-item">Users</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a href="backup/backup.php">
                                        <span class="sub-item">Backup</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#restore" data-toggle="modal">
                                        <span class="sub-item">Restore</span>
                                    </a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>