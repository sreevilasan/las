<!DOCTYPE html>
<?php
	$page = "ManhourReport";
	// File: MR-EmployeeOvertime.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$fromDate=date("Y-m").'-01';
	$toDate = date("Y-m-d");
	//$EmpId = "";  commented to make the first report of the user himself

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
//echo "in Post";
		$fromDate=$_POST['fromDate'];
		$toDate = $_POST['toDate'];	
		$EmpId = $_POST['empid'];
	}	

	function calculateOverTime($idate,$ph,$nph)	{
		$OT = $ph - 8;
		if ($OT < 0) {
			$OT = 0;
		}
		if (isaHoliday($idate)) {
			$OT = $ph;
		}
		return $OT;
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

</head>
<body onload="loadFromToDate();">
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="EmployeeOvertimeForm">
	<table class="table10" border="0"><tr><th>
		From Date:<input type="date" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>">
	</th><th style="width:10px"></th><th>
		To Date:<input type="date" name="toDate" id="toDate" value="<?php echo $toDate; ?>">
	</th><th style="width:20px"></th><th>
		Employee:
	<?php
		echo '<select name="empid" id="empid">'. createDropDownEmployee("employee", "empid", "name", $EmpId) . '</select>';
	?>
	</th><th>
		<button class="button button2" type="submit" form="EmployeeOvertimeForm" value="Run">CreateReport</button>
	</th></tr></table>
</form>

<?php
//echo "from=". $fromDate."  To=". $toDate . " empid=".$EmpId;
	$db = new Database();	// open database

	$sql = 'SELECT Emp_No, Employee, mdate,  sum(Productive_Hours) as PHours, sum(Non_Productive_Hours) as NPHours, sum(mhours) as Hours ';
	$sql .= 'from tsview WHERE empid=' . $EmpId . ' AND mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") ';
	$sql .= 'AND mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d") AND status="A" ';
	$sql .= 'GROUP by Emp_No, mdate ORDER by Emp_No, mdate ;';
//echo "sql=".$sql;	
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	echo '<br><br>';
	echo '<table class="table10" border="0"><tr><th>';
	echo '<h3>Employee: <font color="blue">' . getEmployeeName($EmpId) . '</font></h3>';
	echo '</th></tr></table>';
	echo '<table class="table10" border="1">';
	echo '<tr class="table10 table11">';
	echo '<th>Date</th><th>Day</th><th width="100px">Productive Manhours</th><th width="100px">Non-Productive Manhours</th><th width="120px">Total Manhours (Including Overtime)</th><th width="100px">Overtime Hours</th></tr>';

	$sumPHours = 0.0;
	$sumNPHours = 0.0;
	$sumHours = 0.0;
	$sumOTHours = 0.0;
	foreach ($rows as $row)
	{
		$calDate = $row['mdate'];
		$day = substr($calDate, 8, 2);
		$month = substr($calDate, 5, 2);
		$year = substr($calDate,0, 4);
		$dayName = date("D",strtotime($calDate));
		$printDate = $day."-".$month."-".$year;

		$PHours = $row['PHours'];
		$NPHours = $row['NPHours'];
		$Hours = $row['Hours'];
		$OTHours = calculateOverTime($calDate,$PHours,$NPHours);
		
		echo '<tr class="table10 table12"><td height="23px">';
		if (isaHoliday($calDate)) {
			echo '<font color="red">' . $printDate . '</font></td><td><font color="red">' . $dayName . '</font>';
		} else {
			echo $printDate . '</td><td>' . $dayName;
		}
		echo '</td><td>' . $PHours . '</td><td>'. $NPHours .'</td><td>' . $Hours . '</td><td>' . $OTHours . '</td></tr>';
		
		$sumPHours = $sumPHours + $PHours;
		$sumNPHours = $sumNPHours + $NPHours;
		$sumHours = $sumHours + $Hours;
		$sumOTHours = $sumOTHours + $OTHours;
	}
	echo '<tr class="table10 table13"><th align="right" colspan="2" height="23px">' . 'Total' . '</th><th>' . $sumPHours . '</th><th>'. $sumNPHours .'</th><th>' . $sumHours . '<th>' . $sumOTHours . '</th></th></tr>';
	echo '</table>';

	$db->close();	// Close database  

?>
</body>
</html> 
