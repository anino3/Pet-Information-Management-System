<?php include 'server/server.php' ?>
<?php
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// Check if 'csrf_token' is set in the session
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';


$query = "SELECT * FROM tblmedi_mat"; // Update the SQL query to fetch data from tblmedi_mat
$result = $conn->query($query);

$medicines = array();
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'templates/header.php' ?>
    <title>Medicine Management - Purrfect Clinic Information Management System</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
                                <h2 class="text-white fw-bold">Medicone Records</h2>
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
                                        <div class="card-title">Medicine Management</div>
                                        <div class="card-tools">
                                            <?php if (isset($_SESSION['username'])): ?>
                                                <a href="#add" data-toggle="modal"
                                                    class="btn btn-info btn-border btn-round btn-sm">
                                                    <i class="fa fa-plus"></i>
                                                    Medicine
                                                </a>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="medicinetable" class="table table-striped ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Medicine ID</th>
                                                    <th scope="col">Item Code</th>
                                                    <th scope="col">Item Name</th>
                                                    <th scope="col">Quantity</th>

                                                    <th scope="col">Expiration Date</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($medicines)): ?>
                                                    <?php $no = 1;
                                                    foreach ($medicines as $row): ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row['medID'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row['itemCode'] ?>
                                                            </td>

                                                            <td>
                                                                <br>
                                                                <div class="avatar avatar-xs">
                                                                    <img src="<?= preg_match('/data:image/i', $row['itemImage']) ? $row['itemImage'] : 'assets/uploads/avatar/' . $row['itemImage'] ?>"
                                                                        alt="User Profile" class="avatar-img rounded"
                                                                        style="width: 50px; height: 50px;">
                                                                </div>
                                                                <br>
                                                                <br>
                                                                <?= $row['itemName'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row['quantity'] ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $expirationDate = $row['expirationDate'];

                                                                // Convert expiration date to timestamp
                                                                $expirationTimestamp = strtotime($expirationDate);

                                                                // Calculate the difference in days
                                                                $daysRemaining = floor(($expirationTimestamp - time()) / (60 * 60 * 24));

                                                                // Display expiration date
                                                                echo $expirationDate;

                                                                // Check if expiration is within a warning threshold (e.g., 30 days)
                                                                if ($daysRemaining <= 30) {
                                                                    echo ' <span class="text-danger">(Expires in ' . $daysRemaining . ' days)</span>';
                                                                }
                                                                ?>
                                                            </td>

                                                            <td>

                                                                <div class="form-button-action">


                                                                    <a type="button" data-toggle="tooltip" href="#editModal"
                                                                        class="btn btn-link btn-warning edit-btn"
                                                                        data-original-title="Edit"
                                                                        data-id="<?= $row['medID'] ?>"
                                                                        data-name="<?= $row['itemName'] ?>"
                                                                        data-type="<?= $row['typeName'] ?>"
                                                                        data-code="<?= $row['itemCode'] ?>"
                                                                        data-quantity="<?= $row['quantity'] ?>"
                                                                        data-price="<?= $row['itemPrice'] ?>"
                                                                        data-date="<?= $row['expirationDate'] ?>"
                                                                        data-image="<?= $row['itemImage'] ?>">
                                                                        <i class="fas fa-book"></i>
                                                                    </a>
                                                                    <a type="button" data-toggle="tooltip"
                                                                        href="model/remove_medicines.php?id=<?= $row['medID'] ?>"
                                                                        onclick="return confirm('Are you sure you want to delete this medicine?');"
                                                                        class="btn btn-link btn-danger"
                                                                        data-original-title="Remove">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>


                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php $no++; endforeach ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No Available Data</td>
                                                    </tr>
                                                <?php endif ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>

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

            <!-- Modal -->
            <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ADD MEDICINE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/save_medicines.php" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="size" value="1000000">
                                <div class="text-center">
                                    <div id="my_camera" style="height: 250;" class="text-center">
                                        <img src="assets/img/person.png" alt="..." class="img img-fluid" width="250">
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
                                </div>
                                <div class="form-group">
                                    <label>Medicine Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Name"
                                        name="itemName" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Type</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Type"
                                        name="itemType" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Code</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Code"
                                        name="itemCode" required>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Quantity"
                                        name="quantity" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Price</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Price"
                                        name="itemPrice" required>
                                </div>
                                <div class="form-group">
                                    <label>Expiration Date</label>
                                    <input type="date" class="form-control" name="date" value="<?= date('Y-m-d'); ?>"
                                        required>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal for Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">EDIT MEDICINE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="model/edit_medicines.php" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token"
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="size" value="1000000">
                                <input type="hidden" name="medID" id="editMedID">

                                <div class="text-center">
                                    <!-- Similar image capture and upload as the Add modal -->
                                    <!-- Adjust the following lines based on your requirements -->
                                    <div id="my_camera" style="height: 250;" class="text-center">
                                        <img src="assets/img/person.png" alt="..." class="img img-fluid" width="250">
                                    </div>
                                    <div class="form-group d-flex justify-content-center">
                                        <button type="button" class="btn btn-danger btn-sm mr-2" id="edit_open_cam">Open
                                            Camera</button>
                                        <button type="button" class="btn btn-secondary btn-sm ml-2"
                                            onclick="edit_save_photo()">Capture</button>
                                    </div>
                                    <div id="editProfileImage">
                                        <input type="hidden" name="editProfileimg">
                                    </div>
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="editImg" accept="image/*">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Medicine Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Name"
                                        name="editItemName" id="editItemName" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Type</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Type"
                                        name="editItemType" id="editItemType" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Code</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Code"
                                        name="editItemCode" id="editItemCode" required>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Quantity"
                                        name="editQuantity" id="editQuantity" required>
                                </div>
                                <div class="form-group">
                                    <label>Medicine Price</label>
                                    <input type="text" class="form-control" placeholder="Enter Medicine Price"
                                        name="editItemPrice" id="editItemPrice" required>
                                </div>
                                <div class="form-group">
                                    <label>Expiration Date</label>
                                    <input type="date" class="form-control" name="editDate" id="editDate" required>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Main Footer -->
            <?php include 'templates/main-footer.php' ?>
            <!-- End Main Footer -->

            <script>
                $(document).ready(function () {
                    $('#medicinetable').DataTable();


                    $('.edit-btn').on('click', function () {
                        // Extract data from the clicked button
                        var medID = $(this).data('id');
                        var itemName = $(this).data('name');
                        var itemType = $(this).data('type');
                        var itemCode = $(this).data('code');
                        var quantity = $(this).data('quantity');
                        var itemPrice = $(this).data('price');
                        var expirationDate = $(this).data('date');
                        var itemImage = $(this).data('image');

                        // Populate the modal with data
                        $('#editMedID').val(medID);
                        $('#editItemName').val(itemName);
                        $('#editItemType').val(itemType);
                        $('#editItemCode').val(itemCode);
                        $('#editQuantity').val(quantity);
                        $('#editItemPrice').val(itemPrice);
                        $('#editDate').val(expirationDate);

                        // Handle the image data in a similar way as the 'addImg' field
                        var str = itemImage;
                        var n = str.includes("data:image");

                        if (!n) {
                            itemImage = 'assets/uploads/avatar/' + itemImage;
                        }

                        // Set the source of the image field
                        $('#editProfileImage input').val(itemImage);
                        $('#my_camera img').attr('src', itemImage);

                        // Show the modal
                        $('#editModal').modal('show');
                    });



                })


            </script>
        </div>

    </div>
    <?php include 'templates/footer.php' ?>
</body>

</html>