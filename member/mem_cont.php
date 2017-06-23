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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_contr = 10;
$pageNum_contr = 0;
if (isset($_GET['pageNum_contr'])) {
  $pageNum_contr = $_GET['pageNum_contr'];
}
$startRow_contr = $pageNum_contr * $maxRows_contr;


$colname_contr = $row_profile['id_no'];
mysql_select_db($database_conn, $conn);
$query_contr = sprintf("SELECT id_no, amound, date_ex, payed_to,payed_loan FROM contributions WHERE id_no = %s", GetSQLValueString($colname_contr, "int"));
$query_limit_contr = sprintf("%s LIMIT %d, %d", $query_contr, $startRow_contr, $maxRows_contr);
$contr = mysql_query($query_limit_contr, $conn) or die(mysql_error());
$row_contr = mysql_fetch_assoc($contr);

if (isset($_GET['totalRows_contr'])) {
  $totalRows_contr = $_GET['totalRows_contr'];
} else {
  $all_contr = mysql_query($query_contr);
  $totalRows_contr = mysql_num_rows($all_contr);
}
$totalPages_contr = ceil($totalRows_contr/$maxRows_contr)-1;

$queryString_contr = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_contr") == false && 
      stristr($param, "totalRows_contr") == false) {
      array_push($newParams, $param);
  }
}
if (count($newParams) != 0) {
  $queryString_contr = "&" . htmlentities(implode("&", $newParams));
}
}
$queryString_contr = sprintf("&totalRows_contr=%d%s", $totalRows_contr, $queryString_contr);
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
$pdf->Table($query_contr);
$pdf->Ln(50);

$pdf->Output("../pdf/history.pdf","F");
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
     <div class="col-md-12">
      
       <form action="" method="post" name="contribution" id="contribution">
         <table width="585" border="1" align="center" id="existing">
           <caption>
             Member Contribution History
           </caption>
           <tr>
             <th scope="col">Date</th>
             <th scope="col">Amount</th>
             <th scope="col">Payed to</th>
             <th scope="col">id number</th>
           </tr>
           <?php if ($totalRows_contr > 0) { // Show if recordset not empty ?>
           <?php do { ?>
           <tr>
             <td>&nbsp;<?php echo $row_contr['date_ex']; ?></td>
             <td>&nbsp;<?php echo $row_contr['amound']; ?></td>
             <td>&nbsp;<?php echo $row_contr['payed_to']; ?></td>
             <td>&nbsp;<?php echo $row_contr['id_no']; ?></td>
           </tr>
           <?php } while ($row_contr = mysql_fetch_assoc($contr)); ?>
           <?php } // Show if recordset not empty ?>
           <tr>
             <td>Total Contribution</td>
             <td colspan="3">&nbsp;</td>
           </tr>
         </table>
         <ul class="pager">
           <li><a href="<?php printf("%s?pageNum_contr=%d%s", $currentPage, max(0, $pageNum_contr - 1), $queryString_contr); ?>">Previous</a></li>
           <li><a href="<?php printf("%s?pageNum_contr=%d%s", $currentPage, min($totalPages_contr, $pageNum_contr + 1), $queryString_contr); ?>">Next</a></li>
           <li><a href="../pdf/history.pdf">Print</a></li>
         </ul>
       </form>
     </div>
   </div>			
 </div>	
 <div class="sub-footer">
  <?php include('../footer/sub_footer.php')?>		
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
mysql_free_result($contr);
?>
