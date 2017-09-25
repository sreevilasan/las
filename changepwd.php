<?php
require 'include/commonclass.php';
require 'include/Header.php';
session_start();

if(isset($_POST['Submit']))
{
	$sql3 = "update users set password = '".$_POST['npwd']."' WHERE username='".$_SESSION['tempusername']."'";
	$db3 = new Database();
	$emprows = $db3->select($sql3);
	if ($db3->getError() != "") {
		errorExit($EmpId,$db3->getError());
	}
 
	$_SESSION['msg1']="Password Changed Successfully !!";
	header("location:login.php");
	exit();
}
?>
<html>
<head>
<script type="text/javascript">
function valid()
{
	if(document.getElementById("npwd").value=="")
	{
		alert("New Password Field is Empty !!");
		document.chngpwd.npwd.focus();
		return false;
	}
	else if(document.chngpwd.cpwd.value=="")
	{
		alert("Confirm Password Field is Empty !!");
		document.chngpwd.cpwd.focus();
		return false;
	}
	else if(document.chngpwd.npwd.value!= document.chngpwd.cpwd.value)
	{
		alert("New Password and Confirm Password do not match !!!");
		document.chngpwd.cpwd.focus();
		return false;
	}
	return true;
}
</script>
</head>
	<body>
		<br>
		<h2>Please enter a new password for your account</h2>
		<br><br><br><br>
		<p style="color:red;"><?php echo $_SESSION['msg1'];?><?php echo $_SESSION['msg1']="";?></p>
		<form name="chngpwd" action="changepwd.php" method="post" onSubmit="return valid();">
			<table align="center">
				<tr height="30">
					<td>New Password :</td>
					<td><input type="password" name="npwd" id="npwd"></td>
				</tr>
				<tr height="30">
					<td>Confirm Password :</td>
					<td><input type="password" name="cpwd" id="cpwd"></td>
				</tr>
				<tr height="50">
					<td><a href="Login.php">Back to login Page</a></td>
					<td><input type="submit" name="Submit" value="Change Password" /></td>
				</tr>
			 </table>
		</form>
	</body>
</html>

