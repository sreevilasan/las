<?php
/*	
	File: CheckLogin.php
	Written on: 25-Aug-2017 by Sreevilasan K
	ModificationDate	Description 				ChangedBy
	26-Aug-2017			PDO inserted				Sreevilasan K
*/

$config = parse_ini_file('../../las/Database.ini');

$host = "localhost"; // Host name 
$user = "root"; // Mysql username 
$password = $config['password'];// Mysql password 
$dbname = "las"; // Database name  
$table = "users"; // Table name 

$conn2 = new mysqli($host, $user, $password);

// username and password sent from form 
$myusername=$_POST['username']; 
$mypassword=$_POST['pwd']; 
$changepassword = $_POST['changepwd'];

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysqli_real_escape_string($conn2,$myusername);
$mypassword = mysqli_real_escape_string($conn2,$mypassword);

$result1=mysqli_query($conn2, "SELECT * FROM ". $dbname . "." . $table . " WHERE username='$myusername'");
$count1=mysqli_num_rows($result1);

if ($count1 == 1) {	
	while ($row1 = $result1->fetch_assoc()){
		if($row1['Password'] == "") {
			session_start();
			$_SESSION['tempusername'] = $myusername;
			header("location:changepwd.php");
			exit;
		}
	}
}

$result=mysqli_query($conn2, "SELECT * FROM ". $dbname . "." . $table . " WHERE username='$myusername' and password='$mypassword'");

// Mysql_num_row is counting table row
$count=mysqli_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){
	// Register $myusername, $mypassword and redirect to file "login_success.php"
	session_start();
	$_SESSION['username'] = $myusername;
	$_SESSION['pwd'] = $mypassword;
	
	while($row = $result->fetch_assoc()) {
        $_SESSION['UserRole'] = $row['UserRole'];
    }
	
	
	// Connect to database
	try 
	{
		$pdo = new PDO('mysql:host=localhost;dbname='.$dbname , $user, $password);
	}
	catch (PDOException $e) 
	{
		echo 'Error: ' . $e->getMessage();
		exit();
	}
	// echo 'Connected to MySQL <br><br>';

	// Run Query
	$sql 	= "SELECT * FROM ". $dbname . ".employee WHERE empno='$myusername' limit 1"; 
	$stmt 	= $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement
	$stmt->execute();
	while ($row = $stmt->fetch())
	{
		$_SESSION['Name'] = $row['Name'];
		$_SESSION['EmpId'] = $row['EmpId'];
	}

	// Close connection
	$pdo = null;
	
	if($changepassword != "") {
		session_start();
		$_SESSION['tempusername'] = $myusername;
		header("location:changepwd.php");
		exit;
	}
	header("location:DbMain.php");
	
} else {
	header("location:login.php?invaliduser=true");
	//echo "Wrong Username or Password";
}
?>
