<?php include 'server/server.php' ?>
<?php

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

$query = "SELECT tblowner.*, tblpet.pet_name
FROM tblowner
LEFT JOIN tblpet ON tblowner.OwnerID = tblpet.OwnerID";
$result = $conn->query($query);

$ownerPets = array();
while ($row = $result->fetch_assoc()) {
    $ownerPets[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Pet Owner</title>
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
                                <h2 class="text-white fw-bold">Pet Owner</h2>
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
                                        <div class="card-title">Pet Information</div>
                                        <?php if (isset($_SESSION['username'])): ?>
                                            <div class="card-tools">
                                                <a href="#add" data-toggle="modal"
                                                    class="btn btn-info btn-border btn-round btn-sm">
                                                    <i class="fa fa-plus"></i>
                                                    Add pet
                                                </a>
                                                <a href="model/export_resident_csv.php"
                                                    class="btn btn-danger btn-border btn-round btn-sm">
                                                    <i class="fa fa-file"></i>
                                                    Export CSV
                                                </a>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="residenttable" class="display table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Owner ID</th>
                                                    <th scope="col">Owner Name</th>
                                                    <th scope="col">Owner Mobile</th>
                                                    <th scope="col">Pets</th>
                                                    <?php if (isset($_SESSION['username'])): ?>
                                                        <?php if ($_SESSION['role'] == 'administrator'): ?>
                                                            <!-- Additional columns for administrator -->
                                                        <?php endif ?>
                                                        <th scope="col">Action</th>
                                                    <?php endif ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($ownerPets)): ?>
                                                    <?php $owners = array();
                                                    foreach ($ownerPets as $row): ?>
                                                        <?php $ownerID = htmlspecialchars($row['OwnerID'], ENT_QUOTES); ?>
                                                        <?php if (!isset($owners[$ownerID])): ?>
                                                            <tr>
                                                                <td>
                                                                    <?= htmlspecialchars($row['OwnerID'], ENT_QUOTES); ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($row['OwnerName'], ENT_QUOTES); ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($row['OwnerMobileNo'], ENT_QUOTES); ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $petNames = array();
                                                                    foreach ($ownerPets as $ownerPet) {
                                                                        if ($ownerPet['OwnerID'] === $ownerID) {
                                                                            $petNames[] = htmlspecialchars($ownerPet['pet_name'], ENT_QUOTES);
                                                                        }
                                                                    }
                                                                    echo implode(", ", $petNames);
                                                                    ?>
                                                                </td>
                                                                <?php if (isset($_SESSION['username'])): ?>
                                                                    <?php if ($_SESSION['role'] == 'administrator'): ?>
                                                                        <!-- Additional columns for administrator -->
                                                                    <?php endif ?>
                                                                    <td>
                                                                        <div class="form-button-action">

                                                                            <a type="button" data-toggle="tooltip"
                                                                                href="generate_owner_info.php?id=<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                                                                                class="btn btn-link btn-info"
                                                                                data-original-title="Generate">
                                                                                <i class="fa fa-file"></i>
                                                                            </a>



                                                                            <?php if (isset($_SESSION['username']) && $_SESSION['role'] == 'administrator'): ?>

                                                                                <a type="button" href="#edit" data-toggle="modal"
                                                                                    class="btn btn-link btn-primary edit-btn"
                                                                                    title="View Owners" onclick="editOwner(this)"
                                                                                    data-ownerid="<?= $row['OwnerID'] ?>"
                                                                                    data-OwnerID="<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>"
                                                                                    data-onumber="<?= htmlspecialchars($row['OwnerMobileNo'], ENT_QUOTES) ?>"
                                                                                    data-zcode="<?= htmlspecialchars($row['OwnerZip'], ENT_QUOTES) ?>"
                                                                                    data-oplace="<?= htmlspecialchars($row['OwnerAddress'], ENT_QUOTES) ?>"
                                                                                    data-ocity="<?= htmlspecialchars($row['OwnerCity'], ENT_QUOTES) ?>"
                                                                                    data-oname="<?= htmlspecialchars($row['OwnerName'], ENT_QUOTES) ?>"
                                                                                    data-email="<?= htmlspecialchars($row['OwnerEmail'], ENT_QUOTES) ?>">

                                                                                    <?php if (isset($_SESSION['username'])): ?>
                                                                                        <i class="fa fa-edit"></i>
                                                                                    <?php else: ?>
                                                                                        <i class="fa fa-eye"></i>
                                                                                    <?php endif ?>
                                                                                </a>


                                                                                <a type="button" data-toggle="tooltip"
                                                                                    href="model/remove_owner.php?OwnerID=<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                                                                                    onclick="return confirm('Are you sure you want to delete this resident?');"
                                                                                    class="btn btn-link btn-danger"
                                                                                    data-original-title="Remove">
                                                                                    <i class="fa fa-times"></i>
                                                                                </a>
                                                                            <?php endif ?>
                                                                        </div>
                                                                    </td>
                                                                <?php endif ?>
                                                            </tr>
                                                            <?php $owners[$ownerID] = true; ?>
                                                        <?php endif ?>
                                                    <?php endforeach ?>
                                                <?php endif ?>
                                            </tbody>
                                        </table>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Main Footer -->

                    <!-- End Main Footer -->

                </div>
                <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Manage Pet Owners</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="model/save_pet.php" enctype="multipart/form-data">
                                    <!-- CSRF Token -->
                                    <input name="csrf_token" hidden
                                        value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pet Owner</label>
                                                <select name="oname1" id="oname1" class="form-control input-sm"
                                                    data-live-search="true" style="width:100%">
                                                    <?php
                                                    $qc = mysqli_query($conn, "SELECT DISTINCT tblowner.OwnerID, tblowner.OwnerName, tblowner.OwnerCity, tblowner.OwnerZip, tblowner.OwnerEmail, tblowner.OwnerMobileNo, tblowner.OwnerAddress FROM tblowner");

                                                    echo '<option disabled selected>Select Pet Owner</option>';
                                                    while ($rowc = mysqli_fetch_assoc($qc)) {
                                                        echo '<option data-city="' . htmlspecialchars($rowc['OwnerCity'], ENT_QUOTES) . '" data-zip="' . htmlspecialchars($rowc['OwnerZip'], ENT_QUOTES) . '" data-email="' . htmlspecialchars($rowc['OwnerEmail'], ENT_QUOTES) . '" data-mobile="' . htmlspecialchars($rowc['OwnerMobileNo'], ENT_QUOTES) . '" data-address="' . htmlspecialchars($rowc['OwnerAddress'], ENT_QUOTES) . '" value="' . htmlspecialchars($rowc['OwnerID'], ENT_QUOTES) . '">' . htmlspecialchars($rowc['OwnerName'], ENT_QUOTES) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's Address</label>
                                                <input type="text" class="form-control" placeholder="Enter Owner's City"
                                                    id="owneraddress" name="owneraddress" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's ID</label>
                                                <input type="text" class="form-control" placeholder="Enter Owner's ID"
                                                    id="ownerID" name="ownerID" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's Zip</label>
                                                <input type="text" class="form-control" placeholder="Enter Owner's Zip"
                                                    id="ownerzip" name="ownerzip" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's Mobile Number</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter Owner's Mobile Number" id="ownermobile"
                                                    name="ownermobile" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's Email</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter Owner's Email" id="owneremail" name="owneremail"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label>Owner's City</label>
                                                <input type="text" class="form-control" placeholder="Enter Owner's City"
                                                    id="ownercity" name="ownercity" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="pname">Pet's Name</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Pet's Name" id="pname" name="pname" required>
                                                </div>
                                                <label for="ptype">Pet Type</label>
                                                <input type="text" class="form-control" placeholder="Enter Pet's Type"
                                                    id="ptype" name="ptype" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="bname">Pet Breed</label>
                                                <input type="text" class="form-control" placeholder="Enter Pet Breed"
                                                    id="bname" name="bname" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="bdate">Birthdate</label>
                                                <input placeholder="Enter Pet Birthdate" type="date"
                                                    class="form-control" name="bdate" value="<?= date('Y-m-d'); ?>"
                                                    required>
                                            </div>


                                            <div class="form-group">
                                                <label for="age">Pet Age</label>
                                                <input type="text" class="form-control" placeholder="Enter Pet Age"
                                                    name="age" id="age" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="pgender">Pet Gender</label>
                                                <select class="form-control" name="pgender" id="pgender" required>
                                                    <option disabled selected>Select Pet Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Asexual">Asexual</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="img">Pet Profile Image</label>
                                                <input type="file" class="form-control-file" id="img" name="img">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="pnotes">Pet Notes</label>
                                        <textarea class="form-control" placeholder="Enter Pet Remarks here..."
                                            name="pnotes" id="pnotes" required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Owner Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="model/edit_owner.php" enctype="multipart/form-data">
                                    <!-- CSRF Token -->
                                    <input name="csrf_token" hidden
                                        value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="size" value="1000000">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Owner's Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Owner's Name"
                                                    id="oname" name="oname" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Contact Number</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter Contact Number" id="onumber" name="onumber"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Address</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter email address" id="email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Owner's Address</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter Address" id="oplace" name="oplace"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Owner's Zip Code</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter Zip Code" id="zcode" name="zcode"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Owner's City</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter City" id="ocity" name="ocity"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group" style="display: none;">
                                                            <label>Owner ID</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter Owner ID" id="owner_id"
                                                                name="OwnerID" required readonly
                                                                value="<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <?php if (isset($_SESSION['username'])): ?>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        <?php endif ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php include 'templates/footer.php' ?>
            <script src="assets/js/plugin/datatables/datatables.min.js"></script>
            <script>
                $(document).on('click', '.edit-btn', function () {
                    var ownerID = $(this).data('ownerid');
                    if (ownerID) {
                        $("#owner_id").val(ownerID);
                    } else {
                        // Handle the case when data-ownerid is not set or has no value
                        console.log("No ownerID value found.");
                    }
                });
                $(document).ready(function () {
                    var table = $('#residenttable').DataTable();

                    // Add a search bar


                    // Apply the search functionality
                    $('.search-bar input').on('keyup', function () {
                        table.search(this.value).draw();
                    });
                });
                // Get the selected option's data attributes and populate the corresponding fields
                document.getElementById("oname1").addEventListener("change", function () {
                    var selectedOption = this.options[this.selectedIndex];
                    document.getElementById("ownerID").value = selectedOption.value;
                    document.getElementById("ownercity").value = selectedOption.getAttribute("data-city");
                    document.getElementById("ownerzip").value = selectedOption.getAttribute("data-zip");
                    document.getElementById("owneremail").value = selectedOption.getAttribute("data-email");
                    document.getElementById("ownermobile").value = selectedOption.getAttribute("data-mobile");
                    document.getElementById("owneraddress").value = selectedOption.getAttribute("data-address");
                });
            </script>
</body>

</html>