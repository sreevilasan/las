<!DOCTYPE html>
<?php
	$page = "ManhourReport";
	// File: MR-DrillDown.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$fromDate="";
	$toDate = "";	
	$catagoryWise = true;
	$projectWise = true;
	
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


</script>


</script>
</head>
<body onload="loadFromToDate();">
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="ManhourGrillDownForm">
	<table class="table10" border="0"><tr>
	<td>
		From Date:<input type="date" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>">
	</td><td style="width:30px"></td>
	<td>
		To Date:<input type="date" name="toDate" id="toDate" value="<?php echo $toDate; ?>">
	</td>
	<td width="100px">
		<button class="button button2" type="submit" form="ManhourGrillDownForm" value="Run" onclick=";">CreateReport</button>
	</td></tr></table>
</form>

<?php
//echo "from=". $fromDate."  To=". $toDate;
	$db = new Database();	// open database
/*
	$sql = 'select finalquery.*, HourlyRate * totalsum overalltotal  from (';
	$sql .= 'SELECT c.prjid, grade, d.HourlyRate, SUM(mhours) mhoursum, ';
    $sql .= '(SELECT SUM(mhours) FROM empmh aa, employee bb, grade dd WHERE aa.empid = bb.empid AND bb.gradeid = dd.gradeid AND prjid = c.prjid AND dd.gradeid = d.gradeid AND aa.status="S") totalsum, ';
    $sql .= 'd.HourlyRate * SUM(mhours) gradetotal FROM empmh a, employee b, project c, grade d WHERE a.empid = b.empid AND a.prjid = c.prjid AND b.gradeid = d.gradeid AND c.prjid="' . $PrjId . '" AND a.status="S" AND a.mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") AND a.mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d")';
	$sql .= 'GROUP BY c.prjid , grade ) finalquery;';
*/

if ($catagoryWise) {
	$catagoryQuery = 'select pc.catagory as col1, pc.description as col2, SUM(mhours) as col3 from empmh a, project p, prjcat pc where a.prjid=p.prjid and p.catagory=pc.catagory AND a.status="S" AND a.mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") AND a.mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d") group by pc.catagory';
	$sql = $catagoryQuery;
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	echo '<br><br>';
	echo '<table class="table10" border="0"><tr><th>';
	echo '<h3>Catagory</h3>';
	echo '</th></tr></table>';
	echo "\n";
	
	echo '<table class="table10" align="left" border="1">';
	echo "\n";
	echo '<tr class="table10 table11"><th>No</th><th>Description</th><th>Manhours</th></tr>';
	echo "\n";
	
	$sumHours = 0;
	
	foreach ($rows as $row)
	{
		$col1 = $row['col1'];
		$col2 = $row['col2'];
		$col3 = $row['col3'];
		
		echo '<tr class="table10 table12"><td>' . $col1 . '</td><td align="left"><a href="MR-Summary.php?c='.$col1.'">' . $col2 . '</a></td><td align="center">'. $col3 .'</td>';
		echo '</tr>';
		$sumHours += $col3;
	}
	echo '<tr class="table10 table13"><th align="right" colspan="2">' . "Total" . '</th><td>'. $sumHours . '</td></tr>';
	echo "\n";
	echo '</table>';
	echo "\n";
	echo '<br>';

	$db->close();	// Close database  
}
if (projectWise) {
	$db = new Database();	// open database
	
	$projectQuery = 'select concat(p.catagory,"-", p.prjno) as col1, p.name as col2, SUM(mhours) as col3 from empmh a, project p, prjcat pc where a.prjid=p.prjid and p.catagory=pc.catagory AND a.status="S" AND a.mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") AND a.mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d") group by col1';
	$sql = $projectQuery;
//echo $sql;
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	echo '<br><br>';
	echo '<table class="table10" border="0">';
	echo '<tr><th>';
	echo '<h3>Project</h3>';
	echo '</th></tr></table>';
	echo "\n";
	
	echo '<table class="table10" align="left" border="1">';
	echo '<tr class="table10 table11"><th>No</th><th>Description</th><th>Manhours</th></tr>';
	echo "\n";
	
	$sumHours = 0;
	
	foreach ($rows as $row)
	{
		$col1 = $row['col1'];
		$col2 = $row['col2'];
		$col3 = $row['col3'];
		
		echo '<tr class="table10 table12"><td>' . $col1 . '</td><td align="left">' . $col2 . '</td><td align="center">'. $col3 .'</td>';
		echo '<td><button class="button button2" type="submit" form="ManhourGrillDownForm" value="Run" onclick=";">+</button></td>';
		echo "\n";
		$sumHours += $col3;
	}
	echo '<tr class="table10 table13"><th align="right" colspan="2">' . "Total" . '</th><td>'. $sumHours . '</td></tr>';
	echo "\n";
	echo '</table>';
	echo "\n";

	$db->close();	// Close database  
}
?>
</body>
</html> 
