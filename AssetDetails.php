<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/LasStyle.css">
</head>

<body>
<?php
	// File: AssetDetails.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	//$table = 'asset';
	$currentYear = date("Y");

	$db = new Database();	// open database

	$sql = "SELECT a.type class, a.subtype, b.description item, sum(a.quantity) as qty FROM asset a, assetsubtype b where a.subtype=b.subtype group by a.type, a.subtype order by a.type,a.subtype";
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	echo '<br>';
	echo '<table class="table10" align="center" border="0"><tr><th><h2>Asset List</h1></th></tr></table>';
	
	echo '<table class="table10" align="center" border="1">';
	echo '<tr class="table10 table11"><th>No</th><th>Type</th><th>Description</th><th>Quantity</th></tr>';

	$i = 0;
	foreach ($rows as $row)
	{
		$i = $i + 1;
		$class = $row['class'];
		$description = $row['item'];
		$quantity = $row['qty'];
		
		echo '<tr class="table10 table12"><td>' . $i . '</td><td>' . $class . '</td><td align="left">' . $description . '</td><td align="right">'. $quantity .'</td></tr>';
	}
	echo '</table>';

	$db->close();	// Close database  
?>
</body>
</html> 
