<?php
//  Filename : GetEntityFields.php
//
// get entity and entity fields	********
	$db = new Database();	// open database

	$sql = "SELECT * FROM entity where entityid='" . $entityid . "';";
	$row = $db->select($sql, [], true);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$entitydescription = $row['description'];
	$entityprimtable = $row['primtable'];
	$entityprimcol = $row['primcol'];
	$entitydescol = $row['descol'];
	$selfgenprimkey = $row['selfgenprimkey'];
	$entityview = $row['view'];
	$entityedit = $row['edit'];
	$entitydelete = $row['delete'];
	$displayphoto = $row['displayphoto'];
	$entitymenu = $row['menu'];
	$imagefile = "images/photo/". $entitydescription . '-' . $primarykey . ".jpg";
	
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
		$entityfield['displaytype'] = $row['displaytype'];
		$entityfield['width'] = $row['width'];
		$entityfield['refentityid'] = $row['refentityid'];
		$entityfield['lookupid'] = $row['lookupid'];
		$entityfield['reftable'] = $row['reftable'];
		$entityfield['refvalcol'] = $row['refvalcol'];
		$entityfield['refdescol'] = $row['refdescol'];
		$entityfield['hidden'] = $row['hidden'];
		$entityfield['search'] = $row['search'];
		$entityfield['required'] = $row['required'];
		$entityfield['disable'] = $row['disable'];
		$entityfields[$row['fieldid']] = $entityfield;
	}
	
	$db->close();	// Close database
//  got entity and entity fields	********
?>