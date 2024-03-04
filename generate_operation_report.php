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
$query = "SELECT * FROM tbloperation WHERE id='$id'";
$result = $conn->query($query);
$operation = $result->fetch_assoc();


// Fetch treatment_used from tbloperation
$treatment_used = $operation['treatment_used'];

// Split treatment_used by commas to get individual treatments
$treatments = explode(',', $treatment_used);

// Fetch machineName and machinePrice from tbltreatmentplan
$machinePrices = array();

foreach ($treatments as $treatment) {
    // Use LIKE clause to find matching entries in tbltreatmentplan
    $query = "SELECT machineName, machinePrice FROM tbltreatmentplan WHERE TRIM(machineName) LIKE TRIM('$treatment')";
    $result = $conn->query($query);

    // Fetch the first matching row
    $row = $result->fetch_assoc();

    // If a matching row is found, store machinePrice
    if ($row) {
        $machinePrices[$treatment] = $row['machinePrice'];
    } else {
        $machinePrices[$treatment] = 'N/A';
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Operation Report - Purrfect Clinic Information Management System</title>
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
                                <h2 class="text-white fw-bold">Generate Operation Report</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo $_SESSION['success']; ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>"
                                    role="alert">
                                    <?php echo $_SESSION['message']; ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div>
                                            <button class="btn btn-light" onClick="document.location.href='operation.php'"><i class="fa fa-arrow-left"></i></button>
                                        </div>
                                        <div class="card-title">Operation Report</div>
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
                                            <h1 class="fw-bold mb-0">Purffect Clinic</i></h2>
                                                <p><i>Mobile No. 092040110198</i></p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-9 mx-auto"> <!-- Added mx-auto class here -->
                                            
                                            <div class="text-center">
                                                <h1 class="mt-4 fw-bold mb-5">
                                                    <?php if ($operation['status'] == 'Cancelled'): ?>
                                                        OPERATION REPORT: CANCELLED
                                                    <?php elseif ($operation['status'] == 'On-going'): ?>
                                                        OPERATION REPORT: ON-GOING
                                                    <?php elseif ($operation['status'] == 'Finished'): ?>
                                                        OPERATION REPORT: FINISHED
                                                    <?php elseif ($operation['status'] == 'Scheduled'): ?>
                                                        OPERATION REPORT: SCHEDULED
                                                    <?php endif; ?>

                                                </h1>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">

                                                        <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                                                            Name of Pet:</h3>
                                                        <div class="col-lg-8 col-md-8 col-sm-8"
                                                            style="border-bottom:1px solid black; margin:0 !important">
                                                            <?php

                                                            $petID = $operation['petID'];


                                                            $petQuery = "SELECT pet_name FROM tblpet WHERE id = '$petID'";
                                                            $petResult = $conn->query($petQuery);
                                                            $pet = $petResult->fetch_assoc();


                                                            ?>
                                                            <span class="fw-bold" style="font-size:20px;">
                                                                <?= ucwords($pet['pet_name']) ?>
                                                            </span>

                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                                                            File Time:</h3>
                                                        <div class="col-lg-8 col-md-8 col-sm-8"
                                                            style="border-bottom:1px solid black">
                                                            <span class="fw-bold" style="font-size:20px">
                                                                <?= date('h:i:s A', strtotime($operation['time'])) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                                                            File Date:</h3>
                                                        <div class="col-lg-8 col-md-8 col-sm-8"
                                                            style="border-bottom:1px solid black">
                                                            <span class="fw-bold" style="font-size:20px">
                                                                <?= date('F d, Y', strtotime($operation['date'])) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                                                            Operation Type:</h3>
                                                        <div class="col-lg-8 col-md-8 col-sm-8"
                                                            style="border-bottom:1px solid black">
                                                            <span class="fw-bold" style="font-size:20px">
                                                                <?= ucwords($operation['operationType']) ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                                                            Name of Owner:</h3>
                                                        <div class="col-lg-8 col-md-8 col-sm-8"
                                                            style="border-bottom:1px solid black; margin:0 !important">
                                                            <?php

                                                            // Fetch OwnerID from tbloperation
                                                            $ownerID = $operation['OwnerID'];

                                                            // Fetch OwnerName from tblowner using OwnerID
                                                            $ownerQuery = "SELECT OwnerName FROM tblowner WHERE OwnerID = '$ownerID'";
                                                            $ownerResult = $conn->query($ownerQuery);
                                                            $owner = $ownerResult->fetch_assoc();

                                                            // Display OwnerName
                                                            ?>
                                                            <span class="fw-bold" style="font-size:20px;">
                                                                <?= ucwords($owner['OwnerName']) ?>
                                                            </span>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                                                            Operation Details:</h3>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                                                        <textarea class="form-control fw-bold" style="font-size:10px"
                                                            rows="8"><?= ucwords(trim($operation['details'])) ?>  </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <h3 class="mt-5">Treatment Plan:</h3>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Machine Name</th>
                                                                <th>Machine Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($treatments as $treatment): ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $treatment ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= $machinePrices[$treatment] ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <?php if ($operation['status'] == 'On-going'): ?>
    <div class="row">
        <div class="col">
            <div class="form-group row">
                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                    Medimat Used:
                </h3>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Item Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $medimatUsed = explode(', ', $operation['medimat_used']);

                        foreach ($medimatUsed as $itemWithQuantity) {
                            // Split the item name and quantity using ":"
                            $itemData = explode(':', $itemWithQuantity);

                            // Check if the array has at least two elements
                            if (count($itemData) >= 2) {
                                $itemName = $itemData[0];
                                $quantity = $itemData[1];

                                // Fetch itemPrice from tblmedi_mat based on itemName
                                $query = "SELECT itemPrice FROM tblmedi_mat WHERE itemName = '$itemName'";
                                $result = $conn->query($query);

                                echo "<tr>";

                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $itemPrice = $row['itemPrice'];

                                    echo "<td class='fw-bold'>$itemName</td>";
                                    echo "<td class='fw-bold'>$quantity</td>";
                                    echo "<td class='fw-bold'>$itemPrice</td>";
                                } else {
                                    echo "<td class='fw-bold'>$itemName (Item not found)</td>";
                                    echo "<td class='fw-bold'>$quantity</td>";
                                    echo "<td class='fw-bold'>N/A</td>";
                                }

                                echo "</tr>";
                            } else {
                                // Handle the case where the array doesn't have enough elements
                                echo "<tr><td colspan='3'>Empty: $itemWithQuantity</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>





                                            
                                            <?php if ($operation['status'] != 'On-going' && $operation['finishDate'] !== null && $operation['finishTime'] !== null): ?>
    <div class="text-center">
        <h1 class="mt-4 fw-bold mb-5">
            <?php if ($operation['status'] == 'Cancelled'): ?>
                OPERATION REPORT: CANCELLED
            <?php else: ?>
                OPERATION REPORT: FINISHED
            <?php endif; ?>
        </h1>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group row">
                <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                    <?php if ($operation['status'] == 'Cancelled'): ?>
                        Operation Cancelled Time:
                    <?php else: ?>
                        Operation Finished Time:
                    <?php endif; ?>
                </h3>
                <div class="col-lg-8 col-md-8 col-sm-8" style="border-bottom:1px solid black">
                    <span class="fw-bold" style="font-size:20px">
                        <?= date('h:i:s A', strtotime($operation['finishTime'])) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group row">
                <h3 class="mt-5 col-lg-5 col-md-5 col-sm-5 mt-sm-2 text-left">
                    <?php if ($operation['status'] != 'Cancelled'): ?>
                        Operation Finished Date:
                    <?php else: ?>
                        Operation Cancelled Date:
                    <?php endif; ?>
                </h3>
                <div class="col-lg-8 col-md-8 col-sm-8" style="border-bottom:1px solid black">
                    <span class="fw-bold" style="font-size:20px">
                        <?= date('F d, Y', strtotime($operation['finishDate'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group row">
                <h3 class="mt-5 col-lg-4 col-md-4 col-sm-4 mt-sm-2 text-left">
                    <?php if ($operation['status'] == 'Cancelled'): ?>
                        Operation Cancelled Details:
                    <?php else: ?>
                        Operation Finished Details:
                    <?php endif; ?>
                </h3>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                <textarea class="form-control fw-bold" style="font-size:10px" rows="8">
                    <?= ucwords(trim($operation['finishDetails'])) ?>  
                </textarea>
            </div>
        </div>
    </div>
<?php endif; ?>


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