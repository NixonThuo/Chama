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


$colname_loan_status =$row_profile['id_no'];
mysql_select_db($database_conn, $conn);
$query_loan_status = sprintf("SELECT * FROM loan_request WHERE id_no = %s ORDER BY t_id DESC", GetSQLValueString($colname_loan_status, "int"));
$loan_status = mysql_query($query_loan_status, $conn) or die(mysql_error());
$row_loan_status = mysql_fetch_assoc($loan_status);
$totalRows_loan_status = mysql_num_rows($loan_status);
?>

<?php

?>
<?php
$today=date("Y-m-d");; 
$next=$row_contDate['next_cont'];
$diff=(strtotime($next)-strtotime($today))/(60*60*24);
$due_date=$row_loan_status['due_date'];
$rem_date=(strtotime($due_date)-strtotime($today))/(60*60*24);
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
     <tr>
        <th>Loan settled</th>
        <th><span class="badge"><?php echo $row_loan_status['setted']; ?></span></th>
    </tr>
</table>

			<?php }else{?>
			<div id="new">Pay Kshs 500 to Become an Active Member</div>
			<?php }?>
			</div>
			<div class="col-lg-6">
			<!--loan processing-->
			<?php 
			$pecentage=(($row_total['total_cont']/$row_total['target'])*100);
			$loan=($row_total['total_cont']+(0.16*$row_total['target'])+(0.08*$row_chama_acc['acc_total']));
			$intrest=($loan*0.025);
			$payment=$loan+$intrest;
			if($row_total['loan']==0){?>
			<?php 
			if(($row_total['no_of_cont']>=3) && ($loan<$row_chama_acc['acc_total']) || $row_loan_status['setted']==1){
			echo "<div id='qualify_loan'>";
			echo "You qualify for a loan of ";
			echo $loan."</br>";
			echo "<h2>"."<a href='apply_loan.php' style='color:red'>APPLY LOAN</a>"."</h2>";
			echo "</div>";
			}else{
			echo "<div id='new'>";
			echo "For you to qualify for a loan you should have atleast contributed 3 times"."</br>";
			echo "</div>";	
			}?>
			<?php 
			}elseif($row_total['approve']==1 && $row_loan_status['setted']==0){?>
			<table width="306" border="0" align="center" id="newMember">
		<tr>
				<th width="145" scope="row">Loan Awarded</th>
				<td width="105">Ksh <?php echo $row_loan_status['to_pay']+$row_loan_status['payed']; ?></td>
		</tr>
		<tr style="color:red">
				<th width="145" scope="row">loan Balance</th>
				<td width="105">Ksh <?php echo $row_loan_status['to_pay']; ?></td>
		</tr>
		<tr>
				<th width="145" scope="row">payed</th>
				<td width="105">Ksh <?php echo $row_loan_status['payed']; ?></td>
		</tr>
		<tr>
				<th scope="row">appoved date</th>
				<td><?php echo $row_loan_status['borrow_date']; ?></td>
		</tr>
		<tr>
				<th scope="row">Due Date</th>
				<td><?php echo $row_loan_status['due_date']; ?></td>
		</tr>
		<tr>
				<th scope="row">You have</th>
				<td style="color:red"><?php echo $rem_date;?> Days Left</td>
		</tr>
</table>
	
			<?php }else if($row_total['approve']==2){
			echo "<div id='process_loan'>";
			echo "<h2>"."Your loan was diclined or chama account balance is insufficient"."</h2>";
			echo "<br>";
			echo "<h2>"."<a href='apply_loan.php' style='color:red'>Apply loan again</a>"."</h2>";
			echo "</div>";
			}else{
			echo "<div id='process_loan'>";
			echo "<h2>"."Your Loan is Being Processed"."</h2>";
			echo "</div>";
			}?>
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
	</script>	
  </body>
</html>
<?php
mysql_free_result($total);

mysql_free_result($contDate);

mysql_free_result($chama_acc);

mysql_free_result($loan_status);

mysql_free_result($profile);
?>
