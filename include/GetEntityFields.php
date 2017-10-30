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
	$entityprimcol2 = $row['primcol2'];
	$entityprimcol3 = $row['primcol3'];
	$entityprimcol4 = $row['primcol4'];	
	$entitydescol = $row['descol'];
	$selfgenprimkey = $row['selfgenprimkey'];
	$displayphoto = $row['displayphoto'];
	$entitymenu = $row['menu'];
	$entityextrabutton = $row['extrabutton'];
	$imagefile = "images/photo/". $entitydescription . '-' . $primarykey . ".jpg";
//echo "image=".$imagefile;	
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
		$entityfield['comment'] = $row['comment'];
		$entityfield['displayseq'] = $row['displayseq'];
		$entityfield['displaytype'] = $row['displaytype'];
		$entityfield['width'] = $row['width'];
		$entityfield['refentityid'] = $row['refentityid'];
		$entityfield['refentityprim1'] = $row['refentityprim1'];
		$entityfield['refentityprim2'] = $row['refentityprim2'];
		$entityfield['refentityprim3'] = $row['refentityprim3'];
		$entityfield['lookupid'] = $row['lookupid'];
		$entityfield['reftable'] = $row['reftable'];
		$entityfield['refvalcol'] = $row['refvalcol'];
		$entityfield['refdescol'] = $row['refdescol'];
		$entityfield['refnextid'] = $row['refnextid'];
		$entityfield['hidden'] = $row['hidden'];
		$entityfield['search'] = $row['search'];
		$entityfield['required'] = $row['required'];
		$entityfield['disable'] = $row['disable'];
		
		$entityfields[$row['fieldid']] = $entityfield;
	}
	
	$db->close();	// Close database
//  got entity and entity fields	********
?>