<?php require_once('../Connections/conn.php'); ?>
<?php
if (!isset($_SESSION)) {
	session_start();
}
$MM_authorizedUsers = "0";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
	$isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
	if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
		$arrUsers = Explode(",", $strUsers); 
		$arrGroups = Explode(",", $strGroups); 
		if (in_array($UserName, $arrUsers)) { 
			$isValid = true; 
		} 
    // Or, you may restrict access to only certain users based on their username. 
		if (in_array($UserGroup, $arrGroups)) { 
			$isValid = true; 
		} 
		if (($strUsers == "") && false) { 
			$isValid = true; 
		} 
	} 
	return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['clerk'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['clerk'], $_SESSION['MM_UserGroup'])))) {   
	$MM_qsChar = "?";
	$MM_referrer = $_SERVER['PHP_SELF'];
	if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
	if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
		$MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
	$MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	header("Location: ". $MM_restrictGoTo); 
	exit;
}
?>

<?php
if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
		if (PHP_VERSION < 6) {
			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		}

		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

		switch ($theType) {
			case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;    
			case "long":
			case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
			case "double":
			$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			break;
			case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "timestamp":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
		}
		return $theValue;
	}
}

mysql_select_db($database_conn, $conn);
$query_chama_acc = "SELECT * FROM chama_acc";
$chama_acc = mysql_query($query_chama_acc, $conn) or die(mysql_error());
$row_chama_acc = mysql_fetch_assoc($chama_acc);
$totalRows_chama_acc = mysql_num_rows($chama_acc);



$colname_getLoan = $colname_gettid=" ";
if (isset($_GET['id_no'])) {
	$colname_getLoan = $_GET['id_no'];
	$colname_gettid = $_GET['t_id'];
}
mysql_select_db($database_conn, $conn);
$query_getLoan = sprintf("SELECT * FROM loan_request WHERE id_no = %s AND t_id=%s", GetSQLValueString($colname_getLoan, "int"),GetSQLValueString($colname_gettid, "int"));
$getLoan = mysql_query($query_getLoan, $conn) or die(mysql_error());
$row_getLoan = mysql_fetch_assoc($getLoan);
$totalRows_getLoan = mysql_num_rows($getLoan);

