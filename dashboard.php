<?php
include 'server/server.php';



// Fetch pet data

$queryPet= "SELECT * FROM tblpet WHERE OwnerID IS NOT NULL";
    $result = $conn->query($queryPet);
	$totalpet = $result->num_rows;

    $queryPetsMale = "SELECT * FROM tblpet WHERE gender = 'Male' AND OwnerID IS NOT NULL";
    $resultPetsMale = $conn->query($queryPetsMale);
    $malePets = $resultPetsMale->num_rows;
    
    // Number of female pets for the given OwnerID
    $queryPetsFemale = "SELECT * FROM tblpet WHERE gender = 'Female' AND OwnerID IS NOT NULL";
    $resultPetsFemale = $conn->query($queryPetsFemale);
    $femalePets = $resultPetsFemale->num_rows;

    $queryOwner = "SELECT * FROM tblowner";
    $resultOwner = $conn->query($queryOwner);
	$totalOwners = $resultOwner->num_rows;

    $queryOperation = "SELECT * FROM tbloperation";
    $resultOperation = $conn->query($queryOperation);
	$totalOperations = $resultOperation->num_rows;

    $date = date('Y-m-d'); 
	$query8 = "SELECT SUM(amounts) as am FROM tblpayments WHERE `date`='$date'";
	$revenue = $conn->query($query8)->fetch_assoc();

// Fetch scheduled operations
$queryScheduled = "SELECT * FROM tbloperation WHERE status = 'Scheduled'";
$resultScheduled = $conn->query($queryScheduled);



// Store scheduled operations in an array
$scheduledEvents = [];
while ($rowScheduled = $resultScheduled->fetch_assoc()) {
    $scheduledEvents[] = [
        'title' => $rowScheduled['operationType'],
        'start' => $rowScheduled['date'] . 'T' . $rowScheduled['time'],
        'end' => $rowScheduled['date'] . 'T' . $rowScheduled['time'],
        'description' => $rowScheduled['details'],
        'operationID' => $rowScheduled['id'], // Include operation ID for redirection
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Dashboard - Purffect Clinic</title>

    <!-- Add jQuery, Moment.js, and FullCalendar CSS and JS links without integrity checks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
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
                                <h2 class="text-white fw-bold">Dashboard</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner mt--2">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['success']; ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>"
                            role="alert">
                            <?php echo $_SESSION['message']; ?>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif ?>
                    <div class="row">
                    <div class="col-md-4">
							<div class="card card-stats card-primary card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Population</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($totalpet) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="petinfo.php?state=all" class="card-link text-light">Total Pet Population </a>
								</div>
							</div>
						</div>

                        <div class="col-md-4">
                            <div class="card card-stats card-secondary card-round">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="icon-big text-center">
                                                <i class="flaticon-user"></i>
                                            </div>
                                        </div>
                                        <div class="col-3 col-stats">
                                        </div>
                                        <div class="col-6 col-stats">
                                            <div class="numbers mt-4">
                                                <h2 class="fw-bold text-uppercase">Pet Male</h2>
                                                <h3 class="fw-bold">
                                                    <?= number_format($malePets) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <a href="petinfo.php?state=male" class="card-link text-light">Total Male </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats card-warning card-round">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="icon-big text-center">
                                                <i class="icon-user-female"></i>
                                            </div>
                                        </div>
                                        <div class="col-3 col-stats">
                                        </div>
                                        <div class="col-6 col-stats">
                                            <div class="numbers mt-4">
                                                <h2 class="fw-bold text-uppercase">Pet Female</h2>
                                                <h3 class="fw-bold text-uppercase">
                                                    <?= number_format($femalePets) ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <a href="petinfo.php?state=female" class="card-link text-light">Total Female
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="row">
                    <div class="col-md-4">
							<div class="card card-stats card-success card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-fingerprint"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Pet Owners</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($totalOwners) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="owner_info.php" class="card-link text-light">Total Pet Owner </a>
								</div>
							</div>
						</div>
                        <div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#a349a3; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-list"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Total Operations</h2>
												<h3 class="fw-bold"><?= number_format($totalOperations) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="operation.php" class="card-link text-light">Operation Information</a>
								</div>
							</div>
						</div>
                        <?php if (isset($_SESSION['username']) && $_SESSION['role'] == 'administrator'): ?>
                        <div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#3E9C35; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-dollar-sign"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Revenue - by day</h2>
												<h3 class="fw-bold text-uppercase">P <?= number_format($revenue['am'],2) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="revenue.php" class="card-link text-light">All Revenues</a>
								</div>
							</div>
						</div>
                        <?php endif ?>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title fw-bold">Purrfect Clinic, Inc.</div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p><strong style="font-size: 15px;">At Purrfect Clinic, we understand that your
                                            furry companions deserve the very best care. That's why we are dedicated to
                                            providing exceptional veterinary services tailored to meet the unique needs
                                            of cats. With our team of skilled veterinarians and compassionate staff, we
                                            strive to ensure that every visit to our clinic is a purrfect experience for
                                            both you and your beloved feline friend.</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FullCalendar container -->
                    <div id="calendar"></div>

                </div>
            </div>
            <!-- Main Footer -->
            <?php include 'templates/main-footer.php' ?>
            <!-- End Main Footer -->

        </div>

    </div>
    <?php include 'templates/footer.php' ?>

    <!-- Initialize FullCalendar in a script section -->
    <!-- Initialize FullCalendar in a script section -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            // Add your FullCalendar options here
            height: 600, // Set your desired height
            events: <?php echo json_encode($scheduledEvents); ?>,
            eventClick: function (info) {
                // Include the CSRF token in the redirection URL
                var csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
                var redirectURL = 'generate_operation_report.php?id=' + info.event.extendedProps.operationID + '&csrf_token=' + csrfToken;
                
                // Redirect to generate_operation_report.php with operation ID and CSRF token
                window.location.href = redirectURL;
            }
        });
        calendar.render();
    });
</script>

</body>

</html>
