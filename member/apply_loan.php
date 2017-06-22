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
$query_total = sprintf("SELECT * FROM membership_fee WHERE mem_idno = %s", GetSQLValueString($colname_total, "int"));
$total = mysql_query($query_total, $conn) or die(mysql_error());
$row_total = mysql_fetch_assoc($total);
$totalRows_total = mysql_num_rows($total);

mysql_select_db($database_conn, $conn);
$query_contDate = "SELECT * FROM manage_contr";
$contDate = mysql_query($query_contDate, $conn) or die(mysql_error());
$row_contDate = mysql_fetch_assoc($contDate);
$totalRows_contDate = mysql_num_rows($contDate);

mysql_select_db($database_conn, $conn);
$query_chama_acc = "SELECT * FROM chama_acc";
$chama_acc = mysql_query($query_chama_acc, $conn) or die(mysql_error());
$row_chama_acc = mysql_fetch_assoc($chama_acc);
$totalRows_chama_acc = mysql_num_rows($chama_acc);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$loan_t_id=($row_total['t_id']+1);
	$t_id=$loan_t_id+1;
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "apply_loan")) {
  $insertSQL = sprintf("INSERT INTO loan_request (`names`, `id_no`, `request_amound`, `add_more`,`req_reason`,`me`,`t_id`) VALUES (%s, %s,%s,%s,%s,%s,%s)",
                       GetSQLValueString($_POST['names'], "text"),
                       GetSQLValueString($_POST['id_no'], "int"),
					   GetSQLValueString($_POST['amound'], "int"),
                       GetSQLValueString($_POST['add_more'], "int"),
					   GetSQLValueString($_POST['reason'], "text"),
                       GetSQLValueString($_POST['me'], "int"),
					   GetSQLValueString($loan_t_id, "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());
	//update the membership table
		$updateSQL2 = sprintf("UPDATE membership_fee SET loan=%s,t_id=%s WHERE mem_idno=%s",
                       GetSQLValueString(1, "int"),
					    GetSQLValueString($_POST['total_t_id'], "int"),
                       GetSQLValueString($_POST['id_no'], "text"));

	  mysql_select_db($database_conn, $conn);
	  $Result2 = mysql_query($updateSQL2, $conn) or die(mysql_error());
	  
  $insertGoTo = "member_prof.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


?>
<?php
$current_date=date("Y-m-d");
$today=$current_date; 
$next=$row_contDate['next_cont'];
$diff=(strtotime($next)-strtotime($today))/(60*60*24);



$pecentage=(($row_total['total_cont']/$row_total['target'])*100);
$loan=($row_total['total_cont']+(0.16*$row_total['target'])+(0.08*$row_chama_acc['acc_total']));
$intrest=($loan*0.025);
$payment=$loan+$intrest;
if(($row_total['no_of_cont']>=3) && ($loan<$row_chama_acc['acc_total'])){
}else{	
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
	<link href="../SpryAssets/SpryValidationConfirm.css" rel="stylesheet" type="text/css">
	<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css">	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../SpryAssets/SpryValidationConfirm.js" type="text/javascript"></script>
  <script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
  </head>
  <body>	
	<header id="header">
        <?php include('../header/mem_head.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-lg-3">
			<?php if(($row_profile['category']!='New') && ($row_profile['reg_fee']!=0)){?>
			<div id="existing">Your are now an ACTIVE Member</div>
			<table width="200" border="0" id="newMember">
		<tr>
				<th scope="col">Next Contribution Date</th>
				<th scope="col"><?php echo $row_contDate['next_cont'];?></th>
		</tr>
		<tr>
				<th>Remaining Days</th>
				<th style="color:#F00"><?php  if($diff==0){echo "Its Today";}else{echo $diff;};?></th>
		</tr>
		<tr>
				<th>Account Balance</th>
				<th>Kshs <?php echo $row_total['total_cont']; ?></th>
		</tr>
    <tr>
        <th>No of Contributions</th>
        <th><span class="badge"><?php echo $row_total['no_of_cont']; ?></span></th>
    </tr>
</table>

			<?php }else{?>
			<div id="new">Pay Kshs 500 to Become an Active Member</div>
			<?php }?>
			</div>
			<div class="col-lg-6">
			<!--loan processing-->
			<form action="<?php echo $editFormAction; ?>" method="POST" name="apply_loan" id="apply_loan">
					<table width="438" border="0" align="center" id="newMember">
							<caption>
									Loan Application Form
									</caption>
							<tr>
									<td width="120" scope="col">Amound you Qualify</td>
									<td width="64" scope="col"><label for="amound"></label>
											<input type="text" name="amound" id="amound" value="<?php echo $loan?>" readonly></td>
							</tr>
							<tr>
									<td>Add More</td>
									<td><label for="add_more"></label>
											<input type="text" name="add_more" id="add_more"></td>
							</tr>
							<tr>
									<td>Reason</td>
									<td><label for="reason"></label>
											<span id="sprytextarea1">
											<label for="reason"></label>
											<textarea name="reason" id="reason" cols="45" rows="5" required></textarea>
											<span class="textareaRequiredMsg">A value is required.</span></span></td>
							</tr>
							<tr>
									<td>This is me applying for loan</td>
									<td><input type="checkbox" name="me" id="me" required value="1">
											<label for="me"></label></td>
							</tr>
							<tr>
									<td>&nbsp;</td>
									<td><input type="submit" name="submit" id="submit" value="Submit">
									<input name="total_t_id" type="text" value="<?php echo $t_id;?>" readonly></td>
							</tr>
							<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
							</tr>
					</table>
					<input type="hidden" name="names" value="<?php echo $row_profile['f_name']." ".$row_profile['l_name']?>">
					<input name="id_no" type="hidden" value="<?php echo $row_profile['id_no']; ?>">
					<input type="hidden" name="MM_insert" value="apply_loan">
			</form>
			<!--end of loan processing-->	
			</div>
			<div class="col-lg-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="1200ms">
			<table width="293" border="1" align="right">
		<tr> 
				<th colspan="2" scope="col"><p><img src="../upload/<?php echo $row_profile['photo']; ?>" width="200" height="200" class="img-circle"> </th>
				</tr>
		<tr>
				<td><p><?php echo $row_profile['f_name']; ?></p></td>
				<td><p><?php echo $row_profile['l_name']; ?></p></td>
		</tr>
</table>

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
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
	</script>	
  </body>
</html>
<?php
mysql_free_result($total);

mysql_free_result($contDate);

mysql_free_result($chama_acc);

mysql_free_result($profile);
?>