mysql_select_db($database_conn, $conn);
$query_loans = "SELECT * FROM loan_request ORDER BY id ASC";
$loans = mysql_query($query_loans, $conn) or die(mysql_error());
$row_loans = mysql_fetch_assoc($loans);
$totalRows_loans = mysql_num_rows($loans);
?>
<?php
//echo "Today is " . date("Y-m-d") . "<br>";
$today=date("Y-m-d");
$date=date_create($today);
$due_date=date_add($date,date_interval_create_from_date_string("10 days"));
$due_date= date_format($date,"Y-m-d");
$due_date;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$innsuficient_balance="";
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "issue_loan")) {
	
	//cheking wether the amound your requesting for is less tham what we have
		//loan procesing
	if(($qualify<$row_chama_acc['acc_bal']) &&($_POST['status']==1)){
		$qualify=$_POST['qualify'];
		$intrest=(0.12*$qualify);
		$to_pay=($qualify+$intrest);
	//dates
		$today=date("Y-m-d");
		$date=date_create($today);
		$due_date=date_add($date,date_interval_create_from_date_string("10 days"));
		$due_date= date_format($date,"Y-m-d");
		$due_date;
	//end of dates
	//updating starts here
		$updateSQL = sprintf("UPDATE loan_request SET `borrow_date`=%s,`due_date`=%s,`approved`=%s,`to_pay`=%s,`decline_reason`=%s,award=%s WHERE id_no=%s AND t_id=%s",
			GetSQLValueString($today, "date"),
			GetSQLValueString($due_date, "date"),
			GetSQLValueString(1, "int"),
			GetSQLValueString($to_pay, "int"),
			GetSQLValueString($_POST['reason'], "text"),
			GetSQLValueString($qualify, "int"),
			GetSQLValueString($_POST['id_no'], "int"),
			GetSQLValueString($_POST['t_id'], "int"));

		mysql_select_db($database_conn, $conn);
		$Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
	//update the member reg fee table
		$updateSQL2 = sprintf("UPDATE membership_fee SET loan=%s,`approve`=%s,loan_amound=%s WHERE mem_idno=%s",

			GetSQLValueString(1, "int"),
			GetSQLValueString(1, "int"),
			GetSQLValueString($to_pay, "int"),
			GetSQLValueString($_POST['id_no'], "int"));

		mysql_select_db($database_conn, $conn);
		$Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
	//update the chama account balance
		$to_pay_interest=($row_chama_acc['to_pay_interest']+$intrest);
		$acc_balance=($row_chama_acc['acc_total']-$qualify);
		$loan_total=($row_chama_acc['loan']+$qualify);
		$updateSQL3 = sprintf("UPDATE chama_acc SET `acc_bal`=%s,`loan`=%s,to_pay_interest=%s",
			GetSQLValueString($acc_balance, "int"),
			GetSQLValueString($loan_total, "int"),
			GetSQLValueString($to_pay_interest, "int"));

		mysql_select_db($database_conn, $conn);
		$Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());
	//updating ends here
		echo "<script> alert('SUCESS');
		window.location.href='clerk_prof.php';
	</script>";
}elseif(($_POST['status']==2)){
	$updateSQL = sprintf("UPDATE loan_request SET `borrow_date`=%s,`due_date`=%s,`approved`=%s,`to_pay`=%s,`decline_reason`=%s WHERE id_no=%s AND t_id=%s",
		GetSQLValueString('', "date"),
		GetSQLValueString('', "date"),
		GetSQLValueString(2, "int"),
		GetSQLValueString(0, "int"),
		GetSQLValueString($_POST['reason'], "text"),
		GetSQLValueString($_POST['id_no'], "int"),
		GetSQLValueString($_POST['t_id'], "int"));

	mysql_select_db($database_conn, $conn);
	$Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
	//update the member reg fee table
	$updateSQL2 = sprintf("UPDATE membership_fee SET loan=%s,`approve`=%s WHERE mem_idno=%s",
		GetSQLValueString(2, "int"),
		GetSQLValueString(2, "int"),
		GetSQLValueString($_POST['id_no'], "int"));

	mysql_select_db($database_conn, $conn);
	$Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
	echo "<script> alert('Your loan was NOT approved');
	window.location.href='clerk_prof.php';
</script>";
	//update the chama account balance	
}elseif($row_chama_acc['acc_bal']<$qualify){
		//$innsuficient_balance="The chama Account Balance is insufficient";
	echo "<script> alert('INNSUFFICIENT ACCOUNT BALANCE!');</script>";	
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>KAWA Self Help Group</title>

	<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/font-awesome.min.css">
	<link rel="stylesheet" href="../css/animate.css">
	<link href="../css/animate.min.css" rel="stylesheet"> 
	<link href="../css/style.css" rel="stylesheet" />
	<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
	<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
</head>
<body>	
	<header id="header">
		<?php include('../header/clerk_head.php');?>		
	</header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-md-12">
				<!--new members-->
				<form action="" method="post" name="loan" id="loan">
					<table class = "table table-striped" width="843" border="1" id="newMember">
						<tr>
							<th scope="col">t-id</th>
							<th scope="col">Approved</th>
							<th scope="col">id number</th>
							<th scope="col">Member</th>
							<th scope="col">loan Request</th>
							<th scope="col">Reason</th>
							<th scope="col">Application Date</th>
							<th scope="col">Payed</th>
							<th scope="col">Qualify</th>
							<th scope="col">Awarded</th>
							<th scope="col">Penalty</th>
							<th scope="col">Action</th>
						</tr>
						<?php if ($totalRows_loans > 0) { // Show if recordset not empty ?>
						<?php do { ?>
						<tr>
							<td>
								<?php echo $row_loans['t_id']; ?>
							</td>
							<td scope="col">
								<?php if($row_loans['approved']==1){?>
								<span class="glyphicon glyphicon-ok"></span>
								<?php }?>
							</td>
							<td>
								<?php echo $row_loans['id_no']; ?>
							</td>
							<td>
								<?php echo $row_loans['names']; ?>
							</td>
							<td style="color:#F00">
								<?php 
								$request=($row_loans['request_amound']+$row_loans['add_more']);
								echo $request; 
								?>
							</td>
							<td>
								<?php echo $row_loans['req_reason']; ?>
							</td>
							<td>
								<?php echo $row_loans['request_date']; ?>
							</td>
							<td>
								<?php echo $row_loans['payed']; ?>
							</td>
							<td style="color:#F00">
								<?php echo $row_loans['request_amound'];?>
							</td>
							<th scope="col" style="color:black">
								<?php echo $row_loans['award']; ?>
							</th>
							<td scope="col">
								<?php echo $row_loans['penalty']; ?>
							</td>
							<td>
								<?php if($row_loans['approved']==0){?>
								<a href="loan.php?id_no=<?php echo $row_loans['id_no']; ?>&&t_id=<?php echo $row_loans['t_id']; ?>">ISSUE</a>
								<?php }elseif($row_loans['setted']==1){echo "Settled";}elseif($row_loans['approved']==2){echo "Loan Not Isuued";}else{?>
								<a href="pay.php?id_no=<?php echo $row_loans['id_no']; ?>&&t_id=<?php echo $row_loans['t_id']; ?>">Pay</a>
								<?php }?>
							</td>
						</tr>
						<?php } while ($row_loans = mysql_fetch_assoc($loans)); ?>
						<?php } // Show if recordset not empty ?>
					</table>
				</form>
			</div>
			<!--new members-->
			<div class="col-md-9">
				<!--credit-->
				<form action="<?php echo $editFormAction; ?>" method="POST" name="issue_loan" id="issue_loan">
				<table class = "table table-striped" width="292" border="1" id="newMember">
						<caption>
							Loan Processing
						</caption>
						<tr>
							<th height="52" scope="row">status</th>
							<td><span id="spryselect1">
								<label for="status"></label>
								<select name="status" id="status" required>
									<option value="1">Approve</option>
									<option value="2">Decline</option>
								</select>
								<span class="selectRequiredMsg">Please select an item.</span></span>
							</td>
						</tr>
						<tr>
							<th scope="row">Reason</th>
							<td>
								<label for="reason"></label>
								<textarea name="reason" id="reason" required></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">Award</th>
							<td><span id="sprytextfield1">
								<label for="qualify"></label>
								<input type="text" name="qualify" id="qualify">
								<span class="textfieldRequiredMsg">A value is required.</span></span>
							</td>
						</tr>
						<tr>
							<th scope="row">Mem ID no</th>
							<td>
								<label for="id_no"></label>
								<input name="id_no" type="text" id="id_no" value="<?php echo $row_getLoan['id_no']; ?>" readonly>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<input name="t_id" type="text" value="<?php echo $row_getLoan['t_id']; ?>">
							</th>
							<td>
								<input type="submit" name="submit" id="submit" value="Submit">
							</td>
						</tr>
					</table>
					<input type="hidden" name="due_date" id="due_date">
					<label for="due_date"></label>
					<input type="hidden" name="MM_update" value="issue_loan">
				</form>
				<!--credit-->
			</div>
		</div>

	</div>
	<div class="sub-footer">
		<?php include('../footer/sub_footer.php');?>		
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="../js/jquery.js"></script>		
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/bootstrap.min.js"></script>	
	<script src="../js/wow.min.js"></script>
	<script>
		wow = new WOW(
		{

		}	) 
		.init();
		var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {validateOn:["blur"]});
		var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	</script>	
</body>
</html>
<?php
mysql_free_result($chama_acc);

mysql_free_result($loans);

mysql_free_result($getLoan);
?>
