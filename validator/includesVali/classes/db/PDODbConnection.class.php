<?php

require_once __DIR__ . '/DbConnection.class.php';

/**
 * Class handling connections to all databases supported by PDO.
 *
 * @author Janice Schmidtke <janice.schmidtke@st.ovgu.de>
 * @author Alice Stang <alice.stang@st.ovgu.de>
 * @author Sören Prilop <soeren.prilop@st.ovgu.de>
 */
class PDODbConnection extends DbConnection {
    private $conn;
    private $lastStmt;
    private $type;

    /**
     * @param $dsn The data source name
     * @param $username The username
     * @param $password The password
     */
    public function __construct($type, $dsn, $username, $password) {
        $this->type = $type;
        $this->conn = new PDO($dsn, $username, $password);
    }

    /** @inheritdoc */
    public function getType() {
        return $this->type;
    }

    public function getLastError() {
        return $this->getErrorText();
    }

    /** @inheritdoc */
    public function queryWithParams($query, $params = null) {
        $this->lastStmt = $this->conn->prepare($query);
        if($this->lastStmt) {
            if (is_array($params)) {
                for($i = 0; $i < count($params); $i++) {
                    if(is_string($params[$i]) && strlen($params[$i]) > 65535) {
                        $this->lastStmt->bindValue($i+1, $params[$i], PDO::PARAM_LOB);
                    } else {
                        $this->lastStmt->bindValue($i+1, $params[$i]);
                    }
                }
                return $this->lastStmt->execute();
            } else {
                return $this->lastStmt->execute();
            }
        }
        return false;
    }

    public function query($query) {

    }

    /** @inheritdoc */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /** @inheritdoc */
    public function getErrorText() {
        $errorInfo = $this->lastStmt->errorInfo();
        return $errorInfo[2];
    }

    /** @inheritdoc */
    public function fetchAll() {
        $rs = $this->lastStmt->fetchAll(PDO::FETCH_NUM);
        return $rs;
    }

    /** @inheritdoc */
    public function fetch() {
        return $this->lastStmt->fetch();
    }

    /** @inheritdoc */
    public function result() {
        $r = $this->fetchAll();
        $c = array();
        for($i = 0; $i < $this->lastStmt->columnCount(); $i++) {
            $col = $this->lastStmt->getColumnMeta($i);
            $c[] = $col['name'];
        }
        return new Result($r, $c);
    }
}