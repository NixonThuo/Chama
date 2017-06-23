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
$query_nextDate = "SELECT * FROM manage_contr";
$nextDate = mysql_query($query_nextDate, $conn) or die(mysql_error());
$row_nextDate = mysql_fetch_assoc($nextDate);
$totalRows_nextDate = mysql_num_rows($nextDate);

mysql_select_db($database_conn, $conn);
$query_chama = "SELECT * FROM chama_acc";
$chama = mysql_query($query_chama, $conn) or die(mysql_error());
$row_chama = mysql_fetch_assoc($chama);
$totalRows_chama = mysql_num_rows($chama);

$maxRows_mem_contr = 10;
$pageNum_mem_contr = 0;
if (isset($_GET['pageNum_mem_contr'])) {
	$pageNum_mem_contr = $_GET['pageNum_mem_contr'];
}
$startRow_mem_contr = $pageNum_mem_contr * $maxRows_mem_contr;

mysql_select_db($database_conn, $conn);
$query_mem_contr = "SELECT * FROM membership_fee ORDER BY id ASC";
$query_limit_mem_contr = sprintf("%s LIMIT %d, %d", $query_mem_contr, $startRow_mem_contr, $maxRows_mem_contr);
$mem_contr = mysql_query($query_limit_mem_contr, $conn) or die(mysql_error());
$row_mem_contr = mysql_fetch_assoc($mem_contr);

if (isset($_GET['totalRows_mem_contr'])) {
	$totalRows_mem_contr = $_GET['totalRows_mem_contr'];
} else {
	$all_mem_contr = mysql_query($query_mem_contr);
	$totalRows_mem_contr = mysql_num_rows($all_mem_contr);
}
$totalPages_mem_contr = ceil($totalRows_mem_contr/$maxRows_mem_contr)-1;

mysql_select_db($database_conn, $conn);
$query_loan = "SELECT * FROM membership_fee WHERE loan = 1 ORDER BY id ASC";
$loan = mysql_query($query_loan, $conn) or die(mysql_error());
$row_loan = mysql_fetch_assoc($loan);
$totalRows_loan = mysql_num_rows($loan);

mysql_select_db($database_conn, $conn);
$query_newMem = "SELECT * FROM member_reg WHERE category = 'new'";
$newMem = mysql_query($query_newMem, $conn) or die(mysql_error());
$row_newMem = mysql_fetch_assoc($newMem);
$totalRows_newMem = mysql_num_rows($newMem);
?>

<?php
//require('../fpdf.php');
require('../fpdf/mysql_table.php');
class PDF extends PDF_MySQL_Table{
	function Header(){
    //$this->Image("../images/sample_signature",10,6,30);
		$this->SetFont('Arial','B',18);
    //$this->SetFillColor(230,230,0);
		$this->SetTextColor(221,50,50);
		$this->Cell(0,6,"KAWA SELF HELP GROUP",0,1,'C');
		$this->SetFont('Arial','B',14);
		$this->Cell(0,6,"P.O BOX 555 KAWANGWARE",0,1,'C');
		$this->SetFont('Arial','I',9);
		$this->Cell(0,6,"Tel:25255555",0,1,'C');
		$this->Cell(0,6,"E-mail:kawaselfhelpgroup@gmail.com",0,1,'C');
		$this->Ln(6);
		$this->Cell(0,6,"Member Contribution History ",0,1,'C');
    // Line break
		parent::Header();

	}

// footer
	function Footer()
	{
    // Position at 1.5 cm from bottom
		$this->SetY(-15);
    // Arial italic 8
		$this->SetFont('Arial','I',8);
    // Page number
		$this->Cell(0,6,"Taking you to your destiny",0,1,'C');
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

}


//PRINTING OUT DATA
mysql_connect('localhost','root','');
mysql_select_db('chama_group');

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(255,0,128);
$pdf->Table($query_chama);
$pdf->Ln(50);

$pdf->Output("../pdf/savings.pdf","F");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Kawa Self Help Group</title>

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
			<!--<div class="col-md-12" align="center"><?php echo "<h2>"."Next meeting will be on ".$row_nextDate['next_cont']."</h2>"; ?></div>-->
			<div class="col-md-12">
				<!--new members-->	
				<form action="" method="post" name="contribution" id="contribution">
					<table  class = "table table-striped" width="200" border="1" id="newMember">
						<caption>
							CHAMA ACCOUNT
						</caption>
						<tr>
							<th scope="col">Total Saving</th>
							<th scope="col"><?php echo $row_chama['acc_total']; ?></th>
						</tr>
						<tr>
							<td> Chama Account Balance</td>
							<td><?php echo $row_chama['acc_bal']; ?></td>
						</tr>
						<tr>
							<td>Total Loans</td>
							<td><?php echo $row_chama['loan']; ?></td>
						</tr>
						<tr>
							<td>Funded Projects</td>
							<td><?php echo $row_chama['project']; ?></td>
						</tr>
						<tr>
							<td>Total Interest</td>
							<td>&nbsp;<?php echo $row_chama['payed_interest']; ?></td>
						</tr>
						<tr style="color:#F00">
							<td>New Balance</td>
							<td><?php echo $row_chama['acc_bal']; ?></td>
						</tr>
					</table>
					<!--new members-->
					<ul class="pager">
						<li><a href="../pdf/savings.pdf">Print</a></li>
					</ul>
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
mysql_free_result($nextDate);

mysql_free_result($chama);

mysql_free_result($mem_contr);

mysql_free_result($loan);

mysql_free_result($newMem);

mysql_free_result($user);

mysql_free_result($newMember);

mysql_free_result($getMember);

mysql_free_result($profile);
?>
