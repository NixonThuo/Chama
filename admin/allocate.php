<?php require_once('../Connections/conn.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1";
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
if (!((isset($_SESSION['admin'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['admin'], $_SESSION['MM_UserGroup'])))) {   
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

$colname_getProject = "-1";
if (isset($_GET['name'])) {
  $colname_getProject = $_GET['name'];
}
mysql_select_db($database_conn, $conn);
$query_getProject = sprintf("SELECT * FROM projects WHERE name = %s", GetSQLValueString($colname_getProject, "text"));
$getProject = mysql_query($query_getProject, $conn) or die(mysql_error());
$row_getProject = mysql_fetch_assoc($getProject);
$totalRows_getProject = mysql_num_rows($getProject);

mysql_select_db($database_conn, $conn);
$query_accChama = "SELECT * FROM chama_acc";
$accChama = mysql_query($query_accChama, $conn) or die(mysql_error());
$row_accChama = mysql_fetch_assoc($accChama);
$totalRows_accChama = mysql_num_rows($accChama);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if(($_POST['available']<$row_accChama['acc_bal'])){
		//updates
	$p_balance=($row_getProject['amount_req']-$row_getProject['total']);
	$total=($row_getProject['allocated']+$_POST['available']);
  $updateSQL = sprintf("UPDATE projects SET allocated=%s,bal=%s,total=%s WHERE name=%s",
                       GetSQLValueString($_POST['available'], "int"),
					   GetSQLValueString($p_balance, "int"),
					   GetSQLValueString($total, "int"),
                       GetSQLValueString($_POST['name'], "text"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

	//update chama acccount balance
	$balance=(($row_accChama['acc_bal'])-($_POST['available']));
	$project=($row_accChama['project'])+($_POST['available']);
	$updateSQL2 = sprintf("UPDATE chama_acc SET project=%s,acc_bal=%s",
                       GetSQLValueString($project, "int"),
                       GetSQLValueString($balance,"int"));

  mysql_select_db($database_conn, $conn);
  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
  
  $updateGoTo = "admin.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
	}else{
	//
	}
}


$colname_profile = "-1";
if (isset($_SESSION['admin'])) {
  $colname_profile = $_SESSION['admin'];
}
mysql_select_db($database_conn, $conn);
$query_profile = sprintf("SELECT * FROM users WHERE username = %s", GetSQLValueString($colname_profile, "text"));
$profile = mysql_query($query_profile, $conn) or die(mysql_error());
$row_profile = mysql_fetch_assoc($profile);
$totalRows_profile = mysql_num_rows($profile);

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
	<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css">
   <link rel="stylesheet" href="../calender/assets/css/jquery-ui.min.css">	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
  <script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>

  </head>
  <body>	
	<header id="header">
        <?php include('../header/admin_head.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-md-4">
			<table width="200" border="1" id="newMember" align="center">
		<caption>
				Chama Account
		</caption>
		<tr>
				<td>Total Balance</td>
				<td><?php echo $row_accChama['acc_bal']; ?></td>
		</tr>
		<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
		</tr>
		<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
		</tr>
		<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
		</tr>
</table>

			<hr>
					<table width="252" border="1" align="center" id="newMember">
							<caption>
									CALCULATIONS
								</caption>
							
							<tr>
									<td>Total Chama Balance</td>
									<td><?php echo $row_accChama['acc_bal']; ?></td>
							</tr>
							<tr>
									<th width="154" scope="col">Required for Project</th>
									<th width="82" scope="col"><?php echo $row_getProject['amount_req']; ?></th>
							</tr>
							<tr>
									<td>Total allocated</td>
									<td><?php echo $row_getProject['total']; ?></td>
							</tr>
							<tr>
									<td>Balance</td>
									<td style="color:#F00"><?php echo $mybalance=($row_getProject['amount_req']-$row_getProject['total'])?></td>
							</tr>
							<tr>
									<td>1st time Alloctions</td>
									<td><?php  //echo $available=($row_accChama['acc_bal']*0.4); ?></td>
							</tr>
					</table>
					
			</div>
			<div class="col-md-6">
			<!--new members--></h2>
			<form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
					<table width="200" border="1" align="center" id="newMember">
							<caption>
									PROJECT ALLOCATION
									</caption>
							<tr>
									<th scope="col">name</th>
									<th scope="col"><label for="name"></label>
											<input name="name" type="text" id="name" value="<?php echo $row_getProject['name']; ?>" readonly></th>
							</tr>
							<tr>
									<td>amound</td>
									<td><label for="amound"></label>
											<input name="amound" type="text" id="amound" value="<?php echo $row_getProject['amount_req']; ?>" readonly></td>
							</tr>
							<tr>
									<td>Available</td>
									<td><span id="sprytextfield1">
									<label for="allocate"></label>
									<input type="text" name="available" id="available" value="">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></td>
							</tr>
							<tr>
									<td><input name="balance" type="hidden" value="<?php echo $mybalance;?>"></td>
									<td><input type="submit" name="submit" id="submit" value="Submit"></td>
							</tr>
					</table>
					
					<input type="hidden" name="MM_update" value="form1">
			</form>
			<!--new members-->
			</div>
		</div>			
	</div>	
	<div class="sub-footer">
		<?php include('../footer/sub_footer.php');?>		
	</div>
	<!--calender-->
  <script src="../js/jquery.js"></script> 
  <!--<script src="../calender/assets/js/jquery-1.8.2.min.js"></script>-->
  <script src="../calender/assets/js/jquery-ui.min.js"></script>
   <script>
  $(document).ready(function() {
    $("#datepicker").datepicker({dateFormat:'yy/mm/dd'}); 
  });
  $(document).ready(function() {
    $("#datepicker2").datepicker({dateFormat:'yy/mm/dd'}); 
  });
  </script>
  <!--calender-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->		
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>	
	<script src="../js/wow.min.js"></script>
	<script>
wow = new WOW(
	 {
	
		}	) 
		.init();
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
	</script>	
  </body>
</html>
<?php
mysql_free_result($profile);

mysql_free_result($getProject);

mysql_free_result($accChama);

mysql_free_result($project);

mysql_free_result($profile);
?>
