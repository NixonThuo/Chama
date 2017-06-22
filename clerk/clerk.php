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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "credit")) {
  $insertSQL = sprintf("INSERT INTO membership_fee (`mem_fname`, `mem_lname`, `mem_idno`, `amound`,`clerk_name`,t_id) VALUES (%s, %s,%s,%s,%s,%s)",
  						GetSQLValueString($_POST['fname'], "text"),
						GetSQLValueString($_POST['lname'], "text"),
						GetSQLValueString($_POST['id_no'], "int"),
						GetSQLValueString($_POST['amound'], "int"),
                       GetSQLValueString($_POST['clerk_name'], "text"),
					   GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
  //update the member reg table
  if(isset($Result1)){
	  $existing="Existing";
	$updateSQL = sprintf("UPDATE member_reg SET category=%s,reg_fee=%s WHERE id_no=%s",
                       GetSQLValueString($existing, "text"),
                       GetSQLValueString($_POST['amound'], "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
	}
   echo "<script> alert('CREDITED!');</script>";
}

$colname_user = "-1";
if (isset($_SESSION['clerk'])) {
  $colname_user = $_SESSION['clerk'];
}
mysql_select_db($database_conn, $conn);
$query_user = sprintf("SELECT f_name, l_name, username FROM users WHERE username = %s", GetSQLValueString($colname_user, "text"));
$user = mysql_query($query_user, $conn) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$maxRows_newMember = 10;
$pageNum_newMember = 0;
if (isset($_GET['pageNum_newMember'])) {
  $pageNum_newMember = $_GET['pageNum_newMember'];
}
$startRow_newMember = $pageNum_newMember * $maxRows_newMember;

mysql_select_db($database_conn, $conn);
$query_newMember = "SELECT * FROM member_reg WHERE category = 'new'";
$query_limit_newMember = sprintf("%s LIMIT %d, %d", $query_newMember, $startRow_newMember, $maxRows_newMember);
$newMember = mysql_query($query_limit_newMember, $conn) or die(mysql_error());
$row_newMember = mysql_fetch_assoc($newMember);

if (isset($_GET['totalRows_newMember'])) {
  $totalRows_newMember = $_GET['totalRows_newMember'];
} else {
  $all_newMember = mysql_query($query_newMember);
  $totalRows_newMember = mysql_num_rows($all_newMember);
}
$totalPages_newMember = ceil($totalRows_newMember/$maxRows_newMember)-1;

$colname_getMember = "-1";
if (isset($_GET['id_no'])) {
  $colname_getMember = $_GET['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_getMember = sprintf("SELECT f_name, l_name, id_no, reg_fee FROM member_reg WHERE id_no = %s", GetSQLValueString($colname_getMember, "int"));
$getMember = mysql_query($query_getMember, $conn) or die(mysql_error());
$row_getMember = mysql_fetch_assoc($getMember);
$totalRows_getMember = mysql_num_rows($getMember);


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
			<div class="col-md-9">
				<!--new members-->
				<form name="form1" method="post" action="">
						<table width="829" border="1" align="center" id="newMember" class="table-responsive table-bordered">
								<tr>
										<th width="129" scope="col">Firsyname</th>
										<th width="114" scope="col">Lastname</th>
										<th width="63" scope="col">id no</th>
										<th width="113" scope="col">Reg date</th>
										<th width="85" scope="col">category</th>
										<th width="38" scope="col">Credit</th>
								</tr>
								
										<?php if ($totalRows_newMember > 0) { // Show if recordset not empty ?>
												<?php do { ?>
								<tr>
														<td><?php echo $row_newMember['f_name']; ?></td>
														<td><?php echo $row_newMember['l_name']; ?></td>
														<td><?php echo $row_newMember['id_no']; ?></td>
														<td><?php echo $row_newMember['reg_date']; ?></td>
														<td><?php echo $row_newMember['category']; ?></td>
														<td><a href="clerk.php?id_no=<?php echo urlencode($row_newMember['id_no']); ?>" class="btn btn-primary">Credit Acc</a></td>
								</tr>
														<?php } while ($row_newMember = mysql_fetch_assoc($newMember)); ?>
												<?php } // Show if recordset not empty ?>
								
						</table>
				</form>
<!--new members-->
			</div>
			<div class="col-md-3">
			<!--credit-->
			<form action="<?php echo $editFormAction; ?>" method="POST" name="credit" id="credit">
			<table width="216" border="0" align="right" id="creditMember">
					<caption>
							Credit Membership Fee
							</caption>
					<tr>
							<th width="122" scope="col">Member ID NO</th>
							<th width="62" scope="col"><span id="sprytextfield1">
									<label for="id_no"></label>
									<input type="text" name="id_no" id="id_no" value="<?php echo $row_getMember['id_no'];?>" readonly>
									<span class="textfieldRequiredMsg">A value is required.</span></span></th>
					</tr>
					<tr>
							<td>Enter Amount</td>
							<td><span id="sprytextfield2">
							<label for="amound"></label>
							<input type="text" name="amound" id="amound" placeholder="Registation Fee 500" maxlength="3">
							<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span></span></td>
					</tr>
					<tr>
							<td><input type="submit" name="submit" id="submit" value="Submit"></td>
							<td>&nbsp;</td>
					</tr>
					<tr style="color: red">
							<td>Note</td>
							<td>Registation fee is KSHS 500</td>
					</tr>
			</table>
			<input name="fname" type="hidden" value="<?php echo $row_getMember['f_name']; ?>">
			<input name="lname" type="hidden" value="<?php echo $row_getMember['l_name']; ?>">
			<input name="clerk_name" type="hidden" value="<?php echo $row_user['f_name']." ".$row_user['l_name']; ?>">
			<input type="hidden" name="MM_insert" value="credit">
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
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {minChars:3, validateOn:["blur"]});
	</script>	
  </body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($newMember);

mysql_free_result($getMember);

mysql_free_result($profile);
?>
