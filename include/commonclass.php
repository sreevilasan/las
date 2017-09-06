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
	function createDropDownString($table, $column1, $column2, $selectString){
		$db = new Database();	// open database
		$sql = "SELECT " . $column1 . ", " . $column2 . " FROM " . $table . " order by " . $column1 . ";";
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
		return $dropdownString;
	} 
?>