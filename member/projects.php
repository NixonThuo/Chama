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

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['member'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['member'], $_SESSION['MM_UserGroup'])))) {   
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

$colname_profile = "-1";
if (isset($_SESSION['member'])) {
  $colname_profile = $_SESSION['member'];
}
mysql_select_db($database_conn, $conn);
$query_profile = sprintf("SELECT * FROM member_reg WHERE username = %s", GetSQLValueString($colname_profile, "text"));
$profile = mysql_query($query_profile, $conn) or die(mysql_error());
$row_profile = mysql_fetch_assoc($profile);
$totalRows_profile = mysql_num_rows($profile);


$colname_total = $row_profile['id_no'];
mysql_select_db($database_conn, $conn);
$query_total = sprintf("SELECT mem_idno, total_cont FROM membership_fee WHERE mem_idno = %s", GetSQLValueString($colname_total, "int"));
$total = mysql_query($query_total, $conn) or die(mysql_error());
$row_total = mysql_fetch_assoc($total);
$totalRows_total = mysql_num_rows($total);

mysql_select_db($database_conn, $conn);
$query_contDate = "SELECT * FROM manage_contr";
$contDate = mysql_query($query_contDate, $conn) or die(mysql_error());
$row_contDate = mysql_fetch_assoc($contDate);
$totalRows_contDate = mysql_num_rows($contDate);

mysql_select_db($database_conn, $conn);
$query_projects = "SELECT * FROM projects";
$projects = mysql_query($query_projects, $conn) or die(mysql_error());
$row_projects = mysql_fetch_assoc($projects);
$totalRows_projects = mysql_num_rows($projects);
?>

<?php

?>
<?php
$current_date=date("Y-m-d");
$today=$current_date; 
$next=$row_contDate['next_cont'];
$diff=(strtotime($next)-strtotime($today))/(60*60*24);
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
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>	
	<header id="header">
        <?php include('../header/mem_head.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-lg-12">
					<form name="form1" method="post" action="">
							<table width="1070" border="1" align="center" id="newMember">
									<caption>ALL PROJECTS</caption>
									<tr>
											<th width="107" scope="col">Name</th>
											<th width="101" scope="col">Project Description</th>
											<th width="143" scope="col">Start date</th>
											<th width="136" scope="col">End date</th>
											<th width="154" scope="col">Amount</th>
											<th width="131" scope="col">Allocated</th>
											<th width="84" scope="col">Balance</th>
											<th width="16" scope="col">&nbsp;</th>
									</tr>
									
											<?php if ($totalRows_projects > 0) { // Show if recordset not empty ?>
													<?php do { ?>
                          <tr>
															<td><?php echo $row_projects['name']; ?></td>
															<td><?php echo $row_projects['desc']; ?></td>
															<td><?php echo $row_projects['start_date']; ?></td>
															<td><?php echo $row_projects['end_date']; ?></td>
															<td><?php echo $row_projects['amount_req']; ?></td>
															<td><?php echo $row_projects['allocated']; ?></td>
															<td><?php echo $row_projects['bal']; ?></td>
															<td>&nbsp;</td>
                              </tr>
															<?php } while ($row_projects = mysql_fetch_assoc($projects)); ?>
													<?php } // Show if recordset not empty ?>
									
							</table>
					</form>
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
	</script>	
  </body>
</html>
<?php
mysql_free_result($total);

mysql_free_result($contDate);

mysql_free_result($projects);

mysql_free_result($profile);
?>
