<?php
// Prevent multiple class declarations
if (!class_exists('Gofund')) {
require_once 'Connection.php';

class Gofund {
    private $conn;
    private $fund;
    private $lastError;

    public function __construct($fund = null) {
        $this->fund = $fund;
        $this->connect();
    }

    private function connect() {
        // Create connection
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to utf8
        $this->conn->set_charset("utf8");
    }

    public function queryfire($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            // Log error but don't die - return false
            error_log("Query Error: " . $this->conn->error . " | Query: " . $query);
            // Store error for later retrieval
            $this->lastError = $this->conn->error;
            return false;
        }
        return $result;
    }
    
    public function getLastError() {
        return isset($this->lastError) ? $this->lastError : $this->conn->error;
    }

    public function fetchData($query) {
        $result = $this->queryfire($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function fetchDataQuery() {
        // Fetch footer data from settings table
        $query = "SELECT * FROM `tbl_setting` LIMIT 1";
        $result = $this->queryfire($query);
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            // Return data with 'data' key for footer
            return array(
                'data' => isset($data['footer_text']) ? $data['footer_text'] : (isset($data['footer']) ? $data['footer'] : '')
            );
        }
        // Return empty data if no settings found
        return array('data' => '');
    }

    public function real_string($string) {
        if (is_null($string)) {
            return '';
        }
        return mysqli_real_escape_string($this->conn, $string);
    }

    public function login($username, $password, $type = 'admin') {
        // Check domain validation first (you may need to implement this)
        // For now, returning 1 for valid login
        
        $username = $this->real_string($username);
        $password = $this->real_string($password);
        
        if ($type == 'admin') {
            $query = "SELECT * FROM admin WHERE username = '" . $username . "' AND password = '" . $password . "'";
            $result = $this->queryfire($query);
            if ($result && $result->num_rows == 1) {
                return 1; // Login successful
            } else {
                return 0; // Login failed
            }
        }
        
        return 0;
    }

    public function insertData($field_values, $data_values, $table) {
        $fields = implode("`, `", $field_values);
        $values = implode("', '", array_map(array($this, 'real_string'), $data_values));
        
        $query = "INSERT INTO `" . $table . "` (`" . $fields . "`) VALUES ('" . $values . "')";
        
        if ($this->queryfire($query)) {
            return true;
        }
        return false;
    }

    public function insertData_Api($field_values, $data_values, $table) {
        return $this->insertData($field_values, $data_values, $table);
    }

    public function insertDataId_Api($field_values, $data_values, $table) {
        // Check if id field exists and if it needs AUTO_INCREMENT
        // If id is not in field_values and table is users or tbl_user, we need to handle it
        if (($table == 'users' || $table == 'tbl_user') && !in_array('id', $field_values)) {
            // Try to get max id and increment it manually if AUTO_INCREMENT is not set
            $maxIdResult = $this->queryfire("SELECT MAX(id) as max_id FROM `" . $table . "`");
            $nextId = 1;
            if ($maxIdResult && $maxIdResult->num_rows > 0) {
                $maxRow = $maxIdResult->fetch_assoc();
                $nextId = ($maxRow['max_id'] ? intval($maxRow['max_id']) : 0) + 1;
            }
            
            // Try insert without id first (if AUTO_INCREMENT works)
            $fields = implode("`, `", $field_values);
            $values = implode("', '", array_map(array($this, 'real_string'), $data_values));
            $query = "INSERT INTO `" . $table . "` (`" . $fields . "`) VALUES ('" . $values . "')";
            
            try {
                $result = $this->conn->query($query);
                if ($result) {
                    $insertId = $this->conn->insert_id;
                    if ($insertId > 0) {
                        return $insertId;
                    }
                    // If insert_id is 0, AUTO_INCREMENT might not be set, use the manually calculated id
                    return $nextId;
                } else {
                    // If insert failed, check error message
                    $error = $this->conn->error;
                    if (strpos($error, "doesn't have a default value") !== false || 
                        strpos($error, "Field 'id'") !== false ||
                        strpos($error, "id") !== false) {
                        // Insert with explicit id
                        $field_values_with_id = array_merge(['id'], $field_values);
                        $data_values_with_id = array_merge([$nextId], $data_values);
                        $fields = implode("`, `", $field_values_with_id);
                        $values = implode("', '", array_map(array($this, 'real_string'), $data_values_with_id));
                        $query = "INSERT INTO `" . $table . "` (`" . $fields . "`) VALUES ('" . $values . "')";
                        
                        if ($this->queryfire($query)) {
                            return $nextId;
                        }
                    }
                    // If error doesn't match, throw exception to be caught by caller
                    throw new \Exception($error);
                }
            } catch (\mysqli_sql_exception $e) {
                // MySQL exception - try with explicit id
                $error = $e->getMessage();
                if (strpos($error, "doesn't have a default value") !== false || 
                    strpos($error, "Field 'id'") !== false ||
                    strpos($error, "id") !== false) {
                    // Insert with explicit id
                    $field_values_with_id = array_merge(['id'], $field_values);
                    $data_values_with_id = array_merge([$nextId], $data_values);
                    $fields = implode("`, `", $field_values_with_id);
                    $values = implode("', '", array_map(array($this, 'real_string'), $data_values_with_id));
                    $query = "INSERT INTO `" . $table . "` (`" . $fields . "`) VALUES ('" . $values . "')";
                    
                    if ($this->queryfire($query)) {
                        return $nextId;
                    }
                }
                // Re-throw if we can't handle it
                throw $e;
            }
        }
        
        // Standard insert for other tables
        $fields = implode("`, `", $field_values);
        $values = implode("', '", array_map(array($this, 'real_string'), $data_values));
        $query = "INSERT INTO `" . $table . "` (`" . $fields . "`) VALUES ('" . $values . "')";
        
        if ($this->queryfire($query)) {
            $insertId = $this->conn->insert_id;
            // If insert_id is 0, try to get the last inserted id manually
            if ($insertId == 0) {
                $result = $this->queryfire("SELECT LAST_INSERT_ID() as last_id");
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $insertId = $row['last_id'];
                }
                // If still 0, try to get max id (fallback)
                if ($insertId == 0) {
                    $result = $this->queryfire("SELECT MAX(id) as max_id FROM `" . $table . "`");
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $insertId = $row['max_id'];
                    }
                }
            }
            return $insertId;
        }
        return false;
    }

    public function updateData($field, $table, $where) {
        if (is_array($field)) {
            $set = array();
            foreach ($field as $key => $value) {
                $set[] = "`" . $key . "` = '" . $this->real_string($value) . "'";
            }
            $set_clause = implode(", ", $set);
        } else {
            $set_clause = $field;
        }
        
        $query = "UPDATE `" . $table . "` SET " . $set_clause . " " . $where;
        
        if ($this->queryfire($query)) {
            return true;
        }
        return false;
    }

    public function __destruct() {
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }
}
} // End of class_exists check
?>

