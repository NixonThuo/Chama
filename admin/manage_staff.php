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

$colname_checkExistance = "-1";
if (isset($_POST['id_no'])) {
  $colname_checkExistance = $_POST['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_checkExistance = sprintf("SELECT * FROM users WHERE id_no = %s", GetSQLValueString($colname_checkExistance, "int"));
$checkExistance = mysql_query($query_checkExistance, $conn) or die(mysql_error());
$row_checkExistance = mysql_fetch_assoc($checkExistance);
$totalRows_checkExistance = mysql_num_rows($checkExistance);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$id_exist="";
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "staff")) {
	$pass=sha1($_POST['id_no']);
	if($totalRows_checkExistance==0){
  $insertSQL = sprintf("INSERT INTO users ( `f_name`, `l_name`, `id_no`, `username`, `password`) VALUES (%s, %s,%s, %s,%s)",
  						GetSQLValueString($_POST['fname'], "text"),
						GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['id_no'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
					   GetSQLValueString($pass, "text"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
1	}else{
	$id_exist="This member exists";	
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

mysql_select_db($database_conn, $conn);
$query_clerks = "SELECT * FROM users WHERE `all` = 0";
$clerks = mysql_query($query_clerks, $conn) or die(mysql_error());
$row_clerks = mysql_fetch_assoc($clerks);
$totalRows_clerks = mysql_num_rows($clerks);

mysql_select_db($database_conn, $conn);
$query_member = "SELECT * FROM membership_fee ORDER BY id ASC";
$member = mysql_query($query_member, $conn) or die(mysql_error());
$row_member = mysql_fetch_assoc($member);
$totalRows_member = mysql_num_rows($member);


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
			<div class="col-md-3">
			<!--new members-->	
			<div style="color:#FFF;text-align:center;background-color:#CC333F" id="failed_login"><?php echo $id_exist;?></div>
			<form action="<?php echo $editFormAction; ?>" method="POST" name="staff" id="staff">
					<table width="200" border="0" id="newMember">
					
							<caption>
									Clerk Registration
									</caption>
							<tr>
									<td width="119">Firstname</td>
									<td width="71"><span id="sprytextfield1">
											<label for="fname"></label>
											<input type="text" name="fname" id="fname">
											<span class="textfieldRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>Lastname</td>
									<td><span id="sprytextfield2">
											<label for="lname"></label>
											<input type="text" name="lname" id="lname">
											<span class="textfieldRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>id_no</td>
									<td><span id="sprytextfield3">
									<label for="id_no"></label>
									<input type="text" name="id_no" id="id_no">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></td>
							</tr>
							<tr>
									<td>username</td>
									<td><span id="sprytextfield4">
											<label for="username"></label>
											<input type="text" name="username" id="username">
											<span class="textfieldRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td><input type="hidden" name="pass" id="pass"></td>
									<td><input type="submit" name="submit" id="submit" value="Submit"></td>
							</tr>
					</table>
					<input type="hidden" name="MM_insert" value="staff">
			</form>
			<hr>
			<hr>
			<!--new members-->
			</div>
			<div class="col-md-9">
			<!--new members-->
			
		<table width="929" border="1" id="newMember" class="table table-responsive">
				<caption>
						CLERKS
						</caption>
				<tr>
						<td width="141">firstname</td>
						<td width="152">lastname</td>
						<td width="124">idno</td>
						<td width="79">Status</td>
						<td width="73">Action</td>
				</tr>
				<?php if ($totalRows_clerks > 0) { // Show if recordset not empty ?>
						<?php do { ?>
						<tr>
								<td><?php echo $row_clerks['f_name']; ?></td>
								<td><?php echo $row_clerks['l_name']; ?></td>
								<td><?php echo $row_clerks['id_no']; ?></td>
								<td style="color:#30F"><?php if($row_clerks['access']==0){echo "Active";}else{echo "Inactive!!";}; ?></td>
								<td><a href="deactivate_clerk.php?id_no=<?php echo $row_clerks['id_no']; ?>">Action</a></td>
								</tr>
						
								<?php } while ($row_clerks = mysql_fetch_assoc($clerks)); ?>
								<?php } // Show if recordset not empty ?>
								<tr style="color:#F00">
								<td>NOTE</td>
								<td colspan="4">DEACTIVATE CLERK ACCOUNTS IF HE/SHE IS FIRED</td>
								</tr>
				
		</table>
		
<HR>

		<table width="144%" border="1" id="newMember" class="table  table-responsive">
				<caption>
						Members
						</caption>
				<tr>
						<td width="140">Firstname</td>
						<td width="138">Lastname</td>
						<td width="129">id no</td>
						<td width="131">Total savings</td>
						<td width="149">Loan</td>
						<td width="138">contributions</td>
						<td width="17">&nbsp;</td>
				</tr>
				<?php if ($totalRows_member > 0) { // Show if recordset not empty ?>
						<?php do { ?>
						<tr>
								<td><?php echo $row_member['mem_fname']; ?></td>
								<td><?php echo $row_member['mem_lname']; ?></td>
								<td><?php echo $row_member['mem_idno']; ?></td>
								<td><?php echo $row_member['total_cont']; ?></td>
								<td><?php echo $row_member['loan_amound']; ?></td>
								<td><?php echo $row_member['no_of_cont']; ?></td>
								<td>
								<?php if($row_member['no_of_cont']<3 || $row_member['loan_amound']!=0){?>
								<a href="deactivate_mem.php?id_no=<?php echo $row_member['mem_idno']?>">Deactivate Accout</a>
								<?php }elseif($row_member['loan_amound']!=0){?>
								Unpaid Loan
								<?php }else{?>
								Good
								<?php }?>
								</td>
								</tr>
						
								<?php } while ($row_member = mysql_fetch_assoc($member)); ?>
								<?php } // Show if recordset not empty ?>
								<tr style="color:#F00">
								<td>note</td>
								<td colspan="6" >members accounts will be deactivated if they havent contributed 3 times or they have unpaid loans<br>
										deactivating accounts means resticting logiins for the members</td>
								</tr>
		</table>
		

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
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
	</script>
		
  </body>
</html>
<?php
mysql_free_result($profile);

mysql_free_result($clerks);

mysql_free_result($member);

mysql_free_result($checkExistance);
?>
