<?php
require 'include/commonclass.php';
session_start();

if(isset($_POST['Submit']))
{
	$sql3 = "update users set password = '".$_POST['npwd']."' WHERE username='".$_SESSION['username']."'";
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
		alert("New Password Filed is Empty !!");
		document.chngpwd.npwd.focus();
		return false;
	}
	else if(document.chngpwd.cpwd.value=="")
	{
		alert("Confirm Password Filed is Empty !!");
		document.chngpwd.cpwd.focus();
		return false;
	}
	else if(document.chngpwd.npwd.value!= document.chngpwd.cpwd.value)
	{
		alert("Password and Confirm Password Field do not match  !!");
		document.chngpwd.cpwd.focus();
		return false;
	}
	return true;
}
</script>
</head>
	<body>
		<br><br><br><br><br><br><br><br><br><br>
		<p style="color:red;"><?php echo $_SESSION['msg1'];?><?php echo $_SESSION['msg1']="";?></p>
		<form name="chngpwd" action="changepwd.php" method="post" onSubmit="return valid();">
			<table align="center">
				<tr height="50">
					<td>New Password :</td>
					<td><input type="password" name="npwd" id="npwd"></td>
				</tr>
				<tr height="50">
					<td>Confirm Password :</td>
					<td><input type="password" name="cpwd" id="cpwd"></td>
				</tr>
				<tr>
					<td><a href="Login.php">Back to login Page</a></td>
					<td><input type="submit" name="Submit" value="Change Password" /></td>
				</tr>
			 </table>
		</form>
	</body>
</html>

