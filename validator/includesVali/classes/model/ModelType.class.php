<?php

abstract class ModelObject {
    /**
     * Returns the name of the corresponding database table.
     * @return string Name of the database table.
     */
    public abstract function getTableName();

    /**
     * Returns the column prefix. Usually all database columns'
     * names start with this prefix.
     * @return string The column prefix.
     */
    public abstract function getPrefix();

    /**
     * Returns a list of database columns.
     * @return array Array containing the column names.
     */
    public abstract function getColumns();

    /**
     * Returns the name of the primary key column,
     * which should be a technical key column.
     * @return string The primary key column's name.
     */
    public static function getIdColumn() {
        return static::getPrefix() . '_id';
    }

    private function __construct($values) {
        $this->values = $values;
        if(empty($this->values)) {
            $this->new = true;
        }
    }


    /**
     * Delete the object from the database, if already persisted,
     * and reset all values.
     * @return bool Whether the deletion from database was successful.
     */
    public function delete() {
        global $db;
        if($this->new) {
            $this->values = array();
            return true;
        } else {
            $idColumn = static::getIdColumn();
            $sql = "delete from {$this->getTableName()} where {$idColumn} = ?";
            if($db->queryWithParams($sql, array($this->values[$idColumn]))) {
                $this->values = array();
                return true;
            }
            return false;
        }
    }

    /**
     * Updates the already existing object in database.
     * @return bool success
     */
    private function update() {
        global $db;

        $params = array();
        $clauses = array();
        foreach($this->values as $column => $value) {
            $clauses[] = "{$column} = ?";
            $params[] = $value;
        }

        $idColumn = static::getIdColumn();
        $clauses = join(', ', $clauses);
        $sql = "update {$this->getTableName()} set {$clauses} where {$idColumn} = ?";
        $params[] = $this->values[$idColumn];
        if($db->queryWithParams($sql, $params)) {
            return $this->requeryWithParams();
        }
        return false;
    }

    /**
     * Inserts the new object in database.
     * @return bool success
     */
    private function insert() {
        global $db;

        $value_list = array();
        $params = array();
        $column_list = array();

        foreach($this->values as $column => $value) {
            $value_list[] = "?";
            $params[] = $value;
            $column_list[] = $column;
        }

        $value_list = join(', ', $value_list);
        $column_list = join(', ', $column_list);

        $idColumn = static::getIdColumn();
        if($db->getType() == PostgreSQLConnection::TYPE) {
            $sql = "insert into {$this->getTableName()} ({$column_list}) values({$value_list}) returning {$idColumn}";
            if($db->queryWithParams($sql, $params)) {
                $row = $db->fetch();
                $this->values[$idColumn] = $row[0];
                return $this->requeryWithParams();
            }
        } else if($db->getType() == MariaDBConnection::TYPE) {
            $sql = "insert into {$this->getTableName()} ({$column_list}) values({$value_list})";
            if($db->queryWithParams($sql, $params)) {
                $this->values[$idColumn] = $db->lastInsertId();
                return $this->requeryWithParams();
            }
        }
        return false;
    }

    /**
     * Persists the changes to the database. If the object does not exist
     * in the database, it is inserted, otherwise updated.
     */
    public function save() {
        global $db;
        if($this->dirty) {
            if (!$this->new) {
                return $this->update();
            }
            return $this->insert();
        }
        return true;
    }

    /**
     * Requerys the object from database.
     * @return bool <code>true</code> if successful, <code>false</code>
     *      otherwise.
     */
    private function requeryWithParams() {
        global $db;
        $column_list = join(', ', static::getColumns());
        $idColumn = static::getIdColumn();
        $sql = "select {$column_list} from {$this->getTableName()} where {$idColumn} = ?";
        if($db->queryWithParams($sql, array($this->values[$this->getIdColumn()]))) {
            $this->values = array();
            $row = $db->fetch();
            $columns = static::getColumns();
            for ($i = 0; $i < count($columns); $i++) {
                $this->values[$columns[$i]] = $row[$i];
            }
            $this->dirty = false;
            $this->new = false;
            return true;
        }
        return false;
    }

    /**
     * Creates a new model object.
     * @return The new model object.
     */
    public static function create() {
        return new static(array());
    }

    /**
     * Convert camel-case notation to notation with underscores. E.g.,
     * <code>convertVariableToColumnName('IAmACamel')<code> returns
     * <code>i_am_a_camel</code>.
     * @param $var_name String in camel-case notation.
     * @return string String in notation with underscores.
     */
    private static function convertVariableToColumnName($var_name) {
        $columnName = "";
        for($i = 0; $i < strlen($var_name); $i++) {
            if($i > 0 && strtoupper($var_name[$i]) == $var_name[$i]) {
                $columnName = $columnName . '_' . strtolower($var_name[$i]);
            } else {
                $columnName = $columnName . strtolower($var_name[$i]);
            }
        }
        return static::getPrefix() . '_' . $columnName;
    }

    /**
     * Creates a new model object with the given values already set.
     * @param $row Array of values in same order as returned by
     *      <code>getColumn</code>.
     * @return New instance of model object.
     */
    private static function getByRow($row) {
        $columns = static::getColumns();
        for($i = 0; $i < count($columns); $i++) {
            $values[$columns[$i]] = $row[$i];
        }
        return new static($values);
    }

    /**
     * Returns an array of objects fulfilling the given condition.
     * @param null $condition The condition.
     *      Has to be a value where-clause (without where-keyword).
     * @param array $params The params. The n'th questionmark within
     *      the condition is replaced with the n'th param.
     * @return array|bool Array of objects or <code>false</code>
     *      in case of erroros.
     */
    public static function getByCondition($condition = null, $params = null, $orderCols = null) {
        global $db;
        $joinedColumns = join(', ', static::getColumns());
        $tableName = static::getTableName();
        $row = null;
        $result = array();
        $idColumn = static::getIdColumn();

        if(is_null($orderCols)) {
            $orderCols = array($idColumn);
        }
        $orderCols = join(', ', $orderCols);

        if(!is_null($condition)) {
            $sql = "select {$joinedColumns} from {$tableName} where {$condition} order by {$orderCols}";
            if(!$db->queryWithParams($sql, $params)) {
                print_r($db->getErrorText());
                return false;
            }
        } else {

            $sql = "select {$joinedColumns} from {$tableName} order by {$orderCols}";

            if(!$db->queryWithParams($sql)) {
                print_r($db->getErrorText());
                return false;
            }
        }

        while($row = $db->fetch()) {
            $result[] = static::getByRow($row);
        }
        return $result;
    }

    /**
     * Returns the object with the given id.
     * @param $id The id.
     * @return The model object or <code>false</code>, if none.
     */
    public static function getById($id) {
        if(!$id) {
            return FALSE;
        }
        $objs = static::getByCondition(static::getIdColumn() . ' = ?', array($id));
        return is_array($objs) && count($objs) == 1 ? $objs[0] : false;
    }

    /**
     * Returns an array of all objects of this type existing in the database.
     * Be careful, for large databases, this can take some time and memory!
     * @return array|bool Array of all objects or <code>false</code> in case
     *                      of errors.
     */
    public static function getAll($orderCols = null) {
        return static::getByCondition(null, array(), $orderCols);
    }
}