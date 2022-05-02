<?php
session_start();
extract($_REQUEST);
include_once '../config.php';
include("../connection.php");
$gtotal = array();
$ar = array();
$total = 0;
if (isset($_GET['product'])) //product id
{
	$product_id = $_GET['product'];
} else {
	$product_id = "";
}
if (isset($_SESSION['cust_id'])) {
	$cust_id = $_SESSION['cust_id'];
	$qq = mysqli_query($con, "select * from tblcustomer where fld_email='$cust_id'");
	$qqr = mysqli_fetch_array($qq);
}
if (empty($cust_id)) {
	header("location:index.php?msg=you must login first");
}
if (!empty($product_id && $cust_id)) {
	if (mysqli_query($con, "insert into tblcart (fld_product_id,fld_customer_id) values ('$product_id','$cust_id') ")) {
		echo "success";
		$product_id = "";
		header("location:cart.php");
	} else {
		echo "failed";
	}
}
if (isset($del)) {
	//echo $del;
	if (mysqli_query($con, "delete from tblcart where fld_cart_id='$del' && fld_customer_id='$cust_id'")) {
		header("location:deletecart.php");
	}
}
if (isset($logout)) {
	session_destroy();
	header("location:../index.php");
}
if (isset($login)) {
	session_destroy();
	header("location:index.php");
}
//update section
$cust_details = mysqli_query($con, "select * from tblcustomer where fld_email='$cust_id'");
$det_res = mysqli_fetch_array($cust_details);
$fld_name = $det_res['fld_name'];
$fld_email = $det_res['fld_email'];
$fld_mobile = $det_res['fld_mobile'];
$fld_password = $det_res['password'];
if (isset($update)) {
	if (mysqli_query($con, "update tblcustomer set fld_name='$name',fld_mobile='$mobile',password='$pswd' where fld_email='$fld_email'")) {
		header("location:customerupdate.php");
	}
}
$query = mysqli_query($con, "select tbfood.foodname,tbfood.fldvendor_id,tbfood.cost,tbfood.cuisines,tbfood.fldimage,tblcart.fld_cart_id,tblcart.fld_product_id,tblcart.fld_customer_id from tbfood inner  join tblcart on tbfood.food_id=tblcart.fld_product_id where tblcart.fld_customer_id='$cust_id'");
$re = mysqli_num_rows($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Cart </title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/footer.css">
	<link rel="stylesheet" href="../css/index.css">
	<?php
	include('../link.php')
	?>
	<style>
		ul li {
			list-style: none;
		}

		ul li a {
			color: black;
			text-decoration: none;
		}

		ul li a:hover {
			color: black;
			text-decoration: none;
		}
	</style>
	<script>
		function del(id) {
			if (confirm('are you sure you want to cancel order') == true) {
				window.location.href = 'cancelorder.php?id=' + id;
			}
		}
		$('#remove').on('click', function() {
			$('#item').remove();
		})
	</script>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
		<a class="navbar-brand" href="../index.php"><span style="color:green;font-family: 'Permanent Marker', cursive;">Food Hunt</span></a>
		<?php
		if (!empty($cust_id)) {
		?>
			<a class="navbar-brand" style="color:black; text-decoratio:none;"><i class="far fa-user"> 
			<?php if (isset($cust_id)) {
			echo $qqr['fld_name'];
			} ?></i></a>
		<?php
		}
		?>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item active">
					<a class="nav-link" href="../index.php">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../aboutus.php">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../services.php">Services</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../contact.php">Contact</a>
				</li>
				<li class="nav-item">
					<form method="post">
						<?php
						if (empty($cust_id)) {
						?>
							<span style="color:black; font-size:30px;"><i class="fa fa-shopping-cart" aria-hidden="true"><span style="color:red;" id="cart" class="badge badge-light">4</span></i></span>
							&nbsp;&nbsp;&nbsp;
							<button class="btn btn-outline-danger my-2 my-sm-0" name="login">Log In</button>&nbsp;&nbsp;&nbsp;
						<?php
						} else {
						?>
							<a href="cart.php"><span style="color:green; font-size:30px;"><i class="fa fa-shopping-cart" aria-hidden="true"><span style="color:green;" id="cart" class="badge badge-light"><?php if (isset($re)) echo $re; ?></span></i></span></a>
							<button class="btn btn-outline-success my-2 my-sm-0" name="logout" type="submit">Log Out</button>&nbsp;&nbsp;&nbsp;
						<?php
						}
						?>
					</form>
				</li>
			</ul>
		</div>
	</nav>
	<br><br>
	<div class="middle" style="  padding:60px; border:1px solid #ED2553;  width:100%;">

		<ul class="nav nav-tabs nabbar_inverse" id="myTab" style="background:darkcyan;border-radius:10px 10px 10px 10px;" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" style="color:#BDDEFD;" id="viewitem-tab" data-toggle="tab" href="#viewitem" role="tab" aria-controls="viewitem" aria-selected="true">View Cart</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" style="color:#BDDEFD;" id="manageaccount-tab" data-toggle="tab" href="#manageaccount" role="tab" aria-controls="manageaccount" aria-selected="false">Account Settings</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" style="color:#BDDEFD;" id="orders-tab" data-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">Orders</a>
			</li>
		</ul>
		<br><br>

		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="viewitem" role="tabpanel" aria-labelledby="home-tab">
				<table class="table">
					<tbody>
						<?php
						$query = mysqli_query($con, "select tbfood.foodname,tbfood.fldvendor_id,tbfood.cost,tbfood.cuisines,tbfood.fldimage,tblcart.fld_cart_id,tblcart.fld_product_id,tblcart.fld_customer_id from tbfood inner  join tblcart on tbfood.food_id=tblcart.fld_product_id where tblcart.fld_customer_id='$cust_id'");
						$re = mysqli_num_rows($query);
						if ($re) {
							while ($res = mysqli_fetch_array($query)) {
								$vendor_id = $res['fldvendor_id'];
								$v_query = mysqli_query($con, "select * from tblvendor where fldvendor_id='$vendor_id'");
								$v_row = mysqli_fetch_array($v_query);
								$em = $v_row['fld_email'];
								$nm = $v_row['fld_name'];
						?>
								<tr id="item">
									<td>
										<image src="../image/restaurant/<?php echo $em . "/foodimages/" . $res['fldimage']; ?>" height="80px" width="100px">
									</td>
									<td><?php echo $res['foodname']; ?></td>
									<td><?php echo "USD" . $res['cost']; ?></td>
									<td><?php echo $res['cuisines']; ?></td>
									<td><?php echo $nm; ?></td>
									<form method="post" enctype="multipart/form-data">
										<td><button type="submit" name="del" value="<?php echo $res['fld_cart_id'] ?>" class="btn btn-danger">Delete</button></td>
									</form>
									<td><?php $total = $total + $res['cost'];
										$gtotal[] = $total;  ?></td>
								</tr>
							<?php } ?>
							<tr>
								<td>
									<a href="../index.php" class="btn btn-warning mb-3 text-white textstyle">Continue Shopping</a>
								</td>
								<td>
									<h6>Total: <?php echo '$' . $total . ' ' . PAYPAL_CURRENCY; ?></h6>
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>
									<!-- <h6>Total: <?php echo '$' . $total . ' ' . PAYPAL_CURRENCY; ?></h6> -->
									<form action="<?php echo PAYPAL_URL; ?>" method="post">
										<!-- Identify your business so that you can collect the payments. -->
										<input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">
										<!-- Specify a Buy Now button. -->
										<input type="hidden" name="cmd" value="_xclick">
										<!-- Specify details about the item that buyers will purchase. -->
										<?php
										$results = $con->query("SELECT * FROM tbfood ");
										while ($row = $results->fetch_assoc()) {
										?>
											<input type="hidden" name="item_name" value="<?php echo $row['foodname'] ?>">
											<input type="hidden" name="item_number" value="<?php echo $row['food_id'] ?>">
											<?php
										$customer = $con->query("SELECT * FROM tblcart ");
										while ($cus = $customer->fetch_assoc()) {
										?>
											<input type="hidden" name="cus_name" value="<?php echo $cus['fld_customer_id'] ?>">
											<?php } ?>
											<input type="hidden" name="amount" value="<?php echo $total; ?>">
											<input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">
											<!-- Specify URLs -->
											<input type="hidden" name="return" value="<?php echo PAYPAL_RETURN_URL; ?>">
											<input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">
										<?php } ?>
										<!-- Display the payment button. -->
										<input type="image" id="remove" class="btn btn-warning" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif">
									</form>

								</td>
							</tr>
						<?php
						} else {
						?>
							<tr><button type="button" class="btn btn-outline-success"><a href="../index.php" style="color:green; text-decoration:none;">No Items In cart Let's Shop Now</a></button></tr>

						<?php
						}
						?>
					</tbody>
				</table>

				<!-- Account Setting -->

				<span style="color:green; text-align:centre;"><?php if (isset($success)) {
																	echo $success;
																} ?></span>




			</div>
			<div class="tab-pane fade" id="manageaccount" aria-labelledby="-tab">
				<div class="col-10">
					<div class="card">
						<div class="card-header">
						</div>
						<div class="card-body">
							<form method="post" enctype="multipart/form-data">
								<div class="form-group">
									<label for="name">Name</label>
									<input type="text" id="name" value="<?php if (isset($fld_name)) {
									echo $fld_name;	
									} ?>" class="form-control" name="name" required="required" />
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									<input type="email" id="email" name="email" value="<?php if (isset($fld_email)) {
									echo $fld_email;
									} ?>" class="form-control" readonly />
								</div>
								<div class="form-group">
									<label for="mobile">Mobile</label>
									<input type="tel" id="mobile" class="form-control" name="mobile" value="<?php if (isset($fld_mobile)) {
									echo $fld_mobile;
									} ?>" placeholder="" required>
								</div>
								<div class="form-group">
									<label for="pwd">Password:</label>
									<input type="password" name="pswd" value="<?php if (isset($fld_password)) {
									echo $fld_password;
									} ?>" class="form-control" id="pwd" required />
								</div>
								<button type="submit" name="update" style="background:#ED2553; border:1px solid #ED2553;" class="btn btn-primary">Update</button>
								<div class="footer" style="color:red;"><?php if (isset($ermsg)) {
									echo $ermsg;
									} ?><?php if (isset($ermsg2)) {
									echo $ermsg2;
									} ?>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
				<table class="table">
					<th>Order Number</th>
					<th>Item Name</th>
					<th>Price</th>
					<th>Cancel order</th>
					<tbody>
						<?php
						$quer_res = mysqli_query($con, "select * from payments");
						while ($roww = mysqli_fetch_array($quer_res)) {
							$fid = $roww['payment_id'];
							$qr = mysqli_query($con, "select * from tbfood where food_id='$fid'");
							$qrr = mysqli_fetch_array($qr);
						?>
						<tr>
							<td><?php echo $roww['payment_id']; ?></td>
							<?php
							if (empty($qrr['foodname'])) {
							?>
							<td><span style="color:red;">Product Not Available Now</span></td>
							<?php
							} else {
							?>
							<td><?php echo $qrr['foodname']; ?></td>
							<?php
							}
							?>
							<td><?php echo $qrr['foodname']; ?></td>
							<td><a href="#" onclick="del(<?php echo $roww['payment_gross']; ?>);"><button type="button" class="btn btn-danger">Cancel Order</button></a></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>


			</div>
		</div>
	</div>
	<?php
	include("../footer.php");
	?>
</body>

</html>