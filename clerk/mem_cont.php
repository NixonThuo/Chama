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
$colname_user = "-1";
if (isset($_SESSION['clerk'])) {
  $colname_user = $_SESSION['clerk'];
}
mysql_select_db($database_conn, $conn);
$query_user = sprintf("SELECT * FROM users WHERE username = %s", GetSQLValueString($colname_user, "text"));
$user = mysql_query($query_user, $conn) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user); 

$colname_mem_cont = "-1";
if (isset($_GET['id_no'])) {
  $colname_mem_cont = $_GET['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_mem_cont = sprintf("SELECT * FROM membership_fee WHERE mem_idno = %s ORDER BY id ASC", GetSQLValueString($colname_mem_cont, "int"));
$mem_cont = mysql_query($query_mem_cont, $conn) or die(mysql_error());
$row_mem_cont = mysql_fetch_assoc($mem_cont);
$totalRows_mem_cont = mysql_num_rows($mem_cont);

mysql_select_db($database_conn, $conn);
$query_chama_acc = "SELECT * FROM chama_acc";
$chama_acc = mysql_query($query_chama_acc, $conn) or die(mysql_error());
$row_chama_acc = mysql_fetch_assoc($chama_acc);
$totalRows_chama_acc = mysql_num_rows($chama_acc);

$colname_loan = "-1";
if (isset($_GET['id_no'])) {
  $colname_loan = $_GET['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_loan = sprintf("SELECT * FROM loan_request WHERE id_no = %s", GetSQLValueString($colname_loan, "int"));
$loan = mysql_query($query_loan, $conn) or die(mysql_error());
$row_loan = mysql_fetch_assoc($loan);
$totalRows_loan = mysql_num_rows($loan);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
/*
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if($row_mem_cont['loan']==1){
	$contribute=($row_loan['to_pay']-$_POST['amound']);
	$payed_loan=$_POST['amound'];
	}else{
	$contribute=$_POST['amound'];
	$payed_loan=0;
	}
  $insertSQL = sprintf("INSERT INTO contributions (fname, lname, id_no, amound, payed_to,payed_loan) VALUES (%s, %s, %s, %s, %s,%s)",
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['id_no'], "int"),
                       GetSQLValueString($contribute, "int"),
                       GetSQLValueString($_POST['payed_to'], "text"),
					   GetSQLValueString($payed_loan, "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
	//update the members total amound
	if(isset($Result1)){
	$total=($row_mem_cont['total_cont']+$_POST['amound']);
	$no_of_cont=($row_mem_cont['no_of_cont']+1);
	$updateSQL = sprintf("UPDATE membership_fee SET total_cont=%s,no_of_cont=%s WHERE mem_idno=%s",
                       GetSQLValueString($total, "int"),
					   GetSQLValueString($no_of_cont, "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
 		//update chama account
	$chama_total=($row_chama_acc['acc_total']+$contribute);
	$chama_balance=($row_chama_acc['acc_bal']+$contribute);
	$chama_loan=($row_chama_acc['loan']-$payed_loan);
	$updateSQL2 = sprintf("UPDATE chama_acc SET acc_total=%s,acc_bal=%s,loan=%s",
                       GetSQLValueString($chama_total, "int"),
					   GetSQLValueString($chama_balance, "int"),
					   GetSQLValueString($chama_loan, "int"));

  mysql_select_db($database_conn, $conn);
  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
  	//update the loan table
	$to_pay=($row_loan['to_pay']-$payed_loan);
	$updateSQL3 = sprintf("UPDATE loan_request SET payed=%s,to_pay=%s WHERE id_no=%s",
                       GetSQLValueString($payed_loan, "int"),
					   GetSQLValueString($to_pay, "int"),
					   GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());
	}
	echo "<script> alert('SAVED!');</script>";
}*/

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if($row_mem_cont['loan']==1){
	$contribute=($row_loan['to_pay']-$_POST['amound']);
	$payed_loan=$_POST['amound'];
	$fine_member=$_POST['Fine'];
	
	 $insertSQL = sprintf("INSERT INTO contributions (fname, lname, id_no, amound, payed_to,payed_loan, Fine) VALUES (%s, %s, %s, %s, %s,%s, %s)",
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['id_no'], "int"),
                       GetSQLValueString($contribute, "int"),
                       GetSQLValueString($_POST['payed_to'], "text"),
					   GetSQLValueString($payed_loan, "int"),
	 					GetSQLValueString($fine_member, "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
  
  //update the members total amound
	$total=($row_mem_cont['total_cont']+$_POST['amound']);
	$no_of_cont=($row_mem_cont['no_of_cont']+1);
	$updateSQL = sprintf("UPDATE membership_fee SET total_cont=%s,no_of_cont=%s WHERE mem_idno=%s",
                       GetSQLValueString($total, "int"),
					   GetSQLValueString($no_of_cont, "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
 		//update chama account
	$chama_total=($row_chama_acc['acc_total']+$contribute);
	$chama_balance=($row_chama_acc['acc_bal']+$contribute);
	$chama_loan=($row_chama_acc['loan']-$payed_loan);
	$updateSQL2 = sprintf("UPDATE chama_acc SET acc_total=%s,acc_bal=%s,loan=%s",
                       GetSQLValueString($chama_total, "int"),
					   GetSQLValueString($chama_balance, "int"),
					   GetSQLValueString($chama_loan, "int"));

  mysql_select_db($database_conn, $conn);
  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
  	//update the loan table
	$to_pay=($row_loan['to_pay']-$payed_loan);
	$updateSQL3 = sprintf("UPDATE loan_request SET payed=%s,to_pay=%s WHERE id_no=%s",
                       GetSQLValueString($payed_loan, "int"),
					   GetSQLValueString($to_pay, "int"),
					   GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());

	echo "<script> alert('SAVED!');</script>";
  
	}else{
	$contribute=$_POST['amound'];
	$payed_loan=0;
	$fine_member=$_POST['Fine'];
	 $insertSQL = sprintf("INSERT INTO contributions (fname, lname, id_no, amound, payed_to,payed_loan, Fine) VALUES (%s, %s, %s, %s, %s,%s, %s)",
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['id_no'], "int"),
                       GetSQLValueString($contribute, "int"),
                       GetSQLValueString($_POST['payed_to'], "text"),
					   GetSQLValueString($payed_loan, "int"),
	 				   GetSQLValueString($fine_member, "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
  
  //update the members total amound
	$total=($row_mem_cont['total_cont']+$_POST['amound']);
	$no_of_cont=($row_mem_cont['no_of_cont']+1);
	$updateSQL = sprintf("UPDATE membership_fee SET total_cont=%s,no_of_cont=%s WHERE mem_idno=%s",
                       GetSQLValueString($total, "int"),
					   GetSQLValueString($no_of_cont, "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
 		//update chama account
	$chama_total=($row_chama_acc['acc_total']+$contribute);
	$chama_balance=($row_chama_acc['acc_bal']+$contribute);
	$chama_loan=($row_chama_acc['loan']-$payed_loan);
	$updateSQL2 = sprintf("UPDATE chama_acc SET acc_total=%s,acc_bal=%s,loan=%s",
                       GetSQLValueString($chama_total, "int"),
					   GetSQLValueString($chama_balance, "int"),
					   GetSQLValueString($chama_loan, "int"));

  mysql_select_db($database_conn, $conn);
  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
  	//update the loan table
	$to_pay=($row_loan['to_pay']-$payed_loan);
	$updateSQL3 = sprintf("UPDATE loan_request SET payed=%s,to_pay=%s WHERE id_no=%s",
                       GetSQLValueString($payed_loan, "int"),
					   GetSQLValueString($to_pay, "int"),
					   GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());

	echo "<script> alert('SAVED!');</script>";
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
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
  </head>
  <body>	
	<header id="header">
        <?php include('../header/clerk_head.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-md-12">
			<!--contrution history-->
			<table class="table table-striped" width="200" border="1" id="newMember">
				<caption>Member Contribution History</caption>
					<tr>
							<th scope="col">Member name</th>
							<th scope="col"><?php echo $row_mem_cont['mem_fname']; ?></th>
					</tr>
					<tr>
							<td>id no</td>
							<td><?php echo $row_mem_cont['mem_idno']; ?></td>
					</tr>
					<tr>
							<td>Total Contribution</td>
							<td><?php echo $row_mem_cont['total_cont']; ?></td>
					</tr>
					<tr>
							<td>no of contribution</td>
							<td><?php echo $row_mem_cont['no_of_cont']; ?></td>
					</tr>
			</table>
			</div>
			<div class="col-md-4">
			<table class="table table-striped" width="200" border="1" align="center" id="newMember">
				<caption>Member Loan History</caption>
					<tr>
							<th scope="col">Member name</th>
							<th scope="col">&nbsp;<?php echo $row_loan['names']; ?></th>
					</tr>
					<tr>
							<td>Loan</td>
							<td>&nbsp;<?php echo $row_loan['to_pay']; ?></td>
					</tr>
					<tr>
							<td>due date</td>
							<td>&nbsp;<?php echo $row_loan['due_date']; ?></td>
					</tr>
					<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
					</tr>
			</table>
			<!--contrution history-->
			</div>
			<div class="col-md-4 col-md-offset-4">
			<!--credit-->
			<form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
					<table  class="table table-striped" width="200" border="0" align="center" id="newMember">
							<tr>
									<th scope="col">Member Acc No</th>
									<th scope="col"><span id="sprytextfield1">
											<label for="id_no"></label>
											<input name="id_no" type="text" id="id_no" value="<?php echo $row_mem_cont['mem_idno']; ?>" readonly>
											<span class="textfieldRequiredMsg">A value is required.</span></span></th>
							</tr>
							<tr>
									<td>amound</td>
									<td><span id="sprytextfield2">
									<label for="amount"></label>
									<input type="text" name="amound" id="amound">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span></span></td>
							</tr>
							<tr>
									<td>Fine</td>
									<td><span id="sprytextfield3">
									<label for="Fine"></label>
									<input type="text" name="Fine" id="Fine">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span></span></td>
							</tr>
							<tr>
									<td colspan="2"><input type="submit" name="submit" id="submit" value="Save"></td>
									</tr>
					</table>
					<p>
							<input name="fname" type="hidden" id="fname" value="<?php echo $row_mem_cont['mem_fname']; ?>">
					</p>
					<p>
							<input name="lname" type="hidden" id="lname" value="<?php echo $row_mem_cont['mem_lname']; ?>">
					</p>
					<p>
							<input name="payed_to" type="hidden" id="payed_to" value="<?php echo $row_user['f_name']."".$row_user['l_name']; ?>">
					</p>
					<input type="hidden" name="MM_insert" value="form1">
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
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {minChars:2, maxChars:10, validateOn:["blur"]});
	</script>	
  </body>
</html>
