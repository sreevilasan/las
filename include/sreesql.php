<?php
define("HOST", "localhost", false);
define("DBNAME", "las", false);
define("USER", "root", false);
define("PASSWORD", "Login123#", false);

function OpenDatabase() { 	// Connect to database
	$pdo = "";
	
	try 
	{
		$pdo = new PDO('mysql:host='.HOST.';dbname='.DBNAME, USER, PASSWORD);
	}
	catch (PDOException $e) 
	{
		echo 'Error: ' . $e->getMessage();
		exit();
	}
	// echo 'Connected to MySQL <br><br>';

	return($pdo);
}

function CloseDatabase($pdo) { 	// Close connection
	$pdo = null;
	// echo "Database closed";
}

function SqlExecute($pdo,$sql) {
	// Run Query
	$stmt 	= $pdo->prepare($sql); // Prevent MySQl injection. 
	$stmt->execute();
	
	return($stmt);
}

?>
