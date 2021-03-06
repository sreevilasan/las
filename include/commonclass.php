<?php
/* Database Classes */
class Database {
	private $host = ""; 	// Database host
	private $port = ""; 	// Database host
	private $dbname = ""; 	// Database name
    private $user = ""; 	// Database user
    private $password = ""; 	// Database password
    private $charset = "UTF8"; 	    // Database charset.
	
	// Connection information.
    private $connection;

    // SQL query information.
    private $cquery;

    // Connected to the database server.
    private $connected = false;

    // Errors.
    private $error;


    // PDO options.
    private $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true
    ];

    /* Constructor - Creates connection to the database server. */
    public function __construct() {
        if ($this->connected === true) {
            return true;
        } else {
            try {
				$config = parse_ini_file('../../las/Database.ini');
				$this->host = $config['host'];	// Database host
				$this->port = $config['port']; 	// Database host
				$this->dbname = $config['dbname']; 	// Database name
				$this->user = $config['user']; 	// Database user
				$this->password = $config['password']; 	// Database password

                $this->connection = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}", $this->user, $this->password, $this->options);
                $this->connected = true;	
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                return null;
            }
        }
    }
	
	public function select($query, $parameters = [], $expectSingleResult = false) {
		
		$this->error = "";
		
        if ($this->connected === true) {
            if (is_string($query) && $query !== "" && is_array($parameters) && is_bool($expectSingleResult)) {
                try {
                    $this->cquery = $this->connection->prepare($query); 	 // Prepare SQL query.
					
                    foreach ($parameters as $placeholder => $value) { 	 // Bind parameters to SQL query.

                        // Parameter type.
                        if (is_string($value)) {
                            $type = PDO::PARAM_STR; 	// Parameter is a string.
                        } elseif (is_int($value)) {
                            $type = PDO::PARAM_INT; 	// Parameter is a integer.
                        } elseif (is_bool($value)) {
                            $type = PDO::PARAM_BOOL; 	// Parameter is a boolean.
                        } else {
                            $type = PDO::PARAM_NULL; 	// Parameter is NULL.
                        }
						
                        // Bind parameter.
                        $this->cquery->bindValue($placeholder, $value, $type); 	
                    }

                    // Execute SQL query.
                    $this->cquery->execute();

                    // Get Result of SQL query.
                    if ($expectSingleResult === true) {
                        $results = $this->cquery->fetch();
                    } else {
                        $results = $this->cquery->fetchAll();
					}

                    // Return results of SQL query.
                    return $results;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                }
            } else {
                $this->error = "Invalid Query or Parameters";
                return null;
            }
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }
	


    /* Query the Database - Used for SELECT, INSERT,PDATE and DELETE statements    */
    public function query($query, $parameters = [], $expectSingleResult = false) {
		$this->error = "";
        if ($this->connected === true) {
            if (is_string($query) && $query !== "" && is_array($parameters) && is_bool($expectSingleResult)) {
                try {
                    $this->cquery = $this->connection->prepare($query); 	 // Prepare SQL query.
                    foreach ($parameters as $placeholder => $value) { 	 // Bind parameters to SQL query.

                        // Parameter type.
                        if (is_string($value)) {
                            $type = PDO::PARAM_STR; 	// Parameter is a string.
                        } elseif (is_int($value)) {
                            $type = PDO::PARAM_INT; 	// Parameter is a integer.
                        } elseif (is_bool($value)) {
                            $type = PDO::PARAM_BOOL; 	// Parameter is a boolean.
                        } else {
                            $type = PDO::PARAM_NULL; 	// Parameter is NULL.
                        }
						
                        // Bind parameter.
                        $this->cquery->bindValue($placeholder, $value, $type); 	
                    }

                    // Execute SQL query.
                    $this->cquery->execute();

                    // Get Result of SQL query.
                    if ($expectSingleResult === true) {
                        $results = $this->cquery->fetch();
                    } else {
                        $results = $this->cquery->fetchAll();
                    }

                    // Return results of SQL query.
                    return $results;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                }
            } else {
                $this->error = "Invalid Query or Paramaters";
                return null;
            }
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* Row count for the last query */
    public function rowCount() {
        if ($this->connected === true) {
            return $this->cquery->rowCount();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* Get ID for the last query */
    public function lastId() {
        if ($this->connected === true) {
            return $this->connection->lastInsertId();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* Begin a transaction */
    public function beginTransaction() {
        if ($this->connected === true) {
            return $this->connection->beginTransaction();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* Rollback and cancel/end a transaction */
    public function cancelTransaction() {
        if ($this->connected === true) {
            return $this->connection->rollBack();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* rollbackTransaction is same as cancel transaction*/
    public function rollbackTransaction() {
        if ($this->connected === true) {
            return $this->connection->rollBack();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }

    /* Commit and end a transaction */
    public function endTransaction() {
        if ($this->connected === true) {
            return $this->connection->commit();
        } else {
            $this->error = "Not Connected to Database Server";
            return null;
        }
    }
	
	public function getError() {
		return $this->error;
	}

    /* Close the current connection the the database server */
    public function close() {
        $this->connection = null;
		$connected = false;
    }
}

// Function to echo dropdown code in html
function createDropDown($table, $column1, $column2){
	$db = new Database();	// open database
	$sql = "SELECT " . $column1 . ", " . $column2 . " FROM " . $table;
	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	foreach ($rows as $row)
	{
		echo "<option value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option>";
	}
} 

// Function to return dropdown string
function createDropDownString($table, $column1, $column2, $selectString, $disable = "disabled"){
	$db = new Database();	// open database
	$sql = "SELECT " . $column1 . ", " . $column2 . " FROM " . $table . ";";
//echo "sql=".$sql;

	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	// putting blank option at start of dropdown
	$dropdownString = "\n								<option " . $disable . " selected></option> ";
	
	// Looping through query output and creating dropdown
	foreach ($rows as $row)
	{
		if ($selectString == $row['' . $column1. '']) {
			$dropdownString = $dropdownString . "\n								<option selected value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		} else {
			$dropdownString = $dropdownString . "\n								<option value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		}
	}
	return $dropdownString;
} 

function createDropDownProject($table, $column1, $column2, $selectString){
	$db = new Database();	// open database
	
	$sql = "(SELECT " . $column1 . ", concat(catagory,'-',prjno,' ',name) as " . $column2 . ", catagory, prjno FROM " . $table . " where catagory!='99' and status='On Going') UNION (SELECT " . $column1 . ", " . $column2 . ", catagory, prjno FROM " . $table . " where catagory='99') order by catagory asc, PrjNo DESC;";
//echo "sql=".$sql; 

	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	// putting blank option at start of dropdown
	$dropdownString = "\n								<option disabled selected></option> ";
	
	// Looping through query output and creating dropdown
	foreach ($rows as $row)
	{
		if ($selectString == $row['' . $column1. '']) {
			$dropdownString = $dropdownString . "\n								<option selected value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		} else {
			$dropdownString = $dropdownString . "\n								<option value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		}
	}
	
	$db->close();
	
	return $dropdownString;
} 

function createDropDownEmployee($table, $column1, $column2, $selectString){		
	$db = new Database();	// open database
	
	$sql = "SELECT " . $column1 . ", concat(empno,' - ',name) as " . $column2 . " FROM " . $table . " where status != 'Left' order by empno asc ;";

	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	// putting blank option at start of dropdown
	$dropdownString = "\n	<option disabled selected></option> ";
	
	// Looping through query output and creating dropdown
	foreach ($rows as $row)
	{
		if ($selectString == $row['' . $column1. '']) {
			$dropdownString = $dropdownString . "\n	   <option selected value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		} else {
			$dropdownString = $dropdownString . "\n	   <option value=\"" . $row['' . $column1. ''] . "\">" . $row['' . $column2. ''] . "</option> ";
		}
	}
	
	$db->close();
	
	return $dropdownString;
} 

function getProjectName($pid) {	
	$db = new Database();	// open database
	
	$sql = "select catagory, prjno, name, description from project where prjid ='" . $pid . "' limit 1 ;";
	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	$projectName = $row['catagory']."-".$row['prjno']." ".$row['name'];
	
	$db->close();
	
	return $projectName;
}

function getEmployeeName($eid) {
	
	$db = new Database();	// open database
	
	$sql = "select name from employee where empid ='" . $eid . "' limit 1 ;";
	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	$employeeName = $row['name'];
	
	$db->close();
	
	return $employeeName;
}

// Function to return a new Id
function getNextId($table, $nextidcolumn){
	$db = new Database();	// open database
	
	$sql = "SELECT " . $nextidcolumn . ", max(" . $nextidcolumn . ") as maxval FROM " . $table . " ;";
//echo "sql=".$sql;

	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	$str = $row['' . $nextidcolumn . ''];
	$pad_length = strlen($str);
	$maxval = (int)$row['maxval'];
	$nextval = $maxval + 1;
	$nextid = str_pad($nextval, $pad_length, "0", STR_PAD_LEFT);
	
	$db->close();

	return $nextid;
} 

function isaHoliday($idate) {
	// check for public holiday
	$db = new Database();	// open database
	$sql = "select count(hdate) as rowCount from holiday where hdate = STR_TO_DATE('" . $idate ."','%Y-%m-%d');"; 
	$row = $db->select($sql, [], true);	
	
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	if ($row['rowCount'] == "0") {
		$publicHoliday = false;
	} else {
		$publicHoliday = true;
	}

	$db->close(); 	// close database connection

	$dayName = date("D",strtotime($idate));
	if ($dayName == "Sun" || $publicHoliday) { // if the day is weekend or holiday
		return true;	// holiday
	} else {
		return false;	// not holiday
	}
}

function getNewDate($sdate,$ndays, $dfmt) {
	// $sdate in yyyy-mm-dd in string format
	// $ndays is no of days starting from $sdate in integer format
	// $dfmt is the output date format e.g "d-m-y"
	// returns the new date in yyyy-mm-dd in string format
	
	$date=date_create($sdate);
	date_add($date,date_interval_create_from_date_string($ndays . " days"));
	$newDate = date_format($date,$dfmt);

	return $newDate;
}

function getSeatNumber(){
	$db = new Database();	// open database
	
	$sql = "select * from seat";
	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$db->close();
	
	return $employeeName;	
}

function generateDatabaseMenu($option=0) {
	$db = new Database();	// open database
	
	if ($option == 1) {  // DM
		$sql = 'select * from entity where menu="Y" and displayseq < 20 order by displayseq';
	} else if ($option == 2) {  // DM
		$sql = 'select * from entity where menu="Y" and displayseq >= 20 and displayseq < 50 order by displayseq';
	} else if ($option == 9) {  // DA
		$sql = 'select * from entity where displayseq >= 50 order by displayseq';
	} else { // DM
		$sql = 'select * from entity where menu="Y" order by displayseq';
	}
	
	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$menustring = "";
	foreach ($rows as $row) {
		$menustring .= '<a href="EntitySearch.php?entityid=' . $row['entityid'] . '">' . $row['description'] . '</a>';
	}
	
	$db->close();
	
	return $menustring;
}

	function getEntityDescription($v_entityid, $v_primarykey, $v_primarykey2 = "", $v_primarykey3 = "", $v_primarykey4 = "") {
		
		if ($v_primarykey2 != "") {
			$tempkey = $v_primarykey;
			$v_primarykey = $v_primarykey2;
			$v_primarykey2 = $tempkey;
		}
		
		if ($v_primarykey3 != "") {
			$tempkey = $v_primarykey2;
			$v_primarykey2 = $v_primarykey3;
			$v_primarykey3 = $tempkey;
		}
		
		if ($v_primarykey4 != "") {
			$tempkey = $v_primarykey3;
			$v_primarykey3 = $v_primarykey4;
			$v_primarykey4 = $tempkey;
		}
		
		$db = new Database();	// open database

		$sql = "SELECT * FROM entity where entityid='" . $v_entityid . "'";

		$row = $db->select($sql, [], true);
		
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}

		$v_entityprimtable = $row['primtable'];
		$v_entityprimcol = $row['primcol'];
		$v_entityprimcol2 = $row['primcol2'];
		$v_entityprimcol3 = $row['primcol3'];
		$v_entityprimcol4 = $row['primcol4'];
		$v_entitydescol = $row['descol'];
		
		$entitydescsql = "SELECT " . $v_entitydescol . " FROM " . $v_entityprimtable . " WHERE " . $v_entityprimcol . " = '" . $v_primarykey . "' ";
		
		if($v_primarykey2 != "") {
			$entitydescsql = $entitydescsql . " AND " . $v_entityprimcol2 . " = '" . $v_primarykey2 . "' "; 
		}
		
		if($v_primarykey3 != "") {
			$entitydescsql = $entitydescsql . " AND " . $v_entityprimcol3 . " = '" . $v_primarykey3 . "' "; 
		}
		
		if($v_primarykey4 != "") {
			$entitydescsql = $entitydescsql . " AND " . $v_entityprimcol4 . " = '" . $v_primarykey4 . "' "; 
		}
		
		$entitydescsql = $entitydescsql . " ;";

		$row = $db->select($entitydescsql, [], true);
		
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		$v_desc = $row[$v_entitydescol];
		$db->close();
// echo"entity=".$v_entityid."primekey=".$v_primarykey. "desc=".$v_desc."descol=".$v_entitydescol;

		return $v_desc;
	}
	
	function getLookupDropdown($lookupid, $lookupval = ""){
		$db = new Database();	// open database
		
		$sql = "SELECT value, description FROM lookupval where lookupid = '" . $lookupid . "' order by displayseq;";
		$rows = $db->select($sql);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		// putting blank option at start of dropdown
		$dropdownString = "\n								<option disabled selected></option> ";
		
		// Looping through query output and creating dropdown
		foreach ($rows as $row)
		{
			if ($lookupval == $row['value']) {
				$dropdownString = $dropdownString . "\n								<option selected value=\"" . $row['value'] . "\">" . $row['description'] . "</option> ";
			} else {
				$dropdownString = $dropdownString . "\n								<option value=\"" . $row['value'] . "\">" . $row['description'] . "</option> ";
			}
		}
		
		$db->close();
		
		return $dropdownString;
	} 
	
	function getLookupRadio($lookupid, $lookupval = "", $fieldname = ""){
		$db = new Database();	// open database
		
		$sql = "SELECT value, description FROM lookupval where lookupid = '" . $lookupid . "' order by displayseq;";
		$rows = $db->select($sql);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		// putting blank option at start of dropdown
		$dropdownString = "";
		
		// Looping through query output and creating dropdown
		foreach ($rows as $row)
		{
			if ($lookupval == $row['value']) {
				$dropdownString = $dropdownString . '<input type="radio" onchange="updateModifiedFlag();" name="' . $fieldname . '" id="' . $fieldname . '" checked value="' . $row['value'] . '">' . $row['description'] . '&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
				$dropdownString = $dropdownString . '<input type="radio" onchange="updateModifiedFlag();" name="' . $fieldname . '" id="' . $fieldname . '" value="' . $row['value'] . '">' . $row['description'] . '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
		
		$db->close();
		
		return $dropdownString;
	}
	
	function isSearchable($v_entityid){
		$db = new Database();	// open database

		$sql = "SELECT distinct(search) FROM entityfields where entityid='" . $v_entityid . "'";
		$rows = $db->select($sql);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		$searchable = false;
		foreach ($rows as $row)
		{
			if ($row['search'] == "Y") {
				$searchable = true;
			}
		}
		
		$db->close();

		return $searchable;
	}
	
	function getDropdownValue($reftable, $refvalcol, $refdescol, $refvalue) {
		$returnvalue = $refvalue;
		
		$db = new Database();	// open database
	
		$sql = "select " . $refdescol . " from " . $reftable . " where " . $refvalcol . "='" . $refvalue . "';";

		$row = $db->select($sql, [], true);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		$returnvalue = $row[$refdescol];
		
		$db->close();
		
		return $returnvalue;	
	}
	
	function addExtraButton($extrabutton) {
		parse_str($extrabutton,$buttonArray);
		
		if ($buttonArray['type'] != "") {
			$buttonType= 'type="'. $buttonArray['type'] . '"';
		} else {
			$buttonType= "";
		}
		if ($buttonArray['onclick'] != "") {
			$buttonOnclick= 'onclick="' . $buttonArray['onclick'] . '"';
		} else {
			$buttonOnclick = "";
		}
		if ($buttonArray['value'] != "") {
			$buttonValue= 'value="' . $buttonArray['value'] . '"';
		} else {
			$buttonValue = "";
		}
		$buttonName = $buttonArray['value'];
		if ($buttonName == "") {
			$buttonName = $buttonArray['type'];
		}

		return '<button class="button button1" ' . $buttonType . ' ' . $buttonValue . ' ' . $buttonOnclick . '">'. $buttonName . '</button>';
	}
	
	class EntityAccess {
			private $entity = "";
			private $read = false; 
			private $edit = false; 
			private $add = false; 
			private $delete = false;

		/* Constructor*/
		public function __construct($v_entityid, $v_userRole) {
			
			$db = new Database();	// open database

			$sql = "SELECT * FROM entityaccess where entityid='" . $v_entityid . "' and role='" . $v_userRole . "';";
			$rows = $db->select($sql);				
			if ($db->getError() != "") {
				echo $db->getError();
				exit();
			}

			foreach ($rows as $row) {
				$accessPermission = "$" . strtoupper($row['accessid']);  // $ added to get index > 0
	
				if (strpos($accessPermission,"R") > 0) {
					$this->read = true;
				}
				
				if (strpos($accessPermission,"E") > 0) {
					$this->edit = true;
				}
			
				if (strpos($accessPermission,"A") > 0) {
					$this->add = true;
				}
				
				if (strpos($accessPermission,"D") > 0) {
					$this->delete = true;
				}			
			}	
			$db->close();
		}
		
		public function hasReadAccess() {
			return $this->read;
		}
		
		public function hasEditAccess() {
			return $this->edit;
		}
		
		public function hasAddAccess() {
			return $this->add;
		}
		
		public function hasDeleteAccess() {
			return $this->delete;
		}
	}
?>