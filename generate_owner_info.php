<?php include 'server/server.php' ?>
<?php

if (!isset($_GET['csrf_token']) || !hash_equals($_GET['csrf_token'], $_SESSION['csrf_token'])) {
    // Invalid CSRF token, handle accordingly (e.g., show an error message)
    $_SESSION['message'] = "Invalid CSRF token. Please try again.";
    $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
    header("Location: owner_info.php"); // Redirect to an error page or any other page where the error message can be displayed
    exit(); // Terminate the script
}

// Use prepared statement to prevent SQL injection
$id = $conn->real_escape_string($_GET['id']);

// Use prepared statement to retrieve pet and owner information
$query = "SELECT * FROM tblpet INNER JOIN tblowner ON tblpet.OwnerID = tblowner.OwnerID WHERE tblowner.OwnerID=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$resident = $result->fetch_assoc();

// Use prepared statement to retrieve service records
$query1 = "SELECT * FROM tblowner INNER JOIN tblrecordservice ON tblowner.OwnerID = tblrecordservice.OwnerID WHERE tblrecordservice.OwnerID=?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("s", $id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$permit = array();
while ($row = $result1->fetch_assoc()) {
    $permit[] = $row;
}

// Fetch the owner's pets using prepared statement
$ownerPets = array();
$queryPets = "SELECT * FROM tblpet WHERE OwnerID=?";
$stmtPets = $conn->prepare($queryPets);
$stmtPets->bind_param("s", $resident['OwnerID']);
$stmtPets->execute();
$resultPets = $stmtPets->get_result();

if ($resultPets->num_rows > 0) {
    while ($row = $resultPets->fetch_assoc()) {
        $ownerPets[] = $row;
    }
}

// Use prepared statement to retrieve payment history
$queryPayments = "SELECT * FROM tblpayments WHERE petOwner=?";
$stmtPayments = $conn->prepare($queryPayments);
$stmtPayments->bind_param("s", $id);
$stmtPayments->execute();
$resultPayments = $stmtPayments->get_result();
$paymentHistory = array();

while ($rowPayment = $resultPayments->fetch_assoc()) {
    $paymentHistory[] = $rowPayment;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Certificate of Indigency - Barangay Management System</title>
    <style>
        @page {
            size: auto;
            margin: 20mm 20mm 20mm 20mm;
        }
    </style>
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
                                <h2 class="text-white fw-bold">Generate Certificate</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">

                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES); ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>"
                                    role="alert">
                                    <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES); ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                    <div>
                                            <button class="btn btn-light" onClick="document.location.href='owner_info.php'"><i class="fa fa-arrow-left"></i></button>
                                        </div>
                                        <div class="card-title">Owner</div>
                                        <div class="card-tools">
                                            <button class="btn btn-info btn-border btn-round btn-sm"
                                                onclick="printDiv('printThis')">
                                                <i class="fa fa-print"></i>
                                                Print Owner Info
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body m-5" id="printThis">
                                    <div class="d-flex flex-wrap justify-content-around"
                                        style="border-bottom:1px solid red">

                                        <div class="text-center">
                                            <h1 class="mt-4 fw-bold mb-5" style="font-size:38px;color:darkblue">PET
                                                OWNER INFORMATION</h1>
                                        </div>

                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <div class="row">

                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-9 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            OWNER NAME:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <input type="text" class="form-control fw-bold"
                                                            style="font-size:20px"
                                                            value="<?= htmlspecialchars($resident['OwnerName'], ENT_QUOTES) ?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-9 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            CONTACT NO:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <input type="text" class="form-control fw-bold"
                                                            style="font-size:20px"
                                                            value="<?= htmlspecialchars($resident['OwnerMobileNo'], ENT_QUOTES) ?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            EMAIL:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <input type="text" class="form-control fw-bold"
                                                            style="font-size:20px"
                                                            value="<?= htmlspecialchars($resident['OwnerEmail'], ENT_QUOTES) ?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-12 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            OWNER
                                                            ADDRESS:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <textarea readonly class="form-control fw-bold"
                                                            style="font-size:20px"
                                                            rows="3"><?= htmlspecialchars($resident['OwnerAddress'] . ' ' . $resident['OwnerCity'] . ' ' . $resident['OwnerZip'], ENT_QUOTES) ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            PETS:
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <?php foreach ($ownerPets as $ownerPet): ?>
                                                            <?php if ($ownerPet['OwnerID'] === $resident['OwnerID']): ?>
                                                                <input type="text" class="form-control fw-bold"
                                                                    style="font-size:20px"
                                                                    value="<?= htmlspecialchars($ownerPet['pet_name'], ENT_QUOTES) ?>"
                                                                    readonly>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-12 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            PAYMENT HISTORY:</h3>
                                                    </div>
                                                    <?php foreach ($paymentHistory as $payment): ?>
                                                        <div class="card mb-3 col-lg-12 col-md-12 col-sm-12">
                                                            <div class="card-body">
                                                                <div class="row">

                                                                    <div class="col-lg-3">
                                                                        <p class="card-text">Amount:
                                                                            <?= htmlspecialchars($payment['amounts'], ENT_QUOTES) ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <p class="card-text">Transaction Date:
                                                                            <?= htmlspecialchars($payment['date'], ENT_QUOTES) ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <p class="card-text">Payment for:
                                                                            <?= htmlspecialchars($payment['details'], ENT_QUOTES) ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
    <?php include 'templates/footer.php' ?>
    <script>
        // Your JavaScript code here
    </script>
</body>

</html>