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
$query_contDate = "SELECT * FROM manage_contr";
$contDate = mysql_query($query_contDate, $conn) or die(mysql_error());
$row_contDate = mysql_fetch_assoc($contDate);
$totalRows_contDate = mysql_num_rows($contDate);

mysql_select_db($database_conn, $conn);
$query_contri = "SELECT * FROM member_reg WHERE category = 'Existing'";
$contri = mysql_query($query_contri, $conn) or die(mysql_error());
$row_contri = mysql_fetch_assoc($contri);
$totalRows_contri = mysql_num_rows($contri);
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
			<!--credit-->
			<?php 
              
			?>
												 
			<?php if($row_contDate['next_cont']==$current_date){; ?>
			<form action="" method="post" name="contributions" id="contributions">
					<table width="877" border="1" id="newMember" align="center">
							<caption>
									Members Contributions
									</caption>
							<tr>
									<th scope="col">Profile</th>
									<th scope="col">Firstname</th>
									<th scope="col">Lastname</th>
									<th scope="col">Acc Number</th>
									<th scope="col">Category</th>
									<th scope="col">Telephone</th>
									<th scope="col">Actions</th>
							</tr>
							
									<?php if ($totalRows_contri > 0) { // Show if recordset not empty ?>
											<?php do { ?>
											<tr>
													<td><img src="../upload/<?php echo $row_contri['photo']; ?>" width="50" height="50" class="img-circle"></td>
													<td><?php echo $row_contri['f_name']; ?></td>
													<td><?php echo $row_contri['l_name']; ?></td>
													<td><?php echo $row_contri['id_no']; ?></td>
													<td><?php echo $row_contri['category']; ?></td>
													<td><?php echo $row_contri['tel']; ?></td>
													<td><a href="mem_cont.php?id_no=<?php echo urlencode($row_contri['id_no']);?>">Contribute</a></td>
											</tr>
													<?php } while ($row_contri = mysql_fetch_assoc($contri)); ?>
											<?php } // Show if recordset not empty ?>
							
					</table>
			</form>
			<p><?php }else{
				echo "<div id='new'>";
				echo "You cannot credit contributions!!";
				echo "<br>";
				echo "Today is on ".$current_date;
				echo "<br>";
				echo "Next Contribution is on	".$row_contDate['next_cont'];
				echo "<h2>".$diff."	Days Remaining"."</h2>";
				echo "</div>";}?>
			</p>
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
	</script>	
  </body>
</html>
