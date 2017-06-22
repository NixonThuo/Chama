<?php require_once('../Connections/conn.php'); ?>
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
$query_Recordset1 = "SELECT * FROM member_reg";
$Recordset1 = mysql_query($query_Recordset1, $conn) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

$failed_login="";
if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=sha1($_POST['pass']);
  $MM_fldUserAuthorization = "access";
  $MM_redirectLoginSuccess = "member_prof.php";
  $MM_redirectLoginFailed = "index.php";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_conn, $conn);
  	
  $LoginRS__query=sprintf("SELECT username, password, access FROM member_reg WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $conn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'access');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['member'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    $failed_login="wrong password and username";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Member Login</title>

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
        <?php include('../header/header.php');?>		
    </header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<div class="col-md-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="300ms">
			<!--saving info-->
			<table width="270" height="184" border="0" id="newMember">
					<caption>
							SAVING INFO
							</caption>
					<tr>
							<td width="240"><a href="create_account.php">Create account </a>with us</td>
							</tr>
					<tr>
							<td>Save more than 3 Times <strong>any amound</strong></td>
							</tr>
					<tr>
							<td>Apply a loan</td>
					</tr>
					<tr>
							<td>Pay your loan and enjoy saving with us</td>
					</tr>
			</table>
<!--saving info-->
			</div>
			<div class="col-md-6">
			<h2 align="center">Member Login</h2>
      <div id="failed_login" style="color:#FFF;text-align:center;background-color:#CC333F"><?php echo $failed_login;?></div>
					<form action="<?php echo $loginFormAction; ?>" method="POST" name="mem_login" id="mem_login">
							<table width="486" border="0" align="center" id="newMember">
									<tr>
											<th scope="col"><p>Username</p></th>
											<th scope="col"><span id="sprytextfield1" class="form-group">
													<label for="username"></label>
													<input type="text" name="username" id="username" class="form-control">
												<span class="textfieldRequiredMsg">Please Enter Username.</span></span></th>
									</tr>
									<tr>
											<td><p>Password</p></td>
											<td><span id="sprytextfield2" class="form-group">
													<label for="pass"></label>
													<input type="password" name="pass" id="pass" class="form-control">
												<span class="textfieldRequiredMsg">Please Provide A Login Password.</span></span></td>
									</tr>
									<tr>
											<td><input type="submit" name="login" id="login" value="Login"></td>
											<td>&nbsp;</td>
									</tr>
									<tr>
											<td>&nbsp;</td>
											<td></td>
									</tr>
							</table>
					</form>
			</div>
			<div class="col-md-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="1200ms">
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
  <script src="../js/myjs.js"></script>
	<script>
wow = new WOW(
	 {
	
		}	) 
		.init();
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
	</script>	
  </body>
</html>
<?php
mysql_free_result($Recordset1);
?>
