<html>
<head>
<?php
	function getNewDate($sdate,$ndays, $dfmt) {
		// $sdate in yyyy-mm-dd in string format
		// $ndays is no of days starting from $sdate in integer format
		// $dfmt is the output date format e.g "d-m-y"
		// returns the new date in $dfmt in string format
		
		$date=date_create($sdate);
		date_add($date,date_interval_create_from_date_string($ndays . " days"));
		$newDate = date_format($date,$dfmt);
		return $newDate;
	}
?>
</head>
<body>
<?php

	
$s=date("Y-m-d");
echo $s."<br>";
$t=getNewDate($s,1, "d-M-y"); echo "plus ". 1 . "=" . $t . "<br>";
$t=getNewDate($s,2, "d-M-y"); echo "plus ". 2 ."=".$t . "<br>";
$t=getNewDate($s,10, "d-M-y"); echo "plus ". 10 ."=".$t . "<br>";
$t=getNewDate($s,30, "d-M-y"); echo "plus ". 30 ."=".$t . "<br>";

$t=getNewDate($s,0, "d-M-y"); echo "plus ". 0 ."=".$t . "<br>";

$t=getNewDate($s,-1, "d-M-y"); echo "plus ". -1 ."=".$t . "<br>";
$t=getNewDate($s,-2, "d-M-y"); echo "plus ". -2 ."=".$t . "<br>";
$t=getNewDate($s,-10, "d-M-y"); echo "plus ". -10 ."=".$t . "<br>";
$t=getNewDate($s,-30, "d-M-y"); echo "plus ". -30 ."=".$t . "<br>";
?>
<input type="text" name="no" id="no" value="">

</body>
</html>