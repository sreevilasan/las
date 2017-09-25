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

	$db = new Database();	// open database

	$sql = "SELECT * FROM entity where entityid='" . $entityid . "'";
	$row = $db->select($sql, [], true);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$entitydescription = $row['description'];
	$entityprimtable = $row['primtable'];
	$entityprimcol = $row['primcol'];
	echo '<h2>' . $entitydescription. ' </h2><br>';
	
	$sql = "SELECT * FROM entityfields where entityid='" . $entityid . "' order by displayseq";
	$rows = $db->select($sql);
	
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$entityfields;
	
	foreach ($rows as $row)
	{
		$entityfield['fieldid'] = $row['fieldid'];
		$entityfield['description'] = $row['description'];
		$entityfield['displayseq'] = $row['displayseq'];
		$entityfield['display'] = $row['display'];
		$entityfield['search'] = $row['search'];
		$entityfields[$row['fieldid']] = $entityfield;
	}
	$rows = "";

	// make entity sql query
	$entitysql = "";
	foreach ($entityfields as $entityfield)
	{
		if($entityfield['search'] == "Y") {
			$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		}	
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
			document.location = "EditEmployee.php"; // go to employee main page
		}
		
		function edit() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('entityid').value + "&primarykey=" + document.getElementById('primarykey').value;
		}
	</script>
</head>
	
<body onload=";">
	
		<table border="0">
			<tr>
<?php
			echo '					<th>Action</th>';
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
			echo "				<tr>\n";
			echo '<td><a href="EntityAddUpd.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">Edit</a>&nbsp;<a href="EntityDisplay.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">View</a></td>';
			
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
	
	<input type="hidden" name="entityid" id="entityid" value="<?php echo $entityid; ?>">

	<table border="0" align="left">
		<tr>
			<td><button class="button button1" value="Edit" onclick="edit();">Edit</button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Back</button></td>
		</tr>
	</table>
</body>
</html>