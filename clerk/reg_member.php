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

$colname_checkExistance = "-1";
if (isset($_POST['id_no'])) {
	$colname_checkExistance =mysql_real_escape_string($_POST['id_no']);
}
mysql_select_db($database_conn, $conn);
$query_checkExistance = sprintf("SELECT id_no FROM member_reg WHERE id_no = %s", GetSQLValueString($colname_checkExistance, "int"));
$checkExistance = mysql_query($query_checkExistance, $conn) or die(mysql_error());
$row_checkExistance = mysql_fetch_assoc($checkExistance);
$totalRows_checkExistance = mysql_num_rows($checkExistance);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$fname=$lname=$gender=$email=$tel_no=$id_no=$username="";
$error1=$error2=$error3=$error4=$error5=$error6=$error7=$error8="";
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	//processing
	$fname=mysql_real_escape_string($_POST['fname']);
	$lname=mysql_real_escape_string($_POST['lname']);
	$gender=mysql_real_escape_string($_POST['Gender']);
	$id_no=mysql_real_escape_string($_POST['id_no']);
	$email=mysql_real_escape_string($_POST['email']);
	$tel_no=mysql_real_escape_string($_POST['tel']);
	$username=mysql_real_escape_string($_POST['username']);
	$pass_1=sha1($_POST['pass_1']);
	$pass_2=sha1($_POST['pass_2']);
	//image processing
	$image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
	$image_name = addslashes($_FILES['image']['name']);
	$image_size = getimagesize($_FILES['image']['tmp_name']);
	move_uploaded_file($_FILES["image"]["tmp_name"], "../upload/" . $_FILES["image"]["name"]);
	$myimage = "../upload/" . $_FILES["image"]["name"];
	//end of image processing
     //PHP validation begins
	if(is_numeric($fname) || empty($fname) || strlen($fname)<2){
		$error1="Check your firstname field";
	}elseif (is_numeric($lname) || empty($lname) || strlen($lname)<2) {
		$error2="Check your lastname field";
	}elseif (!is_numeric($gender) || empty($gender)) {
		$error3="Check your gender field";
	}elseif (is_numeric($email) || empty($email) || strlen($email)<2) {
		$error4="Check your email field";
	}elseif (!is_numeric($tel_no) || empty($tel_no) || strlen($tel_no)<10) {
		$error5="Check your mobile number field";
	}elseif ($pass_1 !==$pass_2) {
		$error6="Password mismatch";
	}elseif($totalRows_checkExistance==1){
		$error7="You are a registered member kindly login";
	}elseif (!is_numeric($id_no) || empty($id_no) || strlen($id_no)>9) {
		$error8="Check your id number field";
	}else{
		$insertSQL = sprintf("INSERT INTO member_reg (f_name,l_name,id_no, email, gender, username,password,photo,tel) VALUES (%s, %s, %s, %s,%s, %s, %s, %s,%s)",
			GetSQLValueString($fname, "text"),
			GetSQLValueString($lname, "text"),
			GetSQLValueString($id_no, "int"),
			GetSQLValueString($email, "text"),
			GetSQLValueString($gender, "text"),
			GetSQLValueString($username, "text"),
			GetSQLValueString($pass_1, "text"),
			GetSQLValueString($myimage, "text"),
			GetSQLValueString($tel_no, "int"));

		mysql_select_db($database_conn, $conn);
		$Result1 = mysql_query($insertSQL, $conn) or die(mysql_error());

		$insertGoTo = "index.php";
		if (isset($_SERVER['QUERY_STRING'])) {
			$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
			$insertGoTo .= $_SERVER['QUERY_STRING'];
		}
		header(sprintf("Location: %s", $insertGoTo));
	}
}

