<!DOCTYPE html>
<?php
	// File: EntityDisplay.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	$primarykey = $_GET['primarykey']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['entityid'];
		$primarykey = $_POST['primarykey']; 
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
	$bs = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	
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
	
	if ($primarykey != "") {
		
		$entitysql = "";	
		foreach ($entityfields as $entityfield)
		{
			if($entityfield['display'] == "Y") {
				$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
			}
		}
		$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));
			
		$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " WHERE " . $entityprimcol . " = '" . $primarykey . "';";

		$row = $db->select($entitysql, [], true);
		
		foreach ($entityfields as $entityfield)
		{
			$entityfield['value'] = $row[$entityfield['fieldid']];
			$entityfields[$entityfield['fieldid']] = $entityfield;
		}	
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
<?php
			foreach ($entityfields as $entityfield)
			{
				$disabled = "";
				if ($entityfield['fieldid'] == $entityprimcol) {
					$disabled = "disabled";
				}
				
				echo "				<tr>\n";
				echo '					<th align="left">' . $entityfield['description'] . '</th>';
				echo "\n";
				echo '					<td>' . $entityfield['value'] . '</td>';
				echo "\n";
				echo "				</tr>\n";
			}
?>
			</table>
			<input type="hidden" name="entityid" id="entityid" value="<?php echo $entityid; ?>">
			<input type="hidden" name="primarykey" id="primarykey" value="<?php echo $primarykey; ?>">
		
		<table border="0" align="left">
			<tr>
				<td><button class="button button1" value="Edit" onclick="edit();">Edit</button></td>
				<td><button class="button button1" type="button" value="Quit" onclick="quit();">Back</button></td>
			</tr>
		</table>
	</body>
</html>