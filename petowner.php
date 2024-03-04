<?php include 'server/server.php' ?>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure' => true,
        'httponly' => true,
    ]);
    session_start();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';


$query = "SELECT * FROM tblpet INNER JOIN tblowner ON tblpet.OwnerID = tblowner.OwnerID";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$pet = array();
while ($row = $result->fetch_assoc()) {
    $pet[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Pet Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
                                <h2 class="text-white fw-bold">Pets</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES); ?> <?= htmlspecialchars($_SESSION['success']) == 'danger' ? 'bg-danger text-light' : null ?>"
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
                                                    Add Pet
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
                                                    <th scope="col">Pet ID</th>
                                                    <th scope="col">Pet Name</th>
                                                    <th scope="col">Pet Type</th>
                                                    <th scope="col">Pet Breed</th>
                                                    <th scope="col">Pet Owner</th>

                                                    <?php if (isset($_SESSION['username'])): ?>
                                                        <?php if ($_SESSION['role'] == 'administrator'): ?>
                                                            <!-- Additional columns for administrator -->
                                                        <?php endif ?>
                                                        <th scope="col">Action</th>
                                                    <?php endif ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($pet)): ?>
                                                    <?php $no = 1;
                                                    foreach ($pet as $row): ?>
                                                        <tr>
                                                            <td>
                                                                <?= htmlspecialchars($row['id'], ENT_QUOTES); ?>
                                                            </td>
                                                            <td>
                                                                <div class="avatar avatar-m">
                                                                    <img src="<?= preg_match('/data:image/i', $row['picture']) ? htmlspecialchars($row['picture'], ENT_QUOTES) : 'assets/uploads/resident_profile/' . htmlspecialchars($row['picture'], ENT_QUOTES), '.' ?>"
                                                                        alt="Resident Profile"
                                                                        class="avatar-img rounded-circle">
                                                                </div>
                                                                <?= ucwords(htmlspecialchars($row['pet_name'], ENT_QUOTES) . ' ') ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($row['pet_type'], ENT_QUOTES); ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($row['pet_breed'], ENT_QUOTES); ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($row['OwnerName'], ENT_QUOTES); ?>
                                                            </td>

                                                            <?php if (isset($_SESSION['username'])): ?>
                                                                <?php if ($_SESSION['role'] == 'administrator'): ?>
                                                                    <!-- Additional columns for administrator -->
                                                                <?php endif ?>
                                                                <td>
                                                                    <div class="form-button-action">
                                                                        
                                                                        
                                                                            <a type="button" data-toggle="tooltip"
                                                                                href="generate_pet.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                                                                                class="btn btn-link btn-info"
                                                                                data-original-title="View">
                                                                                <i class="fa fa-file"></i>
                                                                            </a>
                                                                            <?php if (isset($_SESSION['username']) && $_SESSION['role'] == 'administrator'): ?>
                                                                            <a type="button" href="#edit" data-toggle="modal"
                                                                            class="btn btn-link btn-primary"
                                                                            title="Edit Pet Information"
                                                                            onclick="editResident(this)"
                                                                            data-id="<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>"
                                                                            data-ownerid="<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>"
                                                                            data-bname="<?= htmlspecialchars($row['pet_breed'], ENT_QUOTES) ?>"
                                                                            data-pname="<?= htmlspecialchars($row['pet_name'], ENT_QUOTES) ?>"
                                                                            data-ptype="<?= htmlspecialchars($row['pet_type'], ENT_QUOTES) ?>"
                                                                            data-pgender="<?= htmlspecialchars($row['gender'], ENT_QUOTES) ?>"
                                                                            data-pnotes="<?= htmlspecialchars($row['pet_notes'], ENT_QUOTES) ?>"
                                                                            data-bdate="<?= htmlspecialchars($row['birthdate'], ENT_QUOTES) ?>"
                                                                            data-age="<?= htmlspecialchars($row['age'], ENT_QUOTES) ?>"
                                                                            data-OwnerID="<?= htmlspecialchars($row['OwnerID'], ENT_QUOTES) ?>"
                                                                            data-onumber="<?= htmlspecialchars($row['OwnerMobileNo'], ENT_QUOTES) ?>"
                                                                            data-zcode="<?= htmlspecialchars($row['OwnerZip'], ENT_QUOTES) ?>"
                                                                            data-oplace="<?= htmlspecialchars($row['OwnerAddress'], ENT_QUOTES) ?>"
                                                                            data-ocity="<?= htmlspecialchars($row['OwnerCity'], ENT_QUOTES) ?>"
                                                                            data-oname="<?= htmlspecialchars($row['OwnerName'], ENT_QUOTES) ?>"
                                                                            data-email="<?= htmlspecialchars($row['OwnerEmail'], ENT_QUOTES) ?>"
                                                                            data-img="<?= htmlspecialchars($row['picture'], ENT_QUOTES) ?>">
                                                                            <?php if (isset($_SESSION['username'])): ?>
                                                                                <i class="fa fa-edit"></i>
                                                                            <?php else: ?>
                                                                                <i class="fa fa-eye"></i>
                                                                            <?php endif ?>
                                                                        </a>

                                                                            <a type="button" data-toggle="tooltip"
                                                                                href="model/remove_pet.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                                                                                onclick="return confirm('Are you sure you want to delete this pet?');"
                                                                                class="btn btn-link btn-danger"
                                                                                data-original-title="Remove">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>

                                                                        <?php endif ?>
                                                                    </div>
                                                                </td>
                                                            <?php endif ?>
                                                        </tr>
                                                        <?php $no++; endforeach ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No Available Data</td>
                                                    </tr>
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

            <!-- Modal -->
            <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Pet & Owner Registration Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/save_pet.php" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input name="csrf_token" hidden
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="size" value="1000000">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div style="width: 370px; height: 250;" class="text-center" id="my_camera">
                                            <img src="assets/img/person.png" alt="..." class="img img-fluid"
                                                width="250">
                                        </div>
                                        <div class="form-group d-flex justify-content-center">
                                            <button type="button" class="btn btn-danger btn-sm mr-2" id="open_cam">Open
                                                Camera</button>
                                            <button type="button" class="btn btn-secondary btn-sm ml-2"
                                                onclick="save_photo()">Capture</button>
                                        </div>
                                        <div id="profileImage">
                                            <input type="hidden" name="profileimg">
                                        </div>
                                        <div class="form-group">
                                            <input type="file" class="form-control" name="img" accept="image/*">
                                        </div>


                                        <div class="form-group">
                                            <label>Pet's Name</label>
                                            <input type="text" class="form-control" placeholder="Enter pet name"
                                                name="pname" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Pet's Type</label>
                                            <input type="text" class="form-control" placeholder="Enter pet type"
                                                name="ptype" required>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Breed</label>
                                                    <input type="text" class="form-control" placeholder="Enter breed"
                                                        name="bname" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Owner's Name</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Owner's Name" name="oname" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Contact Number</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Contact Number" name="onumber">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Birthdate</label>
                                                    <input type="date" class="form-control"
                                                        placeholder="Enter Birthdate" name="bdate" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Owner's Address</label>
                                                    <input type="text" class="form-control" placeholder="Enter Address"
                                                        name="oplace" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Email Address</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter email address" name="email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Age</label>
                                                    <input type="number" class="form-control" placeholder="Enter Age"
                                                        min="1" name="age" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Owner's City</label>
                                                    <input type="text" class="form-control" placeholder="Enter City"
                                                        name="ocity" required>
                                                </div>


                                            </div>
                                            <div class="col">

                                                <div class="form-group" style="display: none;">
                                                    <label>National ID No.</label>
                                                    <input type="text" class="form-control" name="national"
                                                        placeholder="Enter National ID No."
                                                        value="<?php echo rand(100000, 999999); ?>" required>
                                                </div>
                                            </div>




                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Gender</label>
                                                    <select class="form-control" name="pgender">
                                                        <option disabled selected>Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Asexual">Asexual</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Owner's Zip Code</label>
                                                    <input type="text" class="form-control" placeholder="Enter Zip Code"
                                                        name="zcode" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group" style="visibility: hidden;">
                                                    <label>Is Active</label>
                                                    <select class="form-control" required name="isActive">
                                                        <option selected>Yes</option>
                                                        <option value="No">No</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">

                                            </div>
                                            <div class="col">

                                            </div>
                                            <div class="col">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Pet notes</label>
                                            <textarea class="form-control" name="pnotes" required
                                                placeholder="remarks"></textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Pet Information</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/edit_pet.php" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input name="csrf_token" hidden
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="size" value="1000000">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div id="my_camera1" style="width: 370px; height: 250;" class="text-center">
                                            <img src="assets/img/person.png" alt="..." class="img img-fluid" width="250"
                                                id="image">
                                        </div>
                                        <?php if (isset($_SESSION['username'])): ?>
                                            <div class="form-group d-flex justify-content-center">
                                                <button type="button" class="btn btn-danger btn-sm mr-2" id="open_cam1">Open
                                                    Camera</button>
                                                <button type="button" class="btn btn-secondary btn-sm ml-2"
                                                    onclick="save_photo1()">Capture</button>
                                            </div>
                                            <div id="profileImage1">
                                                <input type="hidden" name="profileimg">
                                            </div>
                                            <div class="form-group">
                                                <input type="file" class="form-control" name="img" accept="image/*">
                                            </div>
                                        <?php endif ?>


                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Name</label>
                                                    <input type="text" class="form-control" name="pname" id="pname"
                                                        placeholder="Enter Pet Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Pet's Breed</label>
                                                    <input type="text" class="form-control" placeholder="Enter breed"
                                                        id="bname" name="bname" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet Type</label>
                                                    <input type="text" class="form-control" name="ptype" id="ptype"
                                                        placeholder="Enter Pet Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Pet's Gender</label>
                                                    <select class="form-control" id="pgender" name="pgender">
                                                        <option disabled selected>Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Asexual">Asexual</option>
                                                    </select>
                                                </div>
                                                <div class="form-group" style="display: none;">
                                                    <label>Owner's Name</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Owner's Name" id="oname" name="oname"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group" style="display: none;">
                                                    <label>Contact Number</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Contact Number" id="onumber" name="onumber"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Birthdate</label>
                                                    <input type="date" class="form-control"
                                                        placeholder="Enter Birthdate" id="bdate" name="bdate" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group" style="display: none;">
                                                    <label>Owner's Address</label>
                                                    <input type="text" class="form-control" placeholder="Enter Address"
                                                        id="oplace" name="oplace" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group" style="display: none;">
                                                    <label>Email Address</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter email address" id="email" name="email"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet's Age</label>
                                                    <input type="number" class="form-control" placeholder="Enter Age"
                                                        min="1" name="age" id="age" required>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group" style="display: none;">
                                                    <label>Owner's City</label>
                                                    <input type="text" class="form-control" placeholder="Enter City"
                                                        id="ocity" name="ocity" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">

                                                    <div class="form-group" style="display: none;">
                                                        <label>Owner's Zip Code</label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter Zip Code" id="zcode" name="zcode"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group" style="display: none;">
                                                        <label>Owner's ID</label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter Zip Code" id="ownerid" name="ownerid"
                                                            required>
                                                    </div>
                                                </div>

                                            </div>


                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Pet Note</label>
                                                    <textarea class="form-control" name="pnotes"
                                                        placeholder="Enter note" id="pnotes"></textarea>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="id" id="res_id">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <?php if (isset($_SESSION['username'])): ?>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    <?php endif ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Main Footer -->
                <?php include 'templates/main-footer.php' ?>
                <!-- End Main Footer -->

            </div>

        </div>
        <?php include 'templates/footer.php' ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#residenttable').DataTable();
            });
        </script>
</body>

</html>