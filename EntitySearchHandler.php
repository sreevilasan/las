<!DOCTYPE html>
<?php
	// File: EntitySearchHandler.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	$fieldid = $_GET['fieldid'];
	
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
	$entitydescol = $row['descol'];
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
		function closeMySelf(sender) {
			try {
				window.opener.HandlePopupResult(document.getElementById('entityid').value, document.getElementById('fieldid').value, sender.getAttribute("primarykey"), sender.getAttribute("description"));
			}
			catch (err) {}
			window.close();
			return false;
		}
	</script>
	</head>
	
	<body onload=";">
	
			<table border="0">
				<tr>
<?php
			echo '					<th align="center">Action</th>';
			foreach ($entityfields as $entityfield)
			{

				if ($entityfield['fieldid'] == $entityprimcol) {
					//$disabled = "disabled";
				}
				
				if($entityfield['search'] == "Y") {
					echo '					<th align="center">' . $entityfield['description'] . '</th>';
				}
				

			}
?>
				</tr>
<?php
			foreach ($rows as $row)
			{
				echo "				<tr>\n";
				echo '<td><a href="#" primarykey="' . $row[$entityprimcol] . '" description="' . $row[$entitydescol] . '" onclick="return closeMySelf(this);">Select</a></td>';
				foreach ($entityfields as $entityfield) {
					if ($entityfield['fieldid'] == $entityprimcol) {
						//$disabled = "disabled";
					}
					if($entityfield['search'] == "Y") {
						echo '					<td align="center">' . $row[$entityfield['fieldid']] . '</td>';
					}
				}
				echo "\n";
				echo "				</tr>\n";
			}
?>
			</table>
			<input type="hidden" name="entityid" id="entityid" value="<?php echo $entityid; ?>">
			<input type="hidden" name="fieldid" id="fieldid" value="<?php echo $fieldid; ?>">
	</body>
</html>