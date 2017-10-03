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
	$primarykey2 = $_GET['primarykey2']; 
	$primarykey3 = $_GET['primarykey3']; 
	$primarykey4 = $_GET['primarykey4']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['_entityid'];
		$primarykey = $_POST['primarykey'];
		$primarykey2 = $_POST['primarykey2'];
		$primarykey3 = $_POST['primarykey3'];
		$primarykey4 = $_POST['primarykey4'];		
	}
	
	require 'include/GetEntityFields.php';

	$db = new Database();	// open database
	
	if ($primarykey != "") {
	
		$entitysql = "";	
		foreach ($entityfields as $entityfield)
		{
			if($entityfield['hidden'] != "Y") {
				$entitysql = $entitysql . $entityfield['fieldid'] . ", ";
			}
		}
		$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));
			
		$entitysql = "SELECT " . $entitysql . " FROM " . $entityprimtable . " WHERE " . $entityprimcol . " = '" . $primarykey . "' ";
		
		if($primarykey2 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol2 . " = '" . $primarykey2 . "' "; 
		}
		
		if($primarykey3 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol3 . " = '" . $primarykey3 . "' "; 
		}
		
		if($primarykey4 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol4 . " = '" . $primarykey4 . "' "; 
		}
		
		$entitysql = $entitysql . " ;";

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
			document.location = "EntitySearch.php?entityid=" + document.getElementById('_entityid').value
		}
		
		function edit() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('_entityid').value + "&primarykey=" + document.getElementById('primarykey').value  + "&primarykey2=" + document.getElementById('primarykey2').value  + "&primarykey3=" + document.getElementById('primarykey3').value  + "&primarykey4=" + document.getElementById('primarykey4').value;
		}
		function displayImage(img) {
			newwindow = window.open(img,'_blank','left=400,top=50,height=500,width=600,titlebar=no,toolbar=no,location=no');
			//if (window.focus) {newwindow.focus();}
		}
	</script>
	</head>
	
	<body onload=";">
	
<?php
		echo '<table class="tabinput" border="0">';      // main title
		echo '<tr><td colspan="2">';
		echo '<h1>' . $entitydescription. ' </h1></td>';
?>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
<?php
		echo '</tr><tr>';

		if ($displayphoto == "Y") {
			echo '<td valign="top"><a href="javascript:displayImage(\'' . $imagefile . '\');"><img src="' . $imagefile . '" height="100" width="80"></a></td>';
		}
		echo '<td>';
		
		echo '<table class="tabinput" border="1">';		// field inputs

		foreach ($entityfields as $entityfield)
		{
			$disabled = "";
			if ($entityfield['fieldid'] == $entityprimcol) {
				$disabled = "disabled";
			}
			if ($entityfield['hidden'] != 'Y') {
				echo "				<tr>\n";
				echo '					<th align="left">' . $entityfield['description'] . '</th>';
				echo "\n";
				if($entityfield['displaytype'] == "date") {
					$date=date_create($entityfield['value']);
					echo '<td>' .  date_format($date,"d-M-Y") . '</td>';
				} elseif($entityfield['displaytype'] == "dropdown") {
					echo '<td>' . getDropdownValue($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $entityfield['value']) . '</td>';
				} elseif($entityfield['displaytype'] == "entity") {
					echo '<td>' . getEntityDescription($entityfield['refentityid'], $entityfield['value']) . '</td>';	
				} else {
					echo '<td>' . $entityfield['value'] . '</td>';	
				}

				echo "\n";
				echo "				</tr>\n";
			}
		}
		echo "				</table>";
		echo "</td>\n";
		echo "</tr></table>";

?>		
		<input type="hidden" name="_entityid" id="_entityid" value="<?php echo $entityid; ?>">
		<input type="hidden" name="primarykey" id="primarykey" value="<?php echo $primarykey; ?>">
		<input type="hidden" name="primarykey2" id="primarykey2" value="<?php echo $primarykey2; ?>">
		<input type="hidden" name="primarykey3" id="primarykey3" value="<?php echo $primarykey3; ?>">
		<input type="hidden" name="primarykey4" id="primarykey4" value="<?php echo $primarykey4; ?>">
		
		<table border="0" align="left">
			<tr>
				<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
			</tr>
		</table>

	</body>
</html>