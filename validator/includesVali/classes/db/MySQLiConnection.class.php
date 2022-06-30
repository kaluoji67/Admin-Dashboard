<?php

require_once __DIR__ . '/PDODbConnection.class.php';

/**
 * Class handling connections to MySQL- and MariaDB-databases.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class MySQLiConnection extends DbConnection {
    const TYPE = "mysql";

    private $conn; //Database connection variable
    private $lastStmt; //Stores the last executed statement
    private $result; //Stores the result of the last executed action

    /**
     * @param $hostname The host to connect to.
     * @param $username The username.
     * @param $password The password.
     * @param $dbname The database to use.
     */
    public function __construct($hostname, $username, $password, $dbname) {
        $this->conn = new mysqli($hostname, $username, $password, $dbname);
        $this->conn->autocommit(TRUE);
        mysqli_set_charset($this->conn,'utf8');

    }

    /**
     * Returns a string describing the type.
     * @return mixed The type.
     */
    public function getType()
    {
        return 'mysql';
    }

    /**
     * Executes the given query. The n'th questionmark within the query
     * is replaced with the escaped n'th parameter.
     * @param $query A valid SQL query
     * @param null $params An array of parameters
     * @return bool false, if the query execution failed.
     */
    public function queryWithParams($query, $params = null)
    {
        if(!is_array($params) || empty($params)) {
            return $this->query($query);
        }

        $this->result = false;
        $this->lastStmt = $this->conn->stmt_init();
        if($this->lastStmt->prepare($query)) {
            $binderList = array();
            $format = "";
            $new_params = array();
            for ($i = 0; $i < count($params); $i++) {
                $new_params[$i + 1] = $params[$i];
                if (is_string($params[$i])) {
                    if (strlen($params[$i]) > 65535) {
                        $format .= "b";
                    } else {
                        $format .= "s";
                    }
                } else if (is_integer($params[$i])) {
                    $format .= "i";
                } else if (is_double($params[$i])) {
                    $format .= "d";
                } else {
                    $format .= "s";
                }
            }

            $tmpList = array();
            $new_params[0] = $format;

            //loop through and perform pointer arithmetic. I know, this is horrible
            for ($i = 0; $i < count($new_params); $i++) {
                $tmp = $new_params[$i];
                $tmpList[] = $tmp;
                $binderList[] = &$tmpList[$i];
            }

            $ref = new ReflectionClass('mysqli_stmt');
            $method = $ref->getMethod("bind_param");
            $method->invokeArgs($this->lastStmt, $binderList);
            $r = $this->lastStmt->execute();
            if(!$r) {
                print_r(array($query, $binderList, $this->getErrorText()));

            }
            return $r;
        }

        return false;
    }

    public function query($query) {
        $this->lastStmt = NULL;
        $this->result = $this->conn->query($query);
        return $this->result;
    }

    /**
     * Returns the id of the last inserted row.
     * For some database drivers (e.g. PostgreSQL), this may not return
     * any useful information.
     * @return string The row id.
     */
    public function lastInsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Returns the error message associated with the last operation.
     * @return string The error message.
     */
    public function getErrorText()
    {
        if(!$this->lastStmt)
            return $this->conn->error;
        return $this->lastStmt->error;
    }

    /**
     * Returns the whole result set.
     * @return array The result set.
     */
    public function fetchAll()
    {
        $array = array();
        while($row = $this->fetch())
        {
            $array[] = $row;
        }
        return $array;
    }

    public function getLastError() {
        return $this->getErrorText();
    }
    /**
     * Returns the next result row.
     * @return array|bool The current row, or false.
     */
    public function fetch()
    {
        if($this->result) {
            return $this->result->fetch_array(MYSQLI_NUM);
        }

        if(!$this->lastStmt) {
            return FALSE;
        }

        $meta = $this->lastStmt->result_metadata();
        $variables = array();
        $data = array();

        while($field = $meta->fetch_field()) {
            $variables[] = &$data[$field->name]; // pass by reference
        }
        call_user_func_array    (array($this->lastStmt, 'bind_result'), $variables);
        if($this->lastStmt->fetch()) {
            $array = array();
            $j = 0;
            foreach ($data as $k => $v) {
                $array[$j] = $v;
                $j++;
            }
            return $array;
        }
        return false;
    }


    private function getColumnCount() {
        if($this->result) {
            return mysqli_num_fields($this->result);
        } else if($this->lastStmt) {
            return mysqli_field_count($this->lastStmt);
        } else {
            return FALSE;
        }
    }

    private function getColumnMeta($i) {
        if($this->result) {
            return $this->result->fetch_field_direct($i);
        } else if($this->lastStmt) {
            return $this->lastStmt->getColumnMeta($i);
        } else {
            return FALSE;
        }
    }
    /**
     * Returns the result of the last query.
     * @return Result Result, consisting of header and the whole result set.
     */
    public function result() {
        $r = $this->fetchAll();
        $c = array();
        for($i = 0; $i < $this->getColumnCount(); $i++) {
            $col = $this->getColumnMeta($i);
            $c[] = is_array($col) ? $col['name'] : $col->name;
        }
        return new Result($r, $c);
    }

    /**
     * Starts a transaction and should be used to controll queries manually
     */
    public function startTransaction(){
        //echo "Changing autocommit:".$this->conn->autocommit(FALSE)."<br>";
        //echo "Starting Transaction:".$this->conn->begin_transaction()."<br>";
        $this->conn->autocommit(FALSE);
        $this->conn->begin_transaction();
    }

    /**
     * Ends the transaction with a commit or a rollback
     * @param $commit bool whether we should commit or rollback
     */
    public function endTransaction($commit){
        if ($commit)
            $this->conn->commit();
        else
            $this->conn->rollback();
        $this->conn->autocommit(TRUE);
    }
}
