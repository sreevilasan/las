<!DOCTYPE html>
<?php
	$page = "ManhourReport";
	// File: MR-Summary.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$fromDate="";
	$toDate = "";	
	$nf = 0;
	$field[] = "";
	$calcHour = "Hours";
	$calcCost = "";
	
//print_r($_POST);

	// get data from POST variables
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$fromDate=$_POST['fromDate'];
		$toDate = $_POST['toDate'];	
		
		$nf = 0;
        foreach($_POST['outArray'] as $fld) {
			$field[$nf] = $fld;
            $nf = $nf +1;
        }
		$calcHour = $_POST['calcHour'];
		$calcCost = $_POST['calcCost'];
	}	
?>
<html>
<head>
<link rel="stylesheet" href="css/LasStyle.css">

<script src="js/jqd-jquery.min.js"></script>
<script src="js/jqd-pair-select.min.js"></script>
<script src="js/jqd-main.js"></script>

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

</head>
<body onload="loadFromToDate();">

<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="MR-SummaryForm">
	<table class="table10" border="0">
		<td>
			From Date:<input type="date" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>">
		</td><td style="width:30px"></td>
		<td>
			To Date:<input type="date" name="toDate" id="toDate" value="<?php echo $toDate; ?>">
		</td>
		<td><button class="button button1" type="submit" form="MR-SummaryForm" value="Run" onclick=";">CreateReport</button></td>
		<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
	
	<br>
	<table class="table10" border="0"><tr><td>
		<b>Select Fields:</b>
	</td></tr></table>
	
	<table class="table10" border="0"><tr><td width="50px"></td><td>
		<select name="InArray[]" id="MasterSelectBox" multiple size="6" style="min-width: 150px;float:left;padding:8px;">
			<option value="Cat_No"> Cat_No </option>
			<option value="Category"> Category </option>
			<option value="Prj_No"> Prj_No </option>
			<option value="Project"> Project </option>
			<option value="Project_Description"> Project_Description</option>
			<option value="Project_Type"> Project_Type </option>
			<option value="Dept"> Dept </option>
			<option value="Department"> Department </option>
			<option value="Activity"> Activity </option>
			<option value="Grade"> Grade </option>
			<option value="Emp_No"> Emp_No </option>
			<option value="Employee"> Employee </option>
			<option value="Client"> Client </option>
			<option value="Company"> Company </option>
			<option value="Country"> Country </option>
		</select>	
	</td><td>
		<div style="float:left;margin:10px;">
			<button type="button" id="btnAdd">>></button><br>
			<button type="button" id="btnRemove"><<</button>
		</div>
	</td><td>
		<select name="outArray[]" id="PairedSelectBox" multiple  size="6" style="min-width: 150px;float:left;padding:8px;">
		</select>
	</td></tr></table>
	
	<table class="table10" border="0"><tr>
		<td><input type="checkbox" name="calcHour" value="hours" <?php if ($calcHour != "") {echo 'checked="checked"';}?>>Calculate Manhours<br></td>
		<td><input type="checkbox" name="calcCost" value="cost" <?php if ($calcCost != "") {echo 'checked="checked"';}?>>Calculate Cost<br></td>
	</tr></table>
	
</form>

<?php
		$db = new Database();	// open database
	
		$sql = 'select ';
		
		if ($nf == 0) {
			
		}
		for ($i = 0; $i < $nf; $i++) {
			$sql .= $field[$i].', ';
		}	
		if ($calcHour != "") {
			$sql .= 'sum(mhours) as manhours ';
		}
		if ($calcHour != "" && $calcCost != "") {
			$sql .= ', ';
		}
		if ($calcCost != "") {
			$sql .= 'sum(mcost) as manhourcost ';
		}
		
		if ($calcHour == "" && $calcCost == "") {
			$sql .= '0 ';
		}

		$sql .= 'from tsview where mdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") ';
		$sql .= 'AND mdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d") AND status="A" ';
		if ($nf > 0) {
			$sql .= 'group by ';
			for ($i = 0; $i < $nf; $i++) {
				if ($i > 0) {
					$sql .= ', ';
				}
				$sql .= $field[$i];
			}
			$sql .= ' order by ';
			for ($i = 1; $i < $nf; $i++) {
				$sql .= $i .', ';
			}
			$sql .= $nf;
		}
		$sql .= ";";

		$rows = $db->select($sql);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}

		echo '<br><br>';
		echo '<table class="table10" border="0"><tr><th>';
		echo '<h3>Manhour Summary</h3>';
		echo '</th></tr></table>';
		echo "\n";
		
		echo '<table class="table10" align="left" border="1">';
		echo "\n";
		echo '<tr class="table10 table11">';
		
		for ($i = 0; $i < $nf; $i++) {
			echo '<th>' . $field[$i] . '</th>';
		}
		
		if ($calcHour != "") {
			echo '<th width="70px">Manhours</th>';
		}

		if ($calcCost != "") {
			echo '<th width="80px">Cost (Rs)</th>';
		}
		echo '</tr>';
		echo "\n";

		$sumHours = 0;	
		$sumCost = 0;
		foreach ($rows as $row)
		{
			$hours = $row['manhours'];
			$cost = $row['manhourcost'];
			echo '<tr class="table10 table12">';
			for ($i =0; $i < $nf; $i++) {
				echo '<td>' . $row[$field[$i]] . '</td>';
			}
			
			if ($calcHour != "") {
				echo '<td align="right">' . $hours . '</td>';
			}

			if ($calcCost != "") {
				echo '<td align="right">' . $cost . '</td>';
			}
			echo "\n";
			echo '</tr>';
			$sumHours += $hours;
			$sumCost += $cost;
		}
		
		if ($nf > 0) {
			echo '<tr class="table10 table13"><th align="right" colspan="' . $nf . '">Total</th>';
			
			if ($calcHour != "") {
				echo '<th align="right">'. $sumHours . '</th>';
			}

			if ($calcCost != "") {
				echo '<th align="right">'. $sumCost . '</th>';
			}
			echo '</tr>';
		}
		echo "\n";
		echo '</table>';
		echo "\n";
		echo '<br>';

		$db->close();	// Close database  

?>
</body>
</html> 
