<?php include 'server/server.php' ?>
<?php 
    $state = $_GET['state'];
    
    if($state == 'male') {
        $query = "SELECT * FROM tblpet WHERE gender='Male' AND OwnerID IS NOT NULL";
        $result = $conn->query($query);
    } elseif($state == 'female') {
        $query = "SELECT * FROM tblpet WHERE gender='Female' AND OwnerID IS NOT NULL";
        $result = $conn->query($query);
    } elseif($state == 'all') {
        $query = "SELECT * FROM tblpet WHERE OwnerID IS NOT NULL";
        $result = $conn->query($query);
    } else {
        // Handle other states as needed
        // You can add more conditions based on your requirements
    }
	
    $pets = array();
	while($row = $result->fetch_assoc()){
		$pets[] = $row; 
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php' ?>
    <title>Pet Information -  Purrfect Clinic Information Management System</title>
</head>
<body>
    <?php include 'templates/loading_screen.php' ?>
    <div class="wrapper">
        <!-- Main Header -->
        <?php include 'templates/main-header.php' ?>
        <!-- End Main Header -->

        <!-- Sidebar -->
        <?php include 'templates/sidebar.php' ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="content">
                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white fw-bold"><?php echo 'Pet Information'; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['success']; ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                        </div>
                    <?php unset($_SESSION['message']); ?>
                    <?php endif ?>
                    
                    <!-- Statistics Card for Pets -->
                    <div class="row mt--2">
                        <div class="col">
                            <div class="card card-stats card-secondary card-round">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="icon-big text-center">
                                                <?php 
                                                    if($state == 'male'){
                                                        echo '<i class="flaticon-users"></i>';
                                                    } elseif($state == 'female'){
                                                        echo ' <i class="flaticon-user"></i>';
                                                    } else {
                                                        echo '<i class="flaticon-users"></i>';
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-4 col-stats">
                                        </div>
                                        <div class="col-5 col-stats">
                                            <div class="numbers">
                                                <p class="card-category">
                                                    <?php 
                                                        if($state == 'male'){
                                                            echo 'All Male Pets';
                                                        } elseif($state == 'female'){
                                                            echo 'All Female Pets';
                                                        } else {
                                                            echo 'All Pets';
                                                        }
                                                    ?>
                                                </p>
                                                <h4 class="card-title"><?= number_format(count($pets)) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Statistics Card for Pets -->

                    <div class="row mt--2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">
                                            <?php 
                                                if($state=='male'){
                                                    echo 'All Male Pets';
                                                } elseif($state=='female'){
                                                    echo 'All Female Pets';
                                                } else {
                                                    echo 'All Pets';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="pettable" class="display table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Pet Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Breed</th>
                                                    <th scope="col">Birthdate</th>
                                                    <th scope="col">Age</th>
                                                    <th scope="col">Gender</th>
                                                    <th scope="col">Notes</th>
                                                    <th scope="col">Owner ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($pets)): ?>
                                                    <?php foreach($pets as $row): ?>
                                                        <tr>
                                                            <td><?= $row['pet_name'] ?></td>
                                                            <td><?= $row['pet_type'] ?></td>
                                                            <td><?= $row['pet_breed'] ?></td>
                                                            <td><?= $row['birthdate'] ?></td>
                                                            <td><?= $row['age'] ?></td>
                                                            <td><?= $row['gender'] ?></td>
                                                            <td><?= $row['pet_notes'] ?></td>
                                                            <td><?= $row['OwnerID'] ?></td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                <?php endif ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th scope="col">Pet Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Breed</th>
                                                    <th scope="col">Birthdate</th>
                                                    <th scope="col">Age</th>
                                                    <th scope="col">Gender</th>
                                                    <th scope="col">Notes</th>
                                                    <th scope="col">Owner ID</th>
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

            <!-- Main Footer -->
            <?php include 'templates/main-footer.php' ?>
            <!-- End Main Footer -->
            
        </div>
        
    </div>
    <?php include 'templates/footer.php' ?>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pettable').DataTable();
        });
    </script>
</body>
</html>
