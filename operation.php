<?php include 'server/server.php' ?>
<?php 
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
	$query = "SELECT * FROM tbloperation";
    $result = $conn->query($query);

	$query4 = "SELECT * FROM tblowner INNER JOIN tblpet ON tblowner.OwnerID = tblpet.OwnerID";
	$result4 = $conn->query($query4);

	$ownerPets = array();
	while ($row1 = $result4->fetch_assoc()) {
		$ownerPets[] = $row1;
	}

    $operation = array();
	while($row = $result->fetch_assoc()){
		$operation[] = $row; 
	}

	$query1 = "SELECT * FROM tbloperation WHERE `status`='Cancelled'";
    $result1 = $conn->query($query1);
	$active = $result1->num_rows;

	$query2 = "SELECT * FROM tbloperation WHERE `status`='Scheduled'";
    $result2 = $conn->query($query2);
	$scheduled = $result2->num_rows;

	$query3 = "SELECT * FROM tbloperation WHERE `status`='Finished'";
    $result3 = $conn->query($query3);
	$settled = $result3->num_rows;

	$query5 = "SELECT * FROM tbloperation WHERE `status`='On-going'";
    $result5 = $conn->query($query5);
	$ongoing = $result5->num_rows;

	$sql = "SELECT * FROM tbloperation ORDER BY date DESC";

