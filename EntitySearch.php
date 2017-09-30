<!DOCTYPE html>
<?php
	// File: EntitySearch.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['_entityid'];
	}
	
	require 'include/GetEntityFields.php';
	
	// make entity sql query
	$db = new Database();	// open database
	
	$entitysql = "";
	foreach ($entityfields as $entityfield)
	{
			$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
	}
	$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));
	
	$filtermap;
	$entityfilterclause = "where 1=1 ";
	
	foreach ($entityfields as $entityfield)
	{
		//$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		if($entityfield['filterable'] == "Y") {
			$filtervalue = "";
			$filtervalue = $_GET['filter_' . $entityfield['fieldid']]; 
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$filtervalue = $_POST['filter_' . $entityfield['fieldid']];
			}
			$tempfilter['fieldid'] = $entityfield['fieldid'];
			$tempfilter['filteroperator'] = $entityfield['filteroperator'];
			$tempfilter['value'] = $filtervalue;
			$filtermap[$entityfield['fieldid']] = $tempfilter; 
			if ($filtervalue != "") {
				$entityfilterclause = $entityfilterclause . " and lower("  . $tempfilter['fieldid'] . ") " . $tempfilter['filteroperator'] . " lower('" . $filtervalue . "')";
			}
		}
	}

	
	$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " " . $entityfilterclause . ";";

	$rows = $db->select($entitysql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	$db->close();	// Close database  
?>

<html>
<head>
	<style>
		body {background-color: white;}
	</style>
	<link rel="stylesheet" href="css/manhour.css">
	<link rel="stylesheet" href="css/LasStyle.css">
	<script type="text/javascript">
		function quit() {
			document.location = "DbMain.php"; // go to entity main page
		}
		
		function edit() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('_entityid').value + "&primarykey=" + document.getElementById('primarykey').value;
		}
		function addnew() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('_entityid').value;
		}
		
		function deleteEntity(entityid, primarykey, entitydescription, objectdescription) {
			if (confirm("Do you want to delete " + entitydescription + " : " + objectdescription) == true) {
				var xmlhttp = new XMLHttpRequest();
				
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						//activityId.innerHTML = this.responseText;
						document.getElementById(primarykey).style.display = "none";
					}
				};

				xmlhttp.open("GET", "EntityDelete.php?entityid=" + entityid + "&primarykey=" + primarykey, true);
				xmlhttp.send();
			} 

		}
		
	</script>
</head>
	
<body onload=";">

	<table class="tabinput" border="0">
		<tr>
			<td colspan="2">
				<h1><?php echo $entitydescription; ?></h1>
			</td>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
	<br>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="filterform" onsubmit=";">
		<input type="hidden" name="_entityid" id="_entityid" value="<?php echo $entityid; ?>">
		<table class="tabinput" border="0">
			<tr>
<?php
			foreach ($entityfields as $entityfield)
			{
				
				if($entityfield['filterable'] == "Y") {
					echo '					<th>' . $entityfield['description'] . '</th>';
					echo '					<td><input type="text" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $filtermap[$entityfield['fieldid']]['value'] . '"></td>';
				}
				
			}
?>
				<td><button class="button button1" type="submit" form="filterform" value="Go" onclick=";">Go</button></td>
			</tr>

		</table>
	</form>
	<br>
	<table class="tabinput" border="1">
		<tr>
<?php

			if ($entityedit == 'Y') {
				echo '<th>Edit</th>';
			}
			if ($entityview == 'Y') {
				echo '<th>View</th>';
			}
			if ($entitydelete == 'Y') {
				echo '<th>Delete</th>';
			}
			
			//echo '					<th>Action</th>';
			foreach ($entityfields as $entityfield)
			{
				if ($entityfield['fieldid'] == $entityprimcol) {
					//$disabled = "disabled";
				}
				
				if($entityfield['search'] == "Y") {
					echo '					<th>' . $entityfield['description'] . '</th>';
				}
				
			}
?>
		</tr>
<?php
		foreach ($rows as $row)
		{
			echo '				<tr id="' . $row[$entityprimcol] . '">';
			
			if ($entityedit == 'Y') {
				echo '<td><a href="EntityAddUpd.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">Edit</a></td>';
			}
			if ($entityview == 'Y') {
				echo '<td><a href="EntityDisplay.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">View</a></td>';
			}
			if ($entitydelete == 'Y') {
				echo '<td><a href="javascript:deleteEntity(\'' . $entityid . '\', \'' . $row[$entityprimcol] . '\', \'' . $entitydescription . '\', \'' . $row[$entitydescol] . '\')">Delete</a></td>';
			}	
			
			foreach ($entityfields as $entityfield) {
				if ($entityfield['fieldid'] == $entityprimcol) {
					//$disabled = "disabled";
				}
				if($entityfield['search'] == "Y") {
					echo '					<td>' . $row[$entityfield['fieldid']] . '</td>';
				}		
			}
			echo "\n";
			echo "				</tr>\n";
		}
?>
	</table>
	


	<table border="0" align="left">
		<tr>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
</body>
</html>