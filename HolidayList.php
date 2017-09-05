<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/manhour.css">
</head>

<body>
<?php
	// File: HolidayList.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	$table = 'holiday';

	$db = new Database();	// open database

	$sql = 'SELECT * FROM ' . $table . ' order by hdate;';
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	echo '<h2>Holiday List</h1><br>';
	echo '<table class="table table2" align="left" border="1">';
	echo '<tr class="table table1"><th width="60px" height="30px">No</th><th width="100px">Date</th><th width="100px">Weekday</th><th style="width:200px; padding:10px">Description</th></tr>';
	//$bs = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	//echo $bs,'<font color = "blue"><strong>'. 'No' . $bs . 'Date' . $bs . 'Weekday' . $bs . $bs .'Description'.'</strong></font>';
	//echo '<br>';
	//echo  "<br>--------------------------------------------------------------------------------<br>";
	$i = 0;
	foreach ($rows as $row)
	{
		$i = $i + 1;
		$fdate = date_format(date_create($row['hdate']),"d-M-Y");
		$fday = date_format(date_create($row['hdate']),"l");
		$description = $row['hname'];
		
		echo '<tr><th height="30px">' . $i . '</th><th>' . $fdate . '</th><th>'. $fday .'</th><th align="left" style="width:200px; padding:10px">' . $description . '</th></tr>';
		
		//echo $bs.$i;
		//echo $bs . $fdate;
		//echo $bs . $fday;
		//echo $bs .$bs. $row['hname']; 
		//echo  "<br>--------------------------------------------------------------------------------<br>";
	}
	echo '</table>';

	$db->close();	// Close database  
?>
</body>
</html> 
