<!DOCTYPE HTML> 
<!--	
	File: login.php
	Written on: 25-Aug-2017 by Sreevilasan K
	ModificationDate	Description 				ChangedBy
	26-Aug-2017			PDO inserted				Sreevilasan K
-->
<html>
<title></title>
<head>
<style>
.copyright {
    position: absolute;
    bottom: 0;
}
</style>
</head>
<body>  
	<?php require 'include/header.php'; ?>

	<h2>Login to LAS Database</h2>
	<form method="post" action="CheckLogin.php">  
		<br><br><br><br><br><br><br><br>

		<table border="0" align="center">
			<tr>
				<td>Username: </td>
				<td><input type="text" name="username" id="username"></td>
			</tr>
			<tr>
				<td>Password: </td>
				<td><input type="password" name="pwd" id="pwd"></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="Submit"> 
				</td>
			</tr>	
			<tr>
				<td colspan="2" align="center">
						<?php
							$invalid=$_GET['invaliduser'];
							if ($invalid ==  true) {
								echo "<font color='red'>Invalid User name or password</font>";
							}
						?> 
				</td>
			</tr>	
		</table>
	</form>
	<?php require 'include/footer.php'; ?>
</body>
</html>