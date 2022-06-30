<?php

abstract class BaseClassEval {
    private $values = array();
    private $dirty = false;
    private $new = false;
    private $primaryK = array();//Saving the primary key separately to allow fast access

    /**
     * Returns the name of the corresponding database table.
     * @return string Name of the database table.
     */
    public abstract static function getTableName();

    /**
     * Returns the column prefix. Usually all database columns'
     * names start with this prefix.
     * @return string The column prefix.
     */
    public abstract static function getPrefix();

    /**
     * Returns a list of database columns.
     * @return array Array containing the column names.
     */
    public abstract static function getColumns();

    /**
     * Returns the name of the primary key columns,
     * @return array The primary key column's name.
     */
    public abstract static function getPKColumns();

    /**
     * Returns the value of the primary keys to identify the object
     * @return array The primary Key values
    */
    public function getPKs()
    {
        return $this->primaryK;
    }

    private function __construct($values,$primaryK=array()) {
        $this->values = $values;
        $this->primaryK = $primaryK;
        if(empty($this->values)) {
            $this->new = true;
        }
    }

    public function __call($name, $arguments) {
        if(strpos($name, 'get') === 0) {
            if(count($arguments) != 0) {
                throw new Exception("Calls for get-methods on this object must not have any arguments.");
            }

            $varName = substr($name, 3);
            $colName = static::convertVariableToColumnName($varName);
            if(in_array($colName, static::getColumns())) {
                return array_key_exists($colName, $this->values) ? $this->values[$colName] : null;
            } else {
                throw new Exception("There is no column named '{$colName}' defined.");
            }
        } else if(strpos($name, 'set') === 0) {
            if(count($arguments) != 1) {
                throw new Exception("Calls for set-method on this object must have exact one argument.");
            }

            $varName = substr($name, 3);
            $colName = static::convertVariableToColumnName($varName);
            if(in_array($colName, static::getColumns())) {
                $this->values[$colName] = $arguments[0];
                $this->dirty = true;
            } else {
                throw new Exception("There is no column named '{$colName}' defined.");
            }
        } else {
            throw new Exception("There is no such function.");
        }
    }

    /**
     * Can be called to directly insert data into the database
     * @param $tableName Name of the table to be inserted
     * @param array $columnNames columns that should be inserted into
     * @param array $vals values to be inserted !-! Have to be the same order as columns !-!
     * @return Bool returns the success value
     */
    public static function basicInsert($tableName,$columnNames=array(),$vals=array())
    {
        global $dbEval;
        $value_list = array();
        foreach ($vals as $val)
            $value_list[] = "?";

        $value_list = join(",",$value_list);
        $column_list = join(",",$columnNames);
        $sql = "insert into {$tableName} ({$column_list}) values ( {$value_list} )";
        if (!$dbEval->queryWithParams($sql,$vals)) {
            print_r($dbEval->getErrorText());
            return FALSE;
        }

        return true;
    }

    /**
     * Creates a new object.
     * @return The new object.
     */
    public static function create() {
        return new static(array(),array());
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
        $primaryK = array();
        $pkColumns = static::getPKColumns();
        for($i = 0; $i < count($columns); $i++) {
            $values[$columns[$i]] = $row[$i];
            if (in_array($columns[$i],$pkColumns))
                $primaryK[] = $row[$i];
        }
        return new static($values,$primaryK);
    }

    /**
     * Returns an array of objects fulfilling the given condition.
     * @param null $condition The condition.
     *      Has to be a value where-clause (without where-keyword).
     * @param array $params The params. The n'th questionmark within
     *      the condition is replaced with the n'th param.
     * @return array|bool Array of objects or <code>false</code>
     *      in case of errors.
     */
    public static function getByCondition($condition = null, $params = array(), $orderCols = null) {
        global $dbEval;
        $joinedColumns = join(', ', static::getColumns());
        $tableName = static::getTableName();
        $row = null;
        $result = array();
        $pkColumns = static::getPKColumns();

        if(is_null($orderCols)) {
            $orderCols = $pkColumns;
        }
        $orderCols = join(', ', $orderCols);

        if(!is_null($condition)) {
            $sql = "select {$joinedColumns} from {$tableName} where {$condition} order by {$orderCols}";
            if(!$dbEval->queryWithParams($sql, $params)) {
                print_r($dbEval->getErrorText());
                return false;
            }
        } else {
            $sql = "select {$joinedColumns} from {$tableName} order by {$orderCols}";
            if(!$dbEval->query($sql)) {
                print_r($dbEval->getErrorText());
                return false;
            }
        }

        while($row = $dbEval->fetch()) {
            $result[] = static::getByRow($row);
        }
        return $result;
    }

