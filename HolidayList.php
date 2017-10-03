<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/LasStyle.css">
</head>

<body>
<?php
	// File: HolidayList.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	$table = 'holiday';
	$currentYear = date("Y");

	$db = new Database();	// open database

	$sql = "SELECT * FROM " . $table . " where year(hdate)='" . $currentYear . "' order by hdate;";
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	echo '<br>';
	echo '<table class="table10" align="center" border="0"><tr><th><h2>Holiday List - ' .  $currentYear . '</h1><br></th></tr></table>';
	
	echo '<table class="table10" align="center" border="1">';
	echo '<tr class="table10 table11"><th width="60px" height="30px">No</th><th width="100px">Date</th><th width="100px">Weekday</th><th style="width:200px;">Description</th></tr>';

	$i = 0;
	foreach ($rows as $row)
	{
		$i = $i + 1;
		$fdate = date_format(date_create($row['hdate']),"d-M-Y");
		$fday = date_format(date_create($row['hdate']),"l");
		$description = $row['hname'];
		
		echo '<tr class="table10 table12"><td height="30px">' . $i . '</td><td>' . $fdate . '</td><td>'. $fday .'</td><td align="left" style="width:200px;">' . $description . '</td></tr>';
	}
	echo '</table>';

	$db->close();	// Close database  
?>
</body>
</html> 
