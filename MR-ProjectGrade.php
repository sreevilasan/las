<!DOCTYPE html>
<?php
	$page = "ManhourReport";
	// File: MR-ProjectGrade.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$fromDate="";
	$toDate = "";	
	$PrjId = "";
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
//echo "in Post";
		$fromDate=$_POST['fromDate'];
		$toDate = $_POST['toDate'];	
		$PrjId = $_POST['prjid'];
	}		
?>
<html>
<head>
<link rel="stylesheet" href="css/LasStyle.css">

<script>

function loadFromToDate() {
	today = new Date();
	if (document.getElementById("fromDate").value == "") {
		document.getElementById("fromDate").valueAsDate = new Date(today.getFullYear(),today.getMonth(),2);
	} 

	if (document.getElementById("toDate").value == "") {
		document.getElementById("toDate").valueAsDate = today;
	}
}

function quit() {
	document.location = "DbMain.php"; // go to entity main page
}

</script>


</script>
</head>
<body onload="loadFromToDate();">
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="ProjectGradeForm">
	<table class="table10" border="0"><tr><td>
		From Date:<input type="date" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>">
	</td><td style="width:30px"></td><td>
		To Date:<input type="date" name="toDate" id="toDate" value="<?php echo $toDate; ?>">
	</td><td style="width:90px"></td><td>
		Project:
	<?php
		echo '<select name="prjid" id="prjid">'. createDropDownProject("project", "prjid", "name", $PrjId) . '</select>';
	?>
	</td>
	<td><button class="button button1" type="submit" form="ProjectGradeForm" value="Run">CreateReport</button></td>
	<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
	</tr></table>
</form>

<?php
//echo "from=". $fromDate."  To=". $toDate;
	$db = new Database();	// open database

	$sql = 'select finalquery.*, HourlyRate * totalsum overalltotal  from (';
	$sql .= 'SELECT c.prjid, grade, d.HourlyRate, SUM(mhours) mhoursum, ';
    $sql .= '(SELECT SUM(mhours) FROM empmh aa, employee bb, grade dd WHERE aa.empid = bb.empid AND bb.gradeid = dd.gradeid AND prjid = c.prjid AND dd.gradeid = d.gradeid AND aa.status="A") totalsum, ';
    $sql .= 'd.HourlyRate * SUM(mhours) gradetotal FROM empmh a, employee b, project c, grade d WHERE a.empid = b.empid AND a.prjid = c.prjid AND b.gradeid = d.gradeid AND c.prjid="' . $PrjId . '" AND a.status="A" AND a.mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") AND a.mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d")';
	$sql .= 'GROUP BY c.prjid , grade ) finalquery;';

	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	echo '<br><br>';
	echo '<table class="table10" border="0"><tr><th>';
	echo '<h2>Project: ' . getProjectName($PrjId) . '</h2>';
	echo '</th></tr></table>';
	echo '<table class="table10" align="left" border="1">';
	echo '<tr class="table10 table11"><th width="80px" height="30px">Group</th><th width="120px">Current Period Manhours</th><th style="width:120px; padding:4px">Cumulative Manhours</th><th width="120px">Current Period Cost (Rs)</th><th style="width:120px; padding:4px">Cumulative Cost (Rs)</th></tr>';

	$sumHours = 0;
	$sumTotalHours = 0;
	$sumCost = 0;
	$sumTotalCost = 0;
	
	foreach ($rows as $row)
	{
		$grade = $row['grade'];
		$hours = $row['mhoursum'];
		$totalHours = $row['totalsum'];
		$cost = $row['gradetotal'];
		$totalCost = $row['overalltotal'];
		
		echo '<tr class="table10 table12"><td height="30px">' . $grade . '</td><td align="center">' . $hours . '</td><td align="center">'. $totalHours .'</td><td align="right">' . $cost . '</td><td align="right">' . $totalCost . '</td></tr>';
		$sumHours += $hours;
		$sumTotalHours += $totalHours;
		$sumCost += $cost;
		$sumTotalCost += $totalCost;
	}
	echo '<tr class="table10 table13"><th height="30px" align="right">' . "Total" . '</th><th align="center">' . $sumHours . '</th><th align="center">'. $sumTotalHours .'</th><th align="right">' . $sumCost . '<th align="right">' . $sumTotalCost . '</th></th></tr>';
	echo '</table>';

	$db->close();	// Close database  
?>
</body>
</html> 