    /**
     * Returns an array fulfilling the given condition natural joined with the given tables.
     * @param array $tables The to be joined table names
     *      has to be an array of tables
     * @param array $columns The column Names of the joined tables
     * @param null $condition The condition.
     *      Has to be a value where-clause (without where-keyword).
     * @param array $params The params. The n'th questionmark within
     *      the condition is replaced with the n'th param.
     * @return array|bool Array of pure values or <code>false</code>
     *      in case of errors.
     */
    public static function getGeneralConditionedJoined($tables=array(), $columns=array(), $condition = null, $params = array(), $orderCols = null) {
        global $dbEval;
        $joinedColumns = join(', ', $columns);
        $joinedTables = join(' NATURAL JOIN ', $tables);
        $row = null;
        $result = array();

        if(is_null($orderCols) AND in_array(static::getTableName(),$tables)) {
            $orderCols = static::getPKColumns();
        }
        if (!is_null($orderCols)) {
            $orderCols = join(', ', $orderCols);
            $orderCols = " order by " . $orderCols;
        }
        else
            $orderCols = "";

        if(!is_null($condition)) {
            $sql = "select {$joinedColumns} from {$joinedTables} where {$condition}{$orderCols}";
            if(!$dbEval->queryWithParams($sql, $params)) {
                print_r($dbEval->getErrorText());
                return false;
            }
        } else {
            $sql = "select {$joinedColumns} from {$joinedTables}{$orderCols}";
            if(!$dbEval->query($sql)) {
                print_r($dbEval->getErrorText());
                return false;
            }
        }

        while($row = $dbEval->fetch()) {
            $innerRes = array();
            $i = 0;
            foreach($columns as $column){
                $innerRes[$column] = $row[$i];
                $i++;
            }/* --old TODO:remove
            for($i = 0; $i < count($columns); $i++) {
                echo $columns[$i]."<br>";
                $innerRes[$columns[$i]] = $row[$i];
            }*/
            $result[] = $innerRes;
        }
        return $result;
    }

    /**
     * Returns the object with the given primaryKey
     * @param array $pk The primary key values
     * @return The model object or <code>false</code>, if none.
     */
    public static function getByPK($pk) {
        $pkCols = static::getPKColumns();
        if (count($pkCols) != count($pk))
            throw new Exception("Primary Key Mismatch: Must provide all primary key values. (BaseClassEval::getById)");
        $where = join(" = ? AND ",$pkCols);
        $where .= "= ?";
        $objs = static::getByCondition($where, $pk);
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

    /**
     * General possibility to update Entries within the specific class
     * @param $cols array Updates columns
     * @param $pks array Primary keys or part of the primary keys of the entry to update
     * @param $newValues array the new values
     * @param $table string an alternate hardcoded table that does not respond to the base class table
     * @param $pkCols array alternate primary columns that respond to the alternate table
     * @param $condition string an extra condition that allows to further define the Where clause - for example updating the variables in a broader scope
     * @return bool
     * @throws Exception
     */
    public static function UpdateEntries($cols,$pks,$newValues,$table = null,$pkCols = null,$condition = null){
        global $dbEval;
        $pkCols = $pkCols == null ? static::getPKColumns() : $pkCols;
        $table = $table == null ? static::getTableName() : $table;
        $sql = "UPDATE ".$table." SET ";
        if (count($cols) != count($newValues))
            throw new Exception("Parameter mismatch: Updated cols and new values must have same dimension. (BaseClassEval::UpdateEntries)");
        $newSetVals = array();
        //echo "<br><br</br>";var_dump($cols);
        foreach ($cols as $c)
            $newSetVals[] = $c." = ?";
        /*for($i=0;$i < count($cols);$i++)
            $newSetVals[] = $cols[$i]." = ?";*/
        $newSetVals = join(",",$newSetVals);
        $where = array();
        //for($i=0;$i < count($pks);$i++)
        //$where[] = $pkCols[$i]. " = ? ";
        foreach ($pkCols as $pkC)
            $where[] = $pkC. " = ? ";
        $where = join(" AND ",$where);
        if ($condition == null)
            $sql = $sql . $newSetVals." WHERE ".$where;
        else
            $sql = $sql . $newSetVals." WHERE ".$where." AND ".$condition;
        //echo "<br>Update Entries<br>".$sql."<br>"; var_dump(array_merge($newValues,$pks));
        if(!$dbEval->queryWithParams($sql,array_merge($newValues,$pks))) {
            //echo $sql."<br>";
            print_r($dbEval->getErrorText());
            return false;
        }
        return true;
    }

    /** Provide a possibility to erase entries without deleting them - aka set them back to default
     * @param $pks array Primary keys or part of the primary keys of the entry to update
     * @param $table string an alternate hardcoded table that does not respond to the base class table
     * @param $pkCols array alternate primary columns that respond to the alternate table
     * @param $cols array Updates columns
     * @param $condition string an extra condition that allows to further define the Where clause - for example updating the variables in a broader scope
     * @return bool
     * @throws Exception
     */
    public static function BlankEntries($pks,$table = null,$pkCols = null,$cols = null,$condition = null){
        $cols = $cols == null ? (array_diff(static::getColumns(),static::getPKColumns())) : $cols; //Set all columns to DEFAULT that are not primary keys
        $newVals = array();
        $newVals = array_pad($newVals,count($cols),"DEFAULT");

        return self::UpdateEntries($cols,$pks,$newVals,$table,$pkCols,$condition);
    }

    /** Allows to delete entries in a specific scope
     * @param $pks array Primary keys or part of the primary keys of the entry to update
     * @param $table string an alternate hardcoded table that does not respond to the base class table
     * @param $pkCols array alternate primary columns that respond to the alternate table
     * @param $condition string an extra condition that allows to further define the Where clause - for example updating the variables in a broader scope
     * @return bool
     */
    public static function DeleteEntries($pks,$table = null,$pkCols = null,$condition = null){
        global $dbEval;
        $pkCols = $pkCols == null ? static::getPKColumns() : $pkCols;
        $table = $table == null ? static::getTableName() : $table;
        $where = array();
        for($i=0;$i < count($pks);$i++)
            $where[] = $pkCols[$i]. " = ? ";
        $where = join(" AND ",$where);
        if ($condition != null)
            $where = $where." AND ".$condition;
        $sql = "DELETE FROM ".$table." WHERE ".$where;
        if(!$dbEval->queryWithParams($sql,$pks)) {
            print_r($dbEval->getErrorText());
            return false;
        }
        return true;
    }
}