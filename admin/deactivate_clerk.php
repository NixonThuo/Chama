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

$colname_getMem = "-1";
if (isset($_GET['id_no'])) {
  $colname_getMem = $_GET['id_no'];
}
mysql_select_db($database_conn, $conn);
$query_getMem = sprintf("SELECT * FROM users WHERE id_no = %s", GetSQLValueString($colname_getMem, "int"));
$getMem = mysql_query($query_getMem, $conn) or die(mysql_error());
$row_getMem = mysql_fetch_assoc($getMem);
$totalRows_getMem = mysql_num_rows($getMem);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE `users` SET access=%s WHERE id_no=%s",
                       GetSQLValueString($_POST['action'], "int"),
                       GetSQLValueString($_POST['id_no'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
  echo "<script> alert('SUBMITED!');
    window.location.href='manage_staff.php';</script>";
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
  <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
  <link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css">
   <link rel="stylesheet" href="../calender/assets/css/jquery-ui.min.css">
   <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">  
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
  <script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
  <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
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
    
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
          <table width="424" border="1" align="center" id="newMember">
              <caption>
                  MANAGE MEMBER
                  </caption>
              <tr>
                  <td width="253">name</td>
                  <td width="155"><label for="names"></label>
                      <input name="names" type="text" id="names" value="<?php echo $row_getMem['f_name']; ?>"></td>
              </tr>
              <tr>
                  <td>id no</td>
                  <td><label for="id_no"></label>
                      <input name="id_no" type="text" id="id_no" value="<?php echo $row_getMem['id_no']; ?>"></td>
              </tr>
              <tr>
                  <td>Action</td>
                  <td><span id="spryselect1">
                      <label for="action"></label>
                      <select name="action" id="action">
                          <option value="0">Activate</option>
                          <option value="2">Deactivate</option>
                      </select>
                      <span class="selectRequiredMsg">Please select an item.</span></span></td>
              </tr>
              <tr>
                  <td>&nbsp;</td>
                  <td><input type="submit" name="submit" id="submit" value="Submit"></td>
              </tr>
          </table>
          <input type="hidden" name="MM_update" value="form1">
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
  
    } ) 
    .init();
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
  </script> 
  </body>
</html>
<?php
mysql_free_result($getMem);
?>
