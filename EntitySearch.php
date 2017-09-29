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
		$entityid = $_POST['entityid'];
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
	
	$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . ";";

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
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('entityid').value + "&primarykey=" + document.getElementById('primarykey').value;
		}
		function addnew() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('entityid').value;
		}
		
	</script>
</head>
	
<body onload=";">

<?php
		echo '<table class="tabinput" border="0">';      // main title
		echo '<tr><td colspan="2">';
			echo '<h1>' . $entitydescription. ' </h1></td>';
?>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
<?php
		echo '</tr><tr><td>';
?>
	
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
			echo '				<tr>';
			
			if ($entityedit == 'Y') {
				echo '<td><a href="EntityAddUpd.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">Edit</a></td>';
			}
			if ($entityview == 'Y') {
				echo '<td><a href="EntityDisplay.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">View</a></td>';
			}
			if ($entitydelete == 'Y') {
				echo '<td><a href="EntityDelete.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">Delete</a></td>';
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
	</table></td></table>
	
	<input type="hidden" name="entityid" id="entityid" value="<?php echo $entityid; ?>">

	<table border="0" align="left">
		<tr>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
</body>
</html>