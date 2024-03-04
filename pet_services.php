<?php
include 'server/server.php';

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        exit("Invalid CSRF token");
    }
}

$query = "SELECT * FROM tblrecordservice";
$result = $conn->query($query);

$permit = array();
while ($row = $result->fetch_assoc()) {
    $permit[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php' ?>
    <title>Purrfect Clinic Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
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
                                <h2 class="text-white fw-bold">Purrfect Services</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success']); ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['message']); ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif ?>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">Service Status</div>
                                        <?php if (isset($_SESSION['username'])): ?>
                                            <div class="card-tools">
                                                <a href="#add" data-toggle="modal" class="btn btn-info btn-border btn-round btn-sm">
                                                    <i class="fa fa-plus"></i>
                                                    Pet Service
                                                </a>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="servicetable" class="display table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Name of Owner</th>
                                                    <th scope="col">Name of Pet</th>
                                                    <th scope="col">Service Applied</th>
                                                    <th scope="col">Total Cost</th>
                                                    <th scope="col">Date Applied</th>
                                                    <?php if (isset($_SESSION['username'])): ?>
                                                        <th scope="col">Action</th>
                                                    <?php endif ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($permit)): ?>
                                                    <?php foreach ($permit as $row): ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                $OwnerID = $row['OwnerID'];
                                                                $ownerQuery = $conn->prepare("SELECT OwnerName FROM tblowner WHERE OwnerID = ?");
                                                                $ownerQuery->bind_param('i', $OwnerID);
                                                                $ownerQuery->execute();
                                                                $ownerResult = $ownerQuery->get_result();

                                                                if ($ownerResult && $ownerData = $ownerResult->fetch_assoc()) {
                                                                    echo htmlspecialchars(ucwords($ownerData['OwnerName']));
                                                                } else {
                                                                    echo "Owner Not Found";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $petID = $row['petID'];
                                                                $petQuery = $conn->prepare("SELECT pet_name FROM tblpet WHERE id = ?");
                                                                $petQuery->bind_param('i', $petID);
                                                                $petQuery->execute();
                                                                $petResult = $petQuery->get_result();

                                                                if ($petResult && $petData = $petResult->fetch_assoc()) {
                                                                    echo htmlspecialchars(ucwords($petData['pet_name']));
                                                                } else {
                                                                    echo "Pet Not Found";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['serviceTypes']) ?></td>
                                                            <td>
                                                                <?= htmlspecialchars($row['totalCost']) ?>
                                                                <br>
                                                                <?php if ($row['paid'] == 'true'): ?>
                                                                    <span class="badge badge-success">Paid</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-danger">Unpaid</span>
                                                                <?php endif ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['date']) ?></td>
                                                            <?php if (isset($_SESSION['username'])): ?>
                                                                <td>
                                                                    <div class="form-button-action">
                                                                        <a type="button" data-toggle="tooltip" href="generate_business_permit.php?id=<?= htmlspecialchars($row['recordID']) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-link btn-primary" data-original-title="Generate Permit">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </a>
                                                                        <?php if (isset($_SESSION['username']) && $_SESSION['role'] == 'administrator'): ?>
                                                                            <a type="button" data-toggle="tooltip" href="model/remove_servrec.php?recordID=<?= htmlspecialchars($row['recordID']) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Are you sure you want to delete this service record?');" class="btn btn-link btn-danger" data-original-title="Remove">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>
                                                                        <?php endif ?>
                                                                    </div>
                                                                </td>
                                                            <?php endif ?>
                                                        </tr>
                                                    <?php endforeach ?>
                                                <?php endif ?>
                                            </tbody>
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

            <!-- Modal -->
            <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Dog Service Information</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/save_servrec.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <div class="form-group">
                                    <label for="petOwners">Pet Owner</label>
                                    <select name="petOwner" id="petOwners" class="form-control input-sm" data-live-search="true" style="width:100%">
                                        <option disabled selected>Select Pet Owner</option>
                                        <?php
                                        $qc = mysqli_query($conn, "SELECT * from tblowner");
                                        while ($rowc = mysqli_fetch_array($qc)) {
                                            echo '<option data-ownerid="' . htmlspecialchars($rowc['OwnerID']) . '">' . htmlspecialchars($rowc['OwnerName']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <input hidden name="petOwnerID" id="petOwnerID">
                                </div>

                                <div class="form-group">
                                    <label>Pet Name</label>
                                    <select name="petName" id="petNames" class="form-control input-sm" data-live-search="true" style="width:100%">
                                    </select>
                                    <input hidden name="petNameID" id="petNameID">
                                </div>

                                <div class="form-group">
                                    <label>Service Type</label>
                                    <select name="serviceType[]" id="serviceTypes" class="form-control input-sm" multiple data-live-search="true" style="width:100%">
                                        <?php
                                        $qc = mysqli_query($conn, "SELECT * from tblservices");
                                        while ($rowc = mysqli_fetch_array($qc)) {
                                            echo '<option>' . htmlspecialchars($rowc['serviceName']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" class="form-control" name="date" value="<?= htmlspecialchars(date('Y-m-d')); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Total Price</label>
                                    <input type="text" id="totalCost" class="form-control" readonly>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <select hidden="hidden" class="form-control" name="paid">
                                <option disabled selected>false</option>
                                <option value="false" selected>false</option>
                            </select>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'templates/footer.php' ?>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>

    <?php
    // Fetch service names and costs from the database
    $serviceCostsQuery = "SELECT serviceName, servicePrice FROM tblservices";
    $serviceCostsResult = $conn->query($serviceCostsQuery);

    // Create a JavaScript array containing service names and their costs
    $serviceCostsArray = array();
    while ($row = $serviceCostsResult->fetch_assoc()) {
        $serviceCostsArray[$row['serviceName']] = $row['servicePrice'];
    }
    ?>


    <script>
        $(document).ready(function () {
            // Function to fetch and set the pet ID based on the selected pet name
            function updatePetID() {
                var selectedPetNames = $('#petNames').val();
                var petID = [];

                // Check if at least one pet name is selected
                if (selectedPetNames && (typeof selectedPetNames === 'string' || selectedPetNames.length > 0)) {
                    // Convert to an array if only one option is selected
                    selectedPetNames = (typeof selectedPetNames === 'string') ? [selectedPetNames] : selectedPetNames;

                    // Iterate through selected pet names and fetch their IDs
                    selectedPetNames.forEach(function (petName) {
                        var petNameOption = $('#petNames option:contains(' + petName + ')');
                        var petIDValue = petNameOption.data('petid');
                        petID.push(petIDValue);
                    });
                }

                // Update the hidden input with the selected pet IDs
                $('#petNameID').val(petID.join(',')); // Join only selected pet IDs with a comma
            }

            $('#petOwners').on('change', function () {
                var ownerID = $(this).find(':selected').data('ownerid');
                $('#petOwnerID').val(ownerID);

                // Fetch associated pet names based on the selected pet owner using AJAX
                $.ajax({
                    url: 'get_pet_names.php',
                    type: 'POST',
                    data: { ownerID: ownerID },
                    success: function (response) {
                        $('#petNames').html(response);
                        $('#petNames').selectpicker('refresh'); // Refresh the Bootstrap Selectpicker
                        updatePetID(); // Call the function to update pet IDs
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });

            // Event listener for changes in selected pet names
            $('#petNames').on('change', function () {
                updatePetID(); // Call the function to update pet IDs

                // Additional check to clear pet ID if no pet name is selected
                if (!$(this).val()) {
                    $('#petNameID').val('');
                }
            });

            $('#servicetable').DataTable();

            $("#petOwners").selectpicker();
            $("#petNames").selectpicker();
            $("#serviceTypes").selectpicker();
        });



        // Define a JavaScript object with service names and their costs
        var serviceCosts = <?php echo json_encode($serviceCostsArray); ?>;

        // Function to calculate and update the total cost
        function updateTotalCost() {
            var selectedServiceNames = $('#serviceTypes').val();
            var totalCost = 0;

            // Iterate through selected serviceNames and calculate total cost
            for (var i = 0; i < selectedServiceNames.length; i++) {
                // Use the serviceCosts object to get the cost for each selected service
                var serviceName = selectedServiceNames[i];
                totalCost += parseFloat(serviceCosts[serviceName]) || 0; // Use 0 if the service name is not found
            }

            // Update the totalCost input field
            $('#totalCost').val(totalCost.toFixed(2)); // Round to 2 decimal places
        }

        // Event listener for changes in selected services
        $('#serviceTypes').on('change', function () {
            updateTotalCost();
        });

        // Call the function initially to set the initial total cost
        updateTotalCost();

    </script>
</body>
</html>
