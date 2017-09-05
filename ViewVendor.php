<!DOCTYPE html>
<html>
<body>
<img src="images/las-logo_left.png">
<h1>Company Details</h1>

<?php 
	$user = "root";
	$password = "Las1";
	$dbname ="sree";
	$table = "vendor";
	$company = $_POST["company"];
	$service = $_POST["service"];
?>

<?php
// echo "welcome to LAS Database";
// echo '<br>';
try 
{
	$pdo = new PDO('mysql:host=localhost;dbname=' . $dbname, $user, $password);
}
catch (PDOException $e) 
{
    echo 'Error: ' . $e->getMessage();
    exit();
}
// echo 'Connected to MySQL <br>';
// echo '<br><br>';

// Run Query
$sql 	= 'SELECT * FROM ' . $dbname . '.' . $table . ' ';
if ($company != "" || $service != "") {
	$sql .= 'where ';
}
if ($company != "") {
	$sql .= 'vendor_name like "%'. $company . '%" ' ;
}
if ($company != "" && $service != "") {
	$sql .= ' and ';
}
if ($service != "") {
	$sql .= 'vendor_services like "%'. $service . '%" ' ;
}

$sql .= 'order by vendor_name;';
// echo 'sql=' . $sql;

$stmt 	= $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement
$stmt->execute();
$nfield = $stmt->columnCount();

// echo 'nfield=' . $nfield;
$n=0;
while ($row = $stmt->fetch())
{
	echo(++$nv . ". &nbsp;");
	echo ' <font color = "blue"><strong>' . $row['Vendor_Name']. '</strong></font><br>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp' . $row['Vendor_Contact_Person'] . '&nbsp; &nbsp; &nbsp ' . $row['Mobile_No'] . '&nbsp; &nbsp; &nbsp' . $row['Vendor _Email'] . '<br>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp' . $row['Vendor_Address'] . '<br>';
	// echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp' . 'Email: '. $row['Vendor _Email'] . '<br>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp' . 'Service: '. $row['Vendor_Services'] . '<br>';
	//for ($i=0; $i < $nfield; $i++) {
	//	echo $row[$i]. '; ';
	//}  
	echo "-----------------------------------------------------------------<br>";
}

// Close connection
$pdo = null;
   
?>
</body>
</html> 




