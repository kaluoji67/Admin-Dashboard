<?php

require_once __DIR__ . '/PDODbConnection.class.php';

/**
 * Class handling connections to MySQL- and MariaDB-databases.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class MySQLConnection extends DbConnection {
    const TYPE = "mysql";

    private $conn;
    private $lastStmt;
    private $result;

    /**
     * @param $hostname The host to connect to.
     * @param $username The username.
     * @param $password The password.
     * @param $dbname The database to use.
     */
    public function __construct($hostname, $username, $password, $dbname) {
        $this->conn = mysql_connect($hostname, $username, $password, $dbname);
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
        $this->lastStmt = $this->conn->prepare($query);
        if($this->lastStmt) {
            if (is_array($params)) {
                $format = "";
                $new_params = array();
                for($i = 0; $i < count($params); $i++) {
                    $new_params[$i] = $params[$i - 1];
                    if(is_string($params[$i])) {
                        if(strlen($params[$i]) > 65535) {
                            $format .= "b";
                        } else {
                            $format .= "s";
                        }
                    } else if(is_integer($params[$i])) {
                        $format .= "i";
                    } else if(is_double($params[$i])) {
                        $format .= "d";
                    }
                }
                $new_params[0] = $format;
                $ref    = new ReflectionClass('mysqli_stmt');
                $method = $ref->getMethod("bind_param");
                $method->invokeArgs($this->lastStmt, $new_params);
                return $this->result = $this->lastStmt->execute();
            } else {
                return $this->result = $this->lastStmt->execute();
            }
        }
        return false;
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
        return $this->lastStmt->error;
    }

    /**
     * Returns the whole result set.
     * @return array The result set.
     */
    public function fetchAll()
    {
        return $this->result->fetch_all(MYSQLI_NUM);
    }

    /**
     * Returns the next result row.
     * @return array|bool The current row, or false.
     */
    public function fetch()
    {
        return $this->result->fetch_array();
    }

    /**
     * Returns the result of the last query.
     * @return Result Result, consisting of header and the whole result set.
     */
    public function result()
    {
        return $this->result;
    }
}