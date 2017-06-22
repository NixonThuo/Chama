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



mysql_select_db($database_conn, $conn);
$query_loans = "SELECT * FROM loan_request WHERE payed = 0 ORDER BY id ASC";
$loans = mysql_query($query_loans, $conn) or die(mysql_error());
$row_loans = mysql_fetch_assoc($loans);
$totalRows_loans = mysql_num_rows($loans);

$colname_getLoan =$colname_gett_id= "-1";
if (isset($_GET['id_no'])) {
  $colname_getLoan = $_GET['id_no'];
  $colname_gett_id=$_GET['t_id'];
}
mysql_select_db($database_conn, $conn);
$query_getLoan = sprintf("SELECT * FROM loan_request WHERE id_no = %s AND t_id=%s", GetSQLValueString($colname_getLoan, "int"),GetSQLValueString($colname_gett_id, "int"));
$getLoan = mysql_query($query_getLoan, $conn) or die(mysql_error());
$row_getLoan = mysql_fetch_assoc($getLoan);
$totalRows_getLoan = mysql_num_rows($getLoan);

$colname_get_mem = "-1";
if (isset($_GET['id_no'])) {
  $colname_get_mem = $_GET['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_get_mem = sprintf("SELECT * FROM membership_fee WHERE mem_idno = %s", GetSQLValueString($colname_get_mem, "int"));
$get_mem = mysql_query($query_get_mem, $conn) or die(mysql_error());
$row_get_mem = mysql_fetch_assoc($get_mem);
$totalRows_get_mem = mysql_num_rows($get_mem);
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "payment")) {
	if(($row_getLoan['approved']==1) && ($row_getLoan['payed']!==$row_get_mem['loan_amound'])){
	//if the loan was approved
	$amound=$_POST['amound'];
	$to_pay=$row_getLoan['to_pay']-$amound;
	$paid=$row_getLoan['payed']+$amound;
	$balance=($to_pay-$paid);	
	//update the loans table
	$updateSQL = sprintf("UPDATE loan_request SET to_pay=%s, payed=%s,penalty=%s WHERE id_no=%s",
                       GetSQLValueString($to_pay, "int"),
					   GetSQLValueString($paid, "int"),
                       GetSQLValueString($_POST['penalty'], "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
  //update the chamas accound
  		$acc_balance=($row_chama_acc['acc_bal']+$amound+$_POST['penalty']);
		$loan_balance=($row_chama_acc['loan']-$amound);
		$payed_interest2=$row_chama_acc['payed_interest']+$_POST['payed_interest'];
	$updateSQL3 = sprintf("UPDATE chama_acc SET `acc_bal`=%s,`loan`=%s,payed_interest=%s",
					   GetSQLValueString($acc_balance, "int"),
					   GetSQLValueString($loan_balance, "int"),
					   GetSQLValueString($payed_interest2, "int"));

  	mysql_select_db($database_conn, $conn);
  	$Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());
	
	
	if($_POST['mybalance']==$amound){
	$updateSQL = sprintf("UPDATE loan_request SET approved=%s,setted=%s WHERE id_no=%s",
					   GetSQLValueString(3, "int"),
                       GetSQLValueString(1, "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
  //update the mem reg fee table
  $updateSQL3 = sprintf("UPDATE membership_fee SET `loan`=%s,`approve`=%s,`loan_amound`=%s WHERE mem_idno=%s",
					   GetSQLValueString(0, "int"),
                       GetSQLValueString(0, "int"),
					   GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());
	}
	}
echo "<script> alert('PAYED');
window.location.href='clerk_prof.php';
</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ruai Chama Group</title>

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
			<div class="col-md-6">
			<!--new members-->	
			<form action="" method="post" name="loan_summary" id="loan_summary">
					<table width="285" border="1" align="center" id="newMember">
							<caption>
									LOAN SUMMARY
									</caption>
							<tr>
									<th width="121" scope="col">Mem ID no</th>
									<th width="148" scope="col"><?php echo $row_getLoan['id_no']; ?></th>
							</tr>
							<tr>
									<td>Loan date</td>
									<td><?php echo $row_getLoan['borrow_date']; ?></td>
							</tr>
							<tr>
									<td>due date</td>
									<td><?php echo $row_getLoan['due_date']; ?></td>
							</tr>
							<tr>
									<td>Loan Isuued</td>
									<td><?php echo $row_getLoan['award']; ?></td>
							</tr>
							<tr>
									<td>To pay</td>
									<td><?php echo $row_get_mem['loan_amound']; ?></td>
							</tr>
							<tr>
									<td>Interest</td>
									<td><?php echo $interest=($row_get_mem['loan_amound']-$row_getLoan['award']); ?></td>
							</tr>
							<tr>
									<td>Payed</td>
									<td><?php echo $row_getLoan['payed']; ?></td>
							</tr>
							<tr style="color: red">
									<td>Balance</td>
									<td><?php echo $mybalance=($row_get_mem['loan_amound']-$row_getLoan['payed']); ?></td>
							</tr>
							<tr>
									<td>penalties</td>
									<td><?php 
									$days_diff=(strtotime($row_getLoan['due_date'])-strtotime($today))/(60*60*24);
									$days_delay=$days_diff*$days_diff;
									if((($row_getLoan['due_date']==$today) || $days_diff<0) &&$row_getLoan['penalty']!==0){
										  $penalty=($days_delay)*100;
										}else{
											$penalty=0;
										}
									echo "Penalty= ".$penalty;
									?></td>
							</tr>
							<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
							</tr>
					</table>
			</form>
			<!--new members-->
			</div>
			<div class="col-md-3">
			<!--new members-->	
			<form action="<?php echo $editFormAction; ?>" method="POST" name="payment" id="payment">
					<table width="218" border="0" id="newMember">
						<caption>
									PAYMENTS
									</caption>
							<tr>
									<th scope="col">Member id No</th>
									<th scope="col"><label for="id_no"></label>
											<input name="id_no" type="text" id="id_no" value="<?php echo $row_getLoan['id_no']; ?>" readonly></th>
							</tr>
							<tr>
									<td>Amound</td>
									<td><span id="sprytextfield1">
									<label for="amound"></label>
									<input type="text" name="amound" id="amound">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed.</span></span></td>
							</tr>
							<tr>
									<td>Penalty</td>
									<td><label for="penalty"></label>
											<span id="sprytextfield2">
											<label for="penalty"></label>
											<input type="text" name="penalty" id="penalty" value="<?php echo $penalty;?>" readonly>
											<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed</span></span></td>
							</tr>
							<tr>
									<td><input type="reset" name="reset" id="reset" value="Cancel"></td>
									<td><input type="submit" name="submit" id="submit" value="PAY"></td>
							</tr>
							<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
							</tr>
					</table>
					<input name="mybalance" type="hidden" value="<?php echo $mybalance?>">
					<?php $payed_interest=($amound-$interest);?>
					<input name="payed_interest" type="hidden" value="<?php echo $payed_interest;?>">
					<input type="hidden" name="MM_update" value="payment">
			</form>
			<!--new members-->
			
			</div>
			<div class="col-md-3">
			<!--credit-->
			
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
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["blur"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {validateOn:["blur"]});
	</script>	
  </body>
</html>
<?php
mysql_free_result($chama_acc);

mysql_free_result($get_mem);
?>