mysql_select_db($database_conn, $conn);
$query_Recordset1 = "SELECT * FROM member_reg";
$Recordset1 = mysql_query($query_Recordset1, $conn) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);


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
</head>
<body>	
	<header id="header">
		<?php include('../header/clerk_head.php');?>		
	</header><!--/header-->	
	<hr>
	
	<div class="services">
		<div class="container">
			<h2 align="center">Create Account</h2>
			<div class="col-md-12"><?php if(isset($_POST['submit'])){?>
				<p class="align-center alert alert-danger"><br>
					<?php echo $error1."".$error2."".$error3."".$error4."".$error5."".$error6."".$error7."".$error8; ?></p>
					<?php }?></div>
					<div class="col-md-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="300ms">
						<form name="form1" method="POST" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
							<p><span id="sprytextfield1" class="form-group">
								<label for="fname">Firstname</label>
								<input type="text" name="fname" id="fname" class="col-lg-3 form-control" maxlength="30" value="<?php echo $fname;?>">
								<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span></span></p>
								<p><span id="sprytextfield2" class="form-group">
									<label for="lname">Lastname</label>
									<input type="text" name="lname" id="lname" class="col-lg-3 form-control" maxlength="30" value="<?php echo $lname;?>">
									<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span></span></p>
									<p><table width="200">
										<tr>
											<td><p><label>
												<input type="radio" name="Gender" value="1" id="Gender_0">
												Male</label></p></td>
											</tr>
											<tr>
												<td><p><label>
													<input type="radio" name="Gender" value="0" id="Gender_1">
													Female</label></p></td>
												</tr>
											</table>
										</div>
										<div class="col-md-6 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="600ms">	
										</p>
										<p><span id="sprytextfield3" class="form-group">
											<label for="id_no">Id number</label>
											<input type="text" name="id_no" id="id_no" class="col-lg-3 form-control" maxlength="10" value="<?php echo $id_no;?>">
											<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></p>
											<p><span id="sprytextfield4" class="form-group">
												<label for="email">Email Address</label>
												<input type="text" name="email" id="email" class="col-lg-3 form-control" maxlength="40" value="<?php echo $email;?>">
												<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span></span></p>
												<p><span id="sprytextfield5" class="form-group">
													<label for="tel">Telephone Number</label>
													<input type="text" name="tel" id="tel" class="col-lg-3 form-control" maxlength="10" value="<?php echo $tel_no;?>">
													<span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldMinCharsMsg">Minimum number of characters not met.</span><span class="textfieldMaxCharsMsg">Exceeded maximum number of characters.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></p>
													<p><span id="sprytextfield9" class="form-group">
														<label for="image">Browse Photo</label>
														<input type="file" name="image" id="image" class="col-lg-3 form-control">
														<span class="textfieldRequiredMsg">A value is required.</span></span></p>
														
													</div>
													<div class="col-md-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="1200ms">
														
														<p><span id="sprytextfield6" class="form-group">
															<label for="username">Username</label>
															<input type="text" name="username" id="username" class="col-lg-3 form-control" maxlength="30" value="<?php echo $username;?>">
															<span class="textfieldRequiredMsg">A value is required.</span></span></p>
															<p><span id="sprytextfield7" class="form-group">
																<label for="pass_1">Password</label>
																<input type="password" name="pass_1" id="pass_1" class="col-lg-3 form-control" maxlength="10">
																<span class="textfieldRequiredMsg">A value is required.</span></span></p>
																<p><span id="sprytextfield8" class="form-group">
																	<label for="pass_2">Confrim Password</label>
																	<input type="password" name="pass_2" id="pass_2" class="col-lg-3 form-control" maxlength="10">
																	<span class="textfieldRequiredMsg">A value is required.</span></span></p>
																	<p>
																		<input type="submit" name="submit" id="submit" value="Register">
																		<br>
																	</p>
																	<input type="hidden" name="MM_insert" value="form1">
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
														var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {minChars:2, maxChars:30, validateOn:["blur"]});
														var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {minChars:2, maxChars:30, validateOn:["blur"]});
														var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {minChars:6, maxChars:10, validateOn:["blur"]});
														var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur"], maxChars:50, minChars:2});
														var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {minChars:10, maxChars:10, validateOn:["blur"]});
														var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur"]});
														var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "none", {validateOn:["blur"]});
														var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "none", {validateOn:["blur"]});
														var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9");
													</script>	
												</body>
												</html>
												<?php
												mysql_free_result($Recordset1);

												mysql_free_result($checkExistance);
												?>
