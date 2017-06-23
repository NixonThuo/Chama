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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("UPDATE manage_contr SET today_ex=%s,next_cont=%s",
                       GetSQLValueString($_POST['start'], "date"),
					   GetSQLValueString($_POST['end'], "date"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());

  if(isset($Result1)){
	  //update the next contribution dates
	  $updateSQL2 = sprintf("UPDATE membership_fee SET next_date=%s",
                       GetSQLValueString($_POST['end'], "date"));

	  mysql_select_db($database_conn, $conn);
	  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
	  //update the member reg table
	  $updateSQL3 = sprintf("UPDATE member_reg SET next_date=%s WHERE category=%s",
                       GetSQLValueString($_POST['end'], "date"),
                       GetSQLValueString('Existing', "text"));

	  mysql_select_db($database_conn, $conn);
	  $Result3 = mysql_query($updateSQL3, $conn) or die(mysql_error());
	  
	  //messages
	  echo "<script> alert('DATE SUCESSFULY SET!');window.location.href='clerk_prof.php';</script>";
	 }
}

mysql_select_db($database_conn, $conn);
$query_contDates = "SELECT * FROM manage_contr ORDER BY id ASC";
$contDates = mysql_query($query_contDates, $conn) or die(mysql_error());
$row_contDates = mysql_fetch_assoc($contDates);
$totalRows_contDates = mysql_num_rows($contDates);
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
	<link rel="stylesheet" href="../calender/assets/css/jquery-ui.min.css">
  </head>
  <body>	
	<header id="header">
        <?php include('../header/clerk_head.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-md-3">
			<!--new members-->	
			<p>Previous Contribution date was : <h2><?php echo $row_contDates['today_ex']; ?></h2></p>
    		<!--new members-->
			</div>
			<div class="col-md-3">
			<!--new members-->	
			<form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
             		<table width="279" border="0" align="left" id="newMember">
             				<caption>
             						Set Contribution Dates
             						</caption>
             				<tr>
             						<th width="81" scope="col">Previous Dates</th>
             						<th width="202" scope="col">
									<label for="start" class="form-group"></label>
                                    <input type="text" name="start" id="datepicker" class="form-control" required autocomplete="off" placeholder="Select start date">
									</th>
             						</tr>
             				<tr>
             						<th>Next date</th>
             						<td>
									<label for="end" class="form-group"></label>
                                    <input type="text" name="end" id="datepicker2" class="form-control" required autocomplete="off" placeholder="Select end date">
									</td>
             						</tr>
             				<tr>
             						<td colspan="2"
									><input type="submit" name="submit" id="submit" value="Submit" class="btn btn-block"></td>
             						</tr>
             				</table>
             		<input type="hidden" name="MM_insert" value="form1">
             		</form>	
    		<!--new members-->
			</div>
			<div class="col-md-3">
			<!--credit--> 
            <p> Next Contribution is on: <h2><?php echo $row_contDates['next_cont']; ?></h2></p>
			<!--credit-->
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
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {minChars:3, validateOn:["blur"]});
	</script>	
  </body>
</html>
<?php
mysql_free_result($contDates);

mysql_free_result($user);

mysql_free_result($newMember);

mysql_free_result($getMember);

mysql_free_result($profile);
?>