// Modify the query to include JOIN operations with tblowner and tblpet
	$sql = "SELECT tbloperation.*, tblowner.OwnerName, tblpet.pet_name 
        FROM tbloperation
        LEFT JOIN tblowner ON tbloperation.OwnerID = tblowner.OwnerID
        LEFT JOIN tblpet ON tbloperation.petID = tblpet.id
        ORDER BY tbloperation.date DESC";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'templates/header.php' ?>
	<title>Purrfect Clinic Operations</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" >
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
								<h2 class="text-white fw-bold">Clinical Operation Records</h2>
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
					<div class="row mt--2">
						<div class="col-md-9">
							<div class="card">
								<div class="card-header">
									<div class="card-head-row">
										<div class="card-title">All Operations</div>
										<?php if(isset($_SESSION['username'])):?>
											<div class="card-tools">
												<a href="#add" data-toggle="modal" class="btn btn-info btn-border btn-round btn-sm">
													<i class="fa fa-plus"></i>
													Schedule Operation
												</a>
											</div>
										<?php endif?>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table id="operationtable" class="display table table-striped">
											<thead>
												<tr>
													
													<th scope="col">Name of Owner</th>
													<th scope="col">Name of Pet</th>
													<th scope="col">Operation Type</th>
													<th scope="col">Operation Bill</th>
													<th scope="col">Payment</th>
													<th scope="col">Status</th>
													<?php if(isset($_SESSION['username'])):?>
													<th scope="col">Action</th>
													<?php endif ?>
												</tr>
											</thead>
											<tbody>
												<?php if(!empty($operation)): ?>
													<?php foreach($operation as $row): ?>
													<tr>
														
													<td> 
													<?php
														$OwnerID = $row['OwnerID'];
														$ownerQuery = "SELECT OwnerName FROM tblowner WHERE OwnerID = $OwnerID";
														$ownerResult = $conn->query($ownerQuery);

														// Check if the query was successful and if there is a result
														if ($ownerResult && $ownerData = $ownerResult->fetch_assoc()) {
															echo ucwords($ownerData['OwnerName']);
														} else {
															// Handle the case where the query fails or no result is found
															echo "Owner Not Found";
														}
														?>
													</td> 
													<td> 
														
													<?php
														$petID = $row['petID'];
														$petQuery = "SELECT pet_name FROM tblpet WHERE id = $petID";
														$petResult = $conn->query($petQuery);
														
														// Check if the query was successful and if there is a result
														if ($petResult && $petData = $petResult->fetch_assoc()) {
															echo ucwords($petData['pet_name']);
														} else {
															// Handle the case where the query fails or no result is found
															echo "Pet Not Found";
														}
														?>
													</td> 
        		
														
														<td><?= ucwords($row['operationType']) ?></td>
														<td><?= ucwords($row['operationCost']) ?></td>

														<td>
														<?php
														// Check if paymentID is set in tbloperation
														if ($row['paymentID'] !== null) {
															// Fetch payments data from tblpayments
															$paymentID = $row['paymentID'];
															$paymentQuery = "SELECT amounts FROM tblpayments WHERE id = $paymentID";
															$paymentResult = $conn->query($paymentQuery);

													

															if ($paymentResult && $paymentResult->num_rows > 0) {
																$paymentRow = $paymentResult->fetch_assoc();
																$paymentAmount = $paymentRow['amounts'];

																// Compare payment amount with operation cost
																if ($paymentAmount < $row['operationCost']) {
																	echo '<span class="badge badge-warning">downpayment: ' . number_format($paymentAmount, 2) . '</span>';
																} elseif ($paymentAmount == $row['operationCost']) {
																	echo '<span class="badge badge-success">paid: ' . number_format($paymentAmount, 2) . '</span>';
																} else {
																	echo 'Payment amount exceeds operation cost';
																}
															} else {
																echo '0';
															}
														} else {
															echo '0';
														}
														?>
													</td>
													<!-- ... Continue with the rest of your HTML code ... -->

														
														<td>
															<?php if($row['status']=='Scheduled'): ?>
																<span class="badge badge-warning">Scheduled</span>
															<?php elseif($row['status']=='Cancelled'): ?>
																<span class="badge badge-danger">Cancelled</span>
															<?php elseif($row['status']=='Finished'): ?>
																<span class="badge badge-success">Finished</span>
															<?php else: ?>
																<span class="badge badge-info">On-going</span>
															<?php endif ?>
														</td>
														<?php if(isset($_SESSION['username'])):?>
														<td>
															

															
														<a type="button" href="#setid" data-toggle="modal" class="btn btn-link btn-primary" 
																title="Update Operation" onclick="editOperation2(this)" data-id="<?= $row['id'] ?>" 
																<?php if ($row['status'] === 'Cancelled' || $row['status'] === 'Finished'): ?>style="display: none;"<?php endif; ?>>
																<?php if(isset($_SESSION['username'])): ?>
																	<i class="fas fa-book"></i>
																<?php else: ?>
																	<i class="fa fa-eye"></i>
																<?php endif; ?>
																</a>


															<a type="button" data-toggle="tooltip" href="generate_operation_report.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"class="btn btn-link btn-primary" data-original-title="View Operation Report">
															<i class="fas fa-eye"></i>
																</a>


																<a type="button" data-toggle="modal" data-target="#paymentModal" class="btn btn-link btn-success" 
																	data-id="<?= $row['id'] ?>" title="Make Payment" 
																	<?php if ($row['status'] === 'Cancelled' || ($row['paymentID'] !== null && $paymentAmount == $row['operationCost'])): ?>style="display: none;"<?php endif; ?>>
																	<i class="fa fa-dollar-sign"></i>
																	</a>


															




															<?php if(isset($_SESSION['username']) && $_SESSION['role']=='administrator'):?>
															<a type="button" data-toggle="tooltip" href="model/remove_operation.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Are you sure you want to delete this operation record?');" class="btn btn-link btn-danger" data-original-title="Remove">
																<i class="fa fa-times"></i>
															</a>
															<?php endif ?>
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
						<div class="col-md-3">
							<div class="card card-stats card-danger card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
										</div>
										<div class="col-3 col-stats">
											<div class="numbers">
												<p class="card-category">Cancelled</p>
												<h4 class="card-title"><?= number_format($active) ?></h4>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="javascript:void(0)" id="activeCase" class="card-link text-light">Cancel Operation </a>
								</div>
							</div>
							
							<div class="card card-stats card-warning card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
										</div>
										<div class="col-3 col-stats">
											<div class="numbers">
												<p class="card-category">Scheduled</p>
												<h4 class="card-title"><?= number_format($scheduled) ?></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="card-body">
									<a href="javascript:void(0)" id="scheduledCase" class="card-link text-light">Scheduled Case </a>
								</div>
							</div>
							<div class="card card-stats card-info card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
										</div>
										<div class="col-3 col-stats">
											<div class="numbers">
												<p class="card-category">On going</p>
												<h4 class="card-title"><?= number_format($ongoing) ?></h4>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="javascript:void(0)" id="ongoingCase" class="card-link text-light">On going operation</a>
								</div>
							</div>
							<div class="card card-stats card-success card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
										</div>
										<div class="col-3 col-stats">
											<div class="numbers">
												<p class="card-category">Finished</p>
												<h4 class="card-title"><?= number_format($settled) ?></h4>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="javascript:void(0)" id="settledCase" class="card-link text-light">Finished Operation</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			  <!-- Modal -->
			 <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Pet Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
						<form method="POST" action="model/save_operation.php">
						<input name="csrf_token" hidden
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
										<label for="petOwners">Pet Owner</label>
										<select name="petOwner" id="petOwners" class="form-control input-sm" data-live-search="true" style="width:100%">
											<option disabled selected>Select Pet Owner</option>
											<?php
												$qc = mysqli_query($conn, "SELECT DISTINCT o.* FROM tblowner o INNER JOIN tblpet p ON o.OwnerID = p.OwnerID");
												while ($rowc = mysqli_fetch_array($qc)) {
													echo '<option data-ownerid="' . $rowc['OwnerID'] . '">' . $rowc['OwnerName'] . '</option>';
												}
											?>
										</select>

                               				 <input type="hidden" name="petOwnerID" id="petOwnerID">
											
										</div>
										
									</div>
									<div class="col-md-6">
										<div class="form-group">
										<label>Pet Name</label>
                                <select name="petName" id="petNames" class="form-control input-sm" data-live-search="true" style="width:100%">
                                </select>

                                <input type="hidden" name="petNameID" id="petNameID">
										
										</div>
										
									</div>
								</div>
								<div class="row">
		
									<div class="col-md-6">
										<div class="form-group">
											<label>Operation</label>
											<select class="form-control" name="operationType">
												<option disabled selected>Select Operation Type</option>
												<option value="Orthopaedic Operation">Orthopaedics</option>
												<option value="Sof-tissue Operation">Soft Tissue Surgery</option>
												<option value="Cardiovascular Operation">Cardiovascular System</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Date</label>
											<input type="date" class="form-control" name="date" value="<?= date('Y-m-d'); ?>" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Time</label>
											<input type="time" class="form-control" name="time" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											
											<select hidden="hidden" class="form-control" name="status" id="setStatus">
												<option disabled selected>Select Blotter Status</option>
												<option value="Scheduled" selected>Scheduled</option>
												
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>Details</label>
									<textarea class="form-control" placeholder="Enter operation details" name="details" required></textarea>
								</div>

								<div class="form-group">
									<label for="searchItems">TREATMENT PLAN:</label>
									<input type="text" class="form-control" id="searchItemsMachine" placeholder="Search...">
								</div>

								<div class="row" id="itemCards">
									<?php
									// Assuming $conn is your database connection
									$queryItems = "SELECT * FROM tbltreatmentplan";
									$resultItems = $conn->query($queryItems);

									while ($rowItem = $resultItems->fetch_assoc()) {
										
										?>
										<div class="col-md-4 item-card" onclick="selectCard(this)" data-price="<?= $rowItem['machinePrice'] ?>">
											<div class="card mb-3">
												<div class="card-body">
													<h5 class="card-title"><?= $rowItem['machineName'] ?></h5>
													<p class="card-text">Description: <?= $rowItem['machineType'] ?></p>
													<p class="card-text">Price: $<span class="item-price"><?= $rowItem['machinePrice'] ?></span></p>
												</div>
											</div>
										</div>
									<?php } ?>


								</div>

								<div class="col-md-12">
									<p class="text-right">Total: $<span id="Total">0.00</span></p>
								</div>

								
                            
                        </div>
						<input  type="hidden" name="treatmentPlans" id="treatmentPlans" value="">


                        <div class="modal-footer">
						    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

			

			<!-- Modal for Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Create Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Display Grand Total -->
                <div class="form-group">
                    <label>Operation Bill</label>
                    <input type="text" class="form-control" id="paymentOperationCost" name="operationCost" readonly>
                </div>

                <!-- Display Paid amount -->
                <div class="form-group">
                    <label>Paid</label>
                    <input type="text" class="form-control" id="paidAmount" name="paidAmount" readonly>
                </div>

                <!-- Display Balance amount -->
                <div class="form-group">
                    <label>Balance</label>
                    <input type="text" class="form-control" id="balanceAmount" name="balanceAmount" readonly>
                </div>

                <form method="POST" action="model/save_opments.php">
				<input name="csrf_token" hidden
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" placeholder="Enter amount to pay" required>
                    </div>
                    <div class="form-group">
                        <label>Date Issued</label>
                        <input type="date" class="form-control" name="date" id="paymentDate" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Payment Details (Optional)</label>
                        <textarea class="form-control" placeholder="Enter Payment Details" id="details" name="details"></textarea>
                    </div>
                    <!-- Hidden field to store the operation ID -->
                    <input hidden id="paymentOperationID" name="id">
                    <!-- Hidden field to store the ownerName -->
                    <input hidden id="paymentOwnerName" name="name">
                    <!-- Hidden field to store the petOwner -->
                    <input hidden id="paymentOwnerID" name="petOwner">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

					
			

			<!-- Modal -->
			<div class="modal fade" id="setid" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Update of Operation</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form id="operationForm" method="POST" action="model/finish_operation.php">
							<input name="csrf_token" hidden
                                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
							<div class="row justify-content-center text-center mb-3">
								<div class="col-md-4">
									<button type="button" class="btn btn-danger btn-block" id="cancelOperationBtn">Cancel Operation</button>
								</div>

								<div class="col-md-4">
									<button type="button" class="btn btn-info btn-block" id="proceedOperationBtn">Proceed Operation</button>
								</div>

								
								<div class="col-md-4" id="finishOperationButtonContainer" style="<?php echo ($row['status'] == 'On-going') ? 'display: block;' : 'display: none;'; ?>">
                            <button type="button" class="btn btn-success btn-block" id="finishOperationBtn">Finish Operation</button>
                        </div>
							</div>

						<!-- Fields for Proceed Operation -->
							<div id="proceedOperationFields" style="display: none;">
								<div class="form-group">
									<label for="searchItems">Search Items:</label>
									<input type="text" class="form-control" id="searchItems" placeholder="Search items...">
								</div>

								<div class="row" id="itemCards">
    <?php
    // Assuming $conn is your database connection
    $queryItems = "SELECT * FROM tblmedi_mat";
    $resultItems = $conn->query($queryItems);

    while ($rowItem = $resultItems->fetch_assoc()) {
        $itemImage = $rowItem['itemImage'];

        // Check if the image data is base64-encoded
        if (preg_match('/data:image/i', $itemImage)) {
            $imageSrc = $itemImage;
        } else {
            // Convert BLOB data to base64 for image display
            $imageSrc = 'assets/uploads/avatar/' . $itemImage;
        }
    ?>
        <div class="col-md-4 item-card">
            <div class="card mb-3">
                <!-- Display the image using the 'src' attribute -->
                <img src="<?= $imageSrc ?>" class="card-img-top" alt="Item Image">
                <div class="card-body">
                    <h5 class="card-title"><?= $rowItem['itemName'] ?></h5>
                    <p class="card-text">Type: <?= $rowItem['typeName'] ?></p>
					<p class="card-text">Stocks: <?= $rowItem['quantity'] ?></p>
                    <p class="card-text">Quantity:
                        <input type="number" class="form-control quantity-input" value="0" min="0" max="<?= $rowItem['quantity'] ?>" data-price="<?= $rowItem['itemPrice'] ?>">
                    </p>
                    <p class="card-text">Price: $<span class="item-price"><?= $rowItem['itemPrice'] ?></span></p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>


									<div class="row">
										<div class="col-md-12">
											<p class="text-right">Grand Total: $<span id="grandTotal">0.00</span></p>
										</div>
									</div>
								</div>



								<!-- Fields for Finish Operation -->
								<div id="finishOperationFields" style="display: none;">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												
												<input hidden type="time" class="form-control" name="finishOperationTime" id="finishOperationTime" required>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												
												<input hidden type="date" class="form-control" name="finishOperationDate" id="finishOperationDate" required>
											</div>
										</div>
									</div>

									<div class="form-group">
										
										<textarea class="form-control" placeholder="Enter operation results here..." name="finishOperationDetails" required></textarea>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											
											<select hidden class="form-control" name="finishStatus" id="finishOperationStatus">
												<option value="Finished" selected>Finished</option>
											</select>
										</div>
									</div>
								</div>

								<!-- Fields for Cancel Operation -->
								<div id="cancelOperationFields" style="display: none;">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												
												<input hidden type="time" class="form-control" name="cancelOperationTime" id="cancelOperationTime" required>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												
												<input hidden type="date" class="form-control" name="cancelOperationDate" id="cancelOperationDate" required>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label>Cancel Operation Details</label>
										<textarea class="form-control" placeholder="Enter operation results here..." name="cancelOperationDetails" required></textarea>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											
											<select hidden class="form-control" name="cancelStatus" id="cancelOperationStatus">
												<option value="Cancelled" selected>Cancelled</option>
											</select>
										</div>
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<input hidden id="set_id" name="id" />
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<?php if(isset($_SESSION['username'])):?>
									<button type="button" class="btn btn-primary" id="updateBtn">Update</button>
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
			<input type="hidden" id="selectedAction" name="selectedAction" />
			<!-- Add this hidden input field inside the form -->
<input type="hidden" name="treatmentPlans" id="treatmentPlansInput">

			

<?php include 'templates/footer.php' ?>
<script src="assets/js/plugin/datatables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>

<script>
	// Update the hidden input field with selected treatment plans
document.getElementById('treatmentPlans').value = JSON.stringify(selectedTreatmentPlans);


var selectedTreatmentPlans = []; // Array to store selected treatment plans

function selectCard(card) {
    var machineName = card.querySelector('.card-title').innerText;

    // Toggle card selection
    if (selectedTreatmentPlans.includes(machineName)) {
        card.style.border = "none";
        var index = selectedTreatmentPlans.indexOf(machineName);
        selectedTreatmentPlans.splice(index, 1);
    } else {
        card.style.border = "2px solid blue";
        selectedTreatmentPlans.push(machineName);
    }

    // Calculate and display the total
    calculateTotal();
}

function calculateTotal() {
    var total = 0;

    // Sum up prices of selected cards
    selectedTreatmentPlans.forEach(function (machineName) {
        var cards = document.querySelectorAll('.card-title');
        cards.forEach(function (card) {
            if (card.innerText === machineName) {
                var cardElement = card.closest('.item-card');
                if (cardElement) {
                    total += parseFloat(cardElement.dataset.price);
                }
            }
        });
    });

    // Update the total displayed on the page
    document.getElementById('Total').innerText = total.toFixed(2);

    // Update the hidden input field with selected treatment plans
    document.getElementById('treatmentPlans').value = JSON.stringify(selectedTreatmentPlans);
}


	  // Function to open the payment modal and set the operation ID
	  
    var selectedAction = ''; // Variable to track the selected action

	// Example of how to call the function when the user clicks 'Proceed'
	$('#proceedButton').on('click', function() {
		var operationId = $('#operationId').val(); // Update with the actual ID retrieval logic
		var selectedItems = {};

		// Iterate through the selected items and their quantities
		$('.quantity-input').each(function() {
			var itemId = $(this).data('item-id');
			var quantity = $(this).val();

			// Only include items with a non-zero quantity
			if (quantity > 0) {
				selectedItems[itemId] = quantity;
			}
		});

		// Call the function to update the operation
		proceedOperation(operationId, selectedItems);
	});

	document.getElementById('searchItems').addEventListener('input', function () {
		const searchTerm = this.value.toLowerCase();
		const itemCards = document.querySelectorAll('.item-card');

		itemCards.forEach(function (card) {
			const itemName = card.querySelector('.card-title').textContent.toLowerCase();
			card.style.display = itemName.includes(searchTerm) ? 'block' : 'none';
		});
	});

	function updateGrandTotal() {
		let grandTotal = 0;

		// Loop through all items and calculate total cost
		document.querySelectorAll('.quantity-input').forEach(function (input) {
			const quantity = parseInt(input.value);
			const price = parseFloat(input.getAttribute('data-price'));
			const totalCost = quantity * price;

			grandTotal += totalCost;
		});

		// Update the grand total
		document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
	}

	document.getElementById('proceedOperationBtn').addEventListener('click', function () {
		console.log('Proceed button clicked');
		selectedAction = 'proceed'; // Set the action to 'proceed'
		showProceedOperationFields();
		updateGrandTotal();

		// Set the hidden input field value to 'On-going'
		document.getElementById('selectedAction').value = 'proceed';
	});

	// Handle quantity changes and update grand total
	document.addEventListener('input', function (e) {
		if (e.target.classList.contains('quantity-input')) {
			updateGrandTotal();
		}
	});

	function showProceedOperationFields() {
		document.getElementById('proceedOperationFields').style.display = 'block';
		document.getElementById('finishOperationFields').style.display = 'none';
		document.getElementById('cancelOperationFields').style.display = 'none';
	}



	function showFinishOperationFields() {
		document.getElementById('finishOperationFields').style.display = 'block';
		document.getElementById('cancelOperationFields').style.display = 'none';
		document.getElementById('proceedOperationFields').style.display = 'none';
	}

	function showCancelOperationFields() {
		document.getElementById('cancelOperationFields').style.display = 'block';
		document.getElementById('finishOperationFields').style.display = 'none';
		document.getElementById('proceedOperationFields').style.display = 'none';
	}



	// ... (your existing code)

	document.getElementById('cancelOperationBtn').addEventListener('click', function () {
    console.log('Cancel button clicked');
    selectedAction = 'cancel'; // Set the action to 'cancel'
    showCancelOperationFields();

    // Set the hidden input field value to 'cancel'
    document.getElementById('selectedAction').value = 'cancel';

    // Automatically set current date and time for cancel operation
    const currentDate = new Date().toISOString().split('T')[0];
    const currentTime = new Date().toTimeString().split(' ')[0];

    document.getElementById('cancelOperationDate').value = currentDate;
    document.getElementById('cancelOperationTime').value = currentTime;
});


	document.getElementById('finishOperationBtn').addEventListener('click', function () {
    console.log('Finish button clicked');
    selectedAction = 'finish'; // Set the action to 'finish'
    showFinishOperationFields();

    // Set the hidden input field value to 'finish'
    document.getElementById('selectedAction').value = 'finish';

    // Automatically set current date and time for finish operation
    const currentDate = new Date().toISOString().split('T')[0];
    const currentTime = new Date().toTimeString().split(' ')[0];
    
    document.getElementById('finishOperationDate').value = currentDate;
    document.getElementById('finishOperationTime').value = currentTime;
});


	document.getElementById('updateBtn').addEventListener('click', function () {
		console.log('Update button clicked');
		
		// Check the selected action before submitting the form
		if (selectedAction === 'proceed' || selectedAction === 'finish' || selectedAction === 'cancel') {
			console.log('Action is valid');

			// Include selected items data
			const selectedItems = {};
			document.querySelectorAll('.quantity-input').forEach(function (input) {
				const itemName = input.closest('.item-card').querySelector('.card-title').textContent;
				const quantity = input.value;
				selectedItems[itemName] = quantity;
			});

			// Include selected action and items in the form data
			const formData = new FormData();
			formData.append('selectedAction', selectedAction);
			formData.append('selectedItems', JSON.stringify(selectedItems));

			// Serialize other form fields
			const formElements = document.getElementById('operationForm').elements;
			for (let i = 0; i < formElements.length; i++) {
				const element = formElements[i];
				if (element.name !== 'selectedAction') {
					formData.append(element.name, element.value);
				}
			}

			fetch("model/finish_operation.php", {
				method: "POST",
				body: formData,
			})
			.then(response => {
				// Remove the response.json() call to eliminate any JSON processing
				// If you're sure the response is not JSON, you can remove this line
				return response.text();
			})
			.then(data => {
				// This log will now show the raw response text
				console.log('Fetch request successful:', data);

				// Reload the page without checking for success
				window.location.reload();
			})
			.catch(error => {
				// Remove the entire catch block to eliminate error handling
				// console.error('Fetch request error:', error);
				// alert('Operation update failed. Please check the console for details.');
			});
		} else {
			console.log('Action is invalid');
			alert('Please select an action before updating.');
		}
	});


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

	
	

		$('a[data-target="#paymentModal"]').on('click', function () {
    var operationID = $(this).data('id');

    // Fetch operation details using AJAX
    $.ajax({
        url: 'get_operation_cost.php',
        type: 'POST',
        data: { operationID: operationID },
        success: function (response) {
            var data = JSON.parse(response);

            // Set id, operationCost, operationType, OwnerName, petOwner values in your form
            $('#paymentOperationID').val(data.id);
            $('#paymentOperationCost').val(data.operationCost);
            $('#details').val(data.operationType);
            $('#paymentOwnerName').val(data.ownerName);
            $('#paymentOwnerID').val(data.petOwner);

            // Fetch amounts from tblpayments using another AJAX request
            $.ajax({
                url: 'get_payment_amounts.php',
                type: 'POST',
                data: { petOwner: data.petOwner, id: data.id }, // Include the id parameter
                success: function (amountsResponse) {
                    var amountsData = JSON.parse(amountsResponse);
                    
                    // Set Paid amount and calculate Balance amount
                    var paidAmount = 0;
                    if (amountsData.length > 0) {
                        paidAmount = amountsData.reduce((total, record) => total + parseFloat(record.amounts), 0);
                    }

                    $('#paidAmount').val(paidAmount);
                    $('#balanceAmount').val(data.operationCost - paidAmount);
                },
                error: function (amountsError) {
                    console.log(amountsError);
                }
            });
        },
        error: function (error) {
            console.log(error);
        }
    });
});


$('form').submit(function(event) {
        // Get the entered payment amount
        var paymentAmount = parseFloat($('input[name="amount"]').val());

        // Get the operation cost
        var operationCost = parseFloat($('#paymentOperationCost').val());

        // If the payment amount is greater than the operation cost
        if (paymentAmount > operationCost) {
            // Prevent form submission
            event.preventDefault();
            
            // Display an error message (you can customize this part)
            alert('Payment amount cannot exceed the operation cost.');
        }
    });

	$('a[data-toggle="modal"]').on('click', function () {
    var operationID = $(this).data('id');

    // Fetch payment status using AJAX
    $.ajax({
        url: 'check_payment_status.php',
        type: 'POST',
        data: { operationID: operationID },
        dataType: 'json',  // Specify that the expected response is JSON
        success: function (response) {
            // Check if the hideButton property is true
            if (response.hideButton === 1) {
                // Hide the button
                $('a[data-toggle="modal"][data-id="' + operationID + '"]').hide();
            } else {
                // Show the button
                $('a[data-toggle="modal"][data-id="' + operationID + '"]').show();
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
});

var currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        $('#time').val(currentTime);
        
        // Show the hidden div
        $('.col-md-6').show();


	var currentDate = new Date().toISOString().split('T')[0];

        // Set the current date to the date input field
        $('#paymentModal').on('show.bs.modal', function () {
            $('#paymentDate').val(currentDate);
        });

		var oTable = $('#operationtable').DataTable({
			"order": [[4, "asc"]]
		});

		$("#petOwner").selectpicker();
		$("#petNames").selectpicker();

		$("#petOwners").selectpicker();
		$("#petName").selectpicker();


	});

</script>



</body>
</html>