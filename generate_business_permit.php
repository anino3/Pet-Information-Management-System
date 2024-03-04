<?php include 'server/server.php' ?>
<?php
// Get record ID from the query string
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Validate and sanitize the input
if (!ctype_digit($id)) {
    exit("Invalid record ID");
}


// Use prepared statements to prevent SQL injection
$query = "SELECT * FROM tblrecordservice WHERE recordID=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$permit = $result->fetch_assoc();
$stmt->close();

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid CSRF token. Please try again.";
        $_SESSION['success'] = "danger"; // You can define different types of messages (success, warning, danger, etc.)
        header("Location: pet)services.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Business Receipt - Purrfect Clinic Management System</title>
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
                                <h2 class="text-white fw-bold">Generate Receipt</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">

                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success']); ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>"
                                    role="alert">
                                    <?php echo htmlspecialchars($_SESSION['message']); ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                    <div>
                                            <button class="btn btn-light" onClick="document.location.href='pet_services.php'"><i class="fa fa-arrow-left"></i></button>
                                        </div>
                                        <div class="card-title">Purrfect Clinic Receipt</div>
                                        <div class="card-tools">
                                            <button class="btn btn-info btn-border btn-round btn-sm"
                                                onclick="printDiv('printThis')">
                                                <i class="fa fa-print"></i>
                                                Print Receipt
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body m-5" id="printThis">
                                    <div class="d-flex flex-wrap justify-content-around"
                                        style="border-bottom:1px solid red">

                                        <div class="text-center">
                                            <h3 class="mb-0">Republic of the Philippines</h3>
                                            <h3 class="mb-0">Province of Davao del Sur</h3>
                                            <h3 class="mb-0">Davao City</h3>
                                            <h1 class="fw-bold mb-0">Planet Nemek</h2>
                                                <p><i>Mobile No. 09475658097</i></p>
                                        </div>

                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <div class="text-center mt-5">
                                                <h1 class="mt-4 fw-bold"><u>OFFICE OF PURRFECT CLINIC</u></h1>
                                            </div>
                                            <div class="text-center">
                                                <h1 class="mt-4 fw-bold mb-5" style="font-size:38px;color:darkblue">THIS
                                                    RECEIPT IS</h1>
                                            </div>
                                            <h2 class="mt-5 fw-bold">GRANTED TO:</h2>
                                            <div class="text-center pt-4">
                                                <?php
                                                // Fetch owner's name from tblowner using OwnerID from tblrecordservice
                                                $ownerId = $permit['OwnerID'];
                                                $ownerQuery = "SELECT OwnerName FROM tblowner WHERE OwnerID = ?";
                                                $stmtOwner = $conn->prepare($ownerQuery);
                                                $stmtOwner->bind_param("i", $ownerId);
                                                $stmtOwner->execute();
                                                $resultOwner = $stmtOwner->get_result();
                                                $owner = $resultOwner->fetch_assoc();
                                                $ownerName = htmlspecialchars(ucfirst($owner['OwnerName']));
                                                $stmtOwner->close();
                                                ?>
                                                <h1 class="mt-4 fw-bold mb-0">
                                                    <?= $ownerName ?>
                                                </h1>
                                                <hr class="w-50 mt-0 mb-0" style="border-top: 2px solid black;">
                                                <h2 class="mt-0">FULL NAME</h2>
                                            </div>

                                            <div class="text-center pt-4 mb-5">
                                                <h1 class="mt-4 fw-bold mb-0">
                                                    <?= htmlspecialchars(ucfirst($permit['serviceTypes'])) ?>
                                                </h1>
                                                <hr class="w-50 mt-0 mb-0" style="border-top: 2px solid black;">
                                                <h2 class="mt-0">SERVICE AVAILED</h2>
                                            </div>

                                            <h2 class="mt-5">Given this <span class="fw-bold" style="font-size:20px">
                                                    <?= date('m/d/Y') ?>
                                                </span> at <span style="font-size:20px">Purffect Clinic, Planet
                                                    Nemek</span>.</h2>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="p-3 text-right mr-5" style="margin-top:120px">
                                                <h1 class="fw-bold mb-0 text-uppercase">James Reid</h1>
                                                <p class="mr-5">Veterinarian Staff</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-4">
                                            <h4 class="mb-0"><i>CTC No.</i>:_____________</h4>
                                            <h4 class="mb-0"><i>Issued On.</i>:_____________</h4>
                                            <h4 class="mb-0"><i>Issued at.</i>: Purffect Clinic, City of Davao, Davao
                                                del Sur, Philippines</h4>
                                            <h4 class="mb-0"><i>OR No.</i>:_____________</h4>
                                        </div>
                                        <p class="ml-3"><i>(This receipt, while in force, shall be posted in a
                                                conspicuous place in the business premises.)</i></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="pment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create Payment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/save_pment.php">
                               
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" class="form-control" name="amount"
                                        placeholder="Enter amount to pay" required>
                                </div>
                                <div class="form-group">
                                    <label>Date Issued</label>
                                    <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Payment Details(Optional)</label>
                                    <textarea class="form-control" placeholder="Enter Payment Details"
                                        name="details"><?= htmlspecialchars(ucfirst($permit['serviceTypes'])) ?></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <input hidden class="form-control" name="OwnerID" value="<?= htmlspecialchars($permit['OwnerID']) ?>">
                            <input hidden class="form-control" name="recordID" value="<?= htmlspecialchars($permit['recordID']) ?>">
                            <select hidden="hidden" class="form-control" name="paid">
                                <option disabled selected>Select Paid Status</option>
                                <option value="true" selected>true</option>
                            </select>
                            <button type="button" class="btn btn-secondary" onclick="goBack()">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Footer -->
            <?php include 'templates/main-footer.php' ?>
            <!-- End Main Footer -->
            <?php if (!isset($_GET['closeModal'])) { ?>

                <script>
                    setTimeout(function () { openModal(); }, 1000);
                </script>
            <?php } ?>
        </div>

    </div>
    <?php include 'templates/footer.php' ?>
    <script>
        function openModal() {
            $('#pment').modal('show');
        }
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
