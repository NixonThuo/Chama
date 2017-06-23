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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "events")) {
  $insertSQL = sprintf("INSERT INTO projects (`name`, `amount_req`, `desc`, `start_date`, `end_date`) VALUES (%s,%s,%s,%s,%s)",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['amound'], "int"),
					   GetSQLValueString($_POST['decr'], "text"),
					   GetSQLValueString($_POST['start'], "date"),
					   GetSQLValueString($_POST['end'], "date"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());

  $insertGoTo = "admin.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
$query_project = "SELECT * FROM projects";
$project = mysql_query($query_project, $conn) or die(mysql_error());
$row_project = mysql_fetch_assoc($project);
$totalRows_project = mysql_num_rows($project);
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
		
			<div class="col-md-12">
			<!--new members-->
			<form name="form1" method="post" action="">
					<table width="50%" border="1" align="right" id="newMember" class="table table-responsive">
							<caption>
									Project Allocation
									</caption>
							<tr>
									<th scope="col">Name</th>
									<th scope="col">Decription</th>
									<th scope="col">Amount</th>
									<th scope="col">Allocated</th>
									<th scope="col">balance</th>
									<th scope="col">Total Allocated</th>
									<th scope="col">Start date</th>
									<th scope="col">Complition date</th>
									<th scope="col">Action</th>
							</tr>
							
									<?php if ($totalRows_project > 0) { // Show if recordset not empty ?>
											<?php do { ?>
											<tr>
													<td><?php echo $row_project['name']; ?></td>
													<td><?php echo $row_project['desc']; ?></td>
													<td><?php echo $row_project['amount_req']; ?></td>
													<td><?php echo $row_project['allocated']; ?></td>
													<td><?php echo $balance=($row_project['amount_req']-$row_project['allocated']); ?></td>
													<td><?php echo $row_project['total']; ?></td>
													<td><?php echo $row_project['start_date']; ?></td>
													<td><?php echo $row_project['end_date']; ?></td>
													<td>
													<?php if($row_project['amount_req']!==$row_project['total']){?>
													<a href="allocate.php?name=<?php echo $row_project['name']; ?>">Allocate</a></td>									<?php }else{echo "Fully Funded";}?>
													</tr>
													<?php } while ($row_project = mysql_fetch_assoc($project)); ?>
											<?php } // Show if recordset not empty ?>
							
					</table>
			</form>
			<!--new members-->
			</div>
				<div class="col-md-12">
			<!--new members-->	
			<form action="<?php echo $editFormAction; ?>" method="POST" name="events" id="events">
					<table width="300" border="0" id="newMember" align="center">
							<caption>
									NEW PROJECT
									</caption>
							<tr>
									<td width="96" scope="col">Project name</td>
									<td width="240" scope="col"><span id="sprytextfield1">
											<label for="name"></label>
											<input type="text" name="name" id="name">
											<span class="textfieldRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>Amount required</td>
									<td><span id="sprytextfield2">
											<label for="amound"></label>
											<input type="text" name="amound" id="amound">
											<span class="textfieldRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>Description</td>
									<td><span id="sprytextarea1">
											<label for="decr"></label>
											<textarea name="decr" id="decr" cols="45" rows="5"></textarea>
											<span class="textareaRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>Start date</td>
									<td> <label for="start" class="form-group"></label>
                                    <input type="text" name="start" id="datepicker" class="form-control" required autocomplete="off" placeholder="Select start date"></td>
							</tr>
							<tr>
									<td>Completion date</td>
									<td><label for="end" class="form-group"></label>
                                    <input type="text" name="end" id="datepicker2" class="form-control" required autocomplete="off" placeholder="Select end date"></td>
							</tr>
							<tr>
									<td>&nbsp;</td>
									<td><input type="submit" name="submit" id="submit" value="Submit"></td>
							</tr>
					</table>
					<input type="hidden" name="MM_insert" value="events">
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
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
	</script>	
  </body>
</html>
<?php
mysql_free_result($profile);

mysql_free_result($project);

mysql_free_result($profile);
?>
