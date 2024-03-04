<?php include 'server/server.php' ?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: petowner.php");
    }
}
$id = $_GET['id'];


// Fetch pet information using prepared statement
$query = "SELECT * FROM tblpet INNER JOIN tblowner ON tblpet.OwnerID = tblowner.OwnerID WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$resident = $result->fetch_assoc();

// Fetch records related to the pet using prepared statement
$queryRecords = "SELECT * FROM tblrecordservice WHERE petID=?";
$stmtRecords = $conn->prepare($queryRecords);
$stmtRecords->bind_param('i', $id);
$stmtRecords->execute();
$resultRecords = $stmtRecords->get_result();
$petRecords = $resultRecords->fetch_all(MYSQLI_ASSOC);

$queryOperation = "SELECT * FROM tbloperation WHERE petID=?";
$stmtOperation = $conn->prepare($queryOperation);
$stmtOperation->bind_param('i', $id);
$stmtOperation->execute();
$resultOperation = $stmtOperation->get_result();
$petOperations = $resultOperation->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Generate Resident Profile - Barangay Management System</title>
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
                                <h2 class="text-white fw-bold">Generate Resident Profile</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">

                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>"
                                    role="alert">
                                    <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div>
                                            <button class="btn btn-light" onClick="document.location.href='petowner.php'"><i class="fa fa-arrow-left"></i></button>
                                        </div>
                                        <div class="card-title">Pet Profile</div>
                                        <div class="card-tools">
                                            <button class="btn btn-info btn-border btn-round btn-sm"
                                                onclick="printDiv('printThis')">
                                                <i class="fa fa-print"></i>
                                                Print Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body m-5" id="printThis">
                                    <div class="d-flex flex-wrap justify-content-center"
                                        style="border-bottom:1px solid red">
                                        <div class="text-center">

                                            <h1 class="fw-bold mb-3">Pet Profile</h2>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="text-center p-1" style="border:1px solid red">
                                                <img src="<?= preg_match('/data:image/i', $resident['picture']) ? $resident['picture'] : 'assets/uploads/resident_profile/' . $resident['picture'] ?>"
                                                    alt="Resident Profile" class="img-fluid">
                                            </div>

                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            PET NAME:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <input type="text" class="form-control fw-bold" readonly
                                                            style="font-size:20px" value="<?= htmlspecialchars($resident['pet_name'], ENT_QUOTES, 'UTF-8') ?>">
                                                    </div>


                                                </div>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            PET TYPE:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <input type="text" class="form-control fw-bold"
                                                            style="font-size:20px" readonly
                                                            value="<?= htmlspecialchars($resident['pet_type'], ENT_QUOTES, 'UTF-8') ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-8 col-md-4 col-sm-4 mt-sm-2 text-left">BIRTHDATE:
                                                </h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= date('F d, Y', strtotime($resident['birthdate'])) ?> "
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">AGE:</h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['age'], ENT_QUOTES, 'UTF-8') ?> yrs. old" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">BREED:
                                                </h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['pet_breed'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">Gender:
                                                </h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['gender'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col">

                                        </div>
                                        <div class="col">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group row" style="visibility: hidden;">
                                            <h3 class="mt-5 col-lg-9 col-md-4 col-sm-4 mt-sm-2 text-left">OWNER NAME:
                                            </h3>
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-9 col-md-4 col-sm-4 mt-sm-2 text-left">OWNER
                                                    NAME:</h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['OwnerName'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-9 col-md-4 col-sm-4 mt-sm-2 text-left">CONTACT
                                                    NO:</h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['OwnerMobileNo'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">EMAIL:
                                                </h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <input type="text" class="form-control fw-bold" style="font-size:20px"
                                                    value="<?= htmlspecialchars($resident['OwnerEmail'], ENT_QUOTES, 'UTF-8') ?>" readonly>
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
                                                <h3 class="mt-5 col-lg-12 col-md-4 col-sm-4 mt-sm-2 text-left">OWNER
                                                    ADDRESS:</h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <textarea readonly class="form-control fw-bold" style="font-size:20px"
                                                    rows="3"><?= htmlspecialchars($resident['OwnerAddress'] . ' ' . $resident['OwnerCity'] . ' ' . $resident['OwnerZip'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group row">
                                                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">Remarks:
                                                </h3>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                <textarea readonly class="form-control fw-bold" style="font-size:20px"
                                                    rows="3"><?= htmlspecialchars($resident['pet_notes'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-head-row">
                                                <div class="card-title">Pet Records</div>
                                            </div>
                                        </div>
                                        <div class="card-body m-5">
                                            <!-- Loop through pet records -->
                                            <?php foreach ($petRecords as $record): ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>Date:
                                                            <?= htmlspecialchars($record['date'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>
                                                        <p>Service Type:
                                                            <?= htmlspecialchars($record['serviceTypes'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>

                                                    </div>
                                                    <!-- Additional fields from tblrecordservice can be displayed here -->
                                                </div>
                                                <hr>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-head-row">
                                                <div class="card-title">Pet Operation Records</div>
                                            </div>
                                        </div>
                                        <div class="card-body m-5">
                                            <!-- Loop through pet operations -->
                                            <?php foreach ($petOperations as $operation): ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>Date:
                                                            <?= htmlspecialchars($operation['date'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>
                                                        <p>Operation Type:
                                                            <?= htmlspecialchars($operation['operationType'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>
                                                        <p>Details:
                                                            <?= htmlspecialchars($operation['details'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>
                                                        <!-- Additional fields from tbloperation can be displayed here -->
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>Status:
                                                            <?= htmlspecialchars($operation['status'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>

                                                        <!-- Additional fields from tbloperation can be displayed here -->
                                                    </div>
                                                </div>
                                                <hr>
                                            <?php endforeach; ?>
                                        </div>
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
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
</body>

</html>