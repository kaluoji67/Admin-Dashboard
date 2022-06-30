<?php

interface ITableDescriptor {
    /**
     * Returns the name of the corresponding database table.
     * @return string Name of the database table.
     */
    static function getTableName();

    /**
     * Returns the column prefix. Usually all database columns'
     * names start with this prefix.
     * @return string The column prefix.
     */
    static function getPrefix();

    /**
     * Returns a list of database columns.
     * @return array Array containing the column names.
     */
    static function getColumns();

    /**
     * Returns the name of the primary key column,
     * which should be a technical key column.
     * @return string The primary key column's name.
     */
    static function getIdColumn();
}

abstract class ModelObject implements ITableDescriptor {
    private $values = array();
    private $dirty = false;
    private $new = false;

    private function __construct($values) {
        $this->values = $values;
        if(empty($this->values)) {
            $this->new = true;
        }
    }

    static function getIdColumn() {
        return static::getPrefix() . '_id';
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
     * Delete the object from the database, if already persisted,
     * and reset all values.
     * @return bool Whether the deletion from database was successful.
     */
    public function delete() {
        global $db;
        
        //if task should be deleted, cut references to this task
        if($this->getIdColumn()==Task::getIdColumn())
        {
            $this->cutReferencesToTask();
        }
        
        //if taskgroup should be deleted, cut references to this taskgroup
        if($this->getIdColumn()==TaskGroup::getIdColumn())
        {
            $this->cutReferencesToTaskGroup();
        }
        
        //if preparation should be deleted, cut references to the belonging task
        if($this->getIdColumn()==TaskPreparation::getIdColumn())
        {
            $t=Task::getById($this->getTskId());
            $t->cutReferencesToTask();
        }
        
        
        //if solution should be deleted, cut references to the belonging task
        if($this->getIdColumn()==Statement::getIdColumn())
        {
            $t=Task::getById($this->getTskId());
            $t->cutReferencesToTask();
        }
        
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
    private function update($reorder=false) {
        global $db;
        
        //if reorder is false, the object was edited and references have to be cut
        //else if true, only the order is changed
        if(!$reorder)
        {
            //if the object is a task
            if($this->getIdColumn()==Task::getIdColumn())
            {
                //if this task references another
                if($this->getCopyofTskId()==null)
                {
                    $this->cutReferencesToTask();
                }
                //else it may be referenced, so cut this references
                else
                {
                    $this->dereferenceTask($this);
                    $this->setCopyofTskId(null);
                }
            }
            
            //if the object is a preparation
            if($this->getIdColumn()==TaskPreparation::getIdColumn())
            {
                //get the connected task
                $t=Task::getById($this->getTskId());
                
                //if this task references another
                if($t->getCopyofTskId()==null)
                {
                    $t->cutReferencesToTask();
                }
                //else it may be referenced, so cut this references
                else
                {
                    $t->dereferenceTask($t);
                    $t->setCopyofTskId(null);
                }
            }
            
            //if the object is a statement
            if($this->getIdColumn()==Statement::getIdColumn())
            {
                //get the connected task
                $t=Task::getById($this->getTskId());
                
                //if this task references another
                if($t->getCopyofTskId()==null)
                {
                    $t->cutReferencesToTask();
                }
                //else it may be referenced, so cut this references
                else
                {
                    $t->dereferenceTask($t);
                    $t->setCopyofTskId(null);
                }
            }
            
            //if the object is a taskgroup
            if($this->getIdColumn()==TaskGroup::getIdColumn())
            {
                //if this taskgroup references another
                if($this->getCopyofTskgId()==null)
                {
                    $this->cutReferencesToTaskGroup();
                }
                //else it may be referenced, so cut this references
                else
                {
                    $this->dereferenceTaskGroup($this);
                    $this->setCopyofTskgId(null);
                }
            }
        }
        
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
        
        //if copy is set, get the value of copy
        if(isset($_POST["copy"]))
        {
            $copy = $_POST["copy"];
        }
        
        
        //if copySem is set, get the value of copySem
        if(isset($_POST["copySem"]))
        {
            $copySem = $_POST["copySem"];
        }

        foreach($this->values as $column => $value) {
            if(!(($column==Task::getIdColumn())||($column==TaskGroupLocalization::getIdColumn())||($column==TaskGroup::getIdColumn())))
            {
                $value_list[] = "?";
                $params[] = $value;
                $column_list[] = $column;
            }
            //do the same, when copy is set false or isn't set
            //do nothing, if copy is true
            elseif(((!isset($copy))||($copy=='false'))&&((!isset($copySem))||($copySem=='false')))
            {
                $value_list[] = "?";
                $params[] = $value;
                $column_list[] = $column;
            }
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
        } else if($db->getType() == 'mysql') {
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
     * in the database, it is inserted, otherwise updates.
     */
    public function save($reorder = null, $ignoreCopy = null) {
        //reorder:
        //true -> object got new order, nothing else changed
        //false -> object was edited
        //null -> like false
        
        //ignoreCopy
        //true -> if object is new, it will be inserted into the db without reacting to copy or copysem before the insert
        //false -> copy and copysem have to be respected
        //null -> like false
        
        
        //if id is set, get the value of id
        if(isset($_POST["id"]))
        {
            $id = $_POST["id"];
            if((strcmp($id,"copyTasks")==0)||(strcmp($id,"copyTaskGroups")==0))
            {
                $id=$this->getId();
                $this->setId(null);
            }
        }
        
        //if copy is set, get the value of copy
        if(isset($_POST["copy"]))
        {
            $copy = $_POST["copy"];
        }
        
        //if copy is set, get the value of copy
        if(isset($_POST["copySem"]))
        {
            $copySem = $_POST["copySem"];
        }
        
        global $db;
        $result = true;
        
        if($this->dirty) {
            /*if a "object" exists and copy isn't set or value of copy is false, the datas will be
             * updated in the database
             */
            if (($reorder)||((!$this->new)&&((!isset($copy))||($copy=='false'))&&((!isset($copySem))||($copySem=='false')))) {
                return $this->update($reorder);
            } 
            //created on 10.05.17
            else { 
                if(((!isset($ignoreCopy))||(!$ignoreCopy))&&(isset($copySem))&&($copySem=='true')&&(($this->getPrefix()=="tsk"||($this->getPrefix()=="tskg")))){
                     //important for copying into another Semester
                    //if false -> reference is enough -> add referenced Task-ID
                    //if true -> has to be a completely new task with localization, solution and preparation -> referenced Task-ID is NULL
                    $localizationHasChanged=false;
                    
                    if(strcmp($_POST["changeMarker"],"1")==0)
                    {
                        $localizationHasChanged=true;
                    }
                    
                    //if localizations has not changed, add the reference to the copied task
                    if(!$localizationHasChanged)
                    {
                        if($this->getIdColumn()==Task::getIdColumn())
                        {
                            $temp=Task::getById($id);
                            //if task, that should be referenced, references no other task
                            if($temp->getCopyofTskId()==null)
                            {
                                $this->setCopyofTskId($id);
                            }
                            else
                            {
                                $this->setCopyofTskId($temp->getCopyofTskId());
                            }
                        }
                        else
                        {
                            $temp=TaskGroup::getById($id);
                            //if taskgroup, that should be referenced, references no other taskgroup
                            if($temp->getCopyofTskgId()==null)
                            {
                                $this->setCopyofTskgId($id);
                            }
                            else
                            {
                                $this->setCopyofTskgId($temp->getCopyofTskgId());
                            }
                        }
                    }
                    else 
                    {
                        if($this->getIdColumn()==Task::getIdColumn())
                        {
                            if($this->getCopyofTskId()!=null)
                            {
                                $this->setCopyofTskId(null);
                            }
                        }
                        else
                        {
                            if($temp->getCopyofTskgId()!=null)
                            {
                                $this->setCopyofTskgId(null);
                            }
                        }
                    }
                    $this->setSemId($_POST["semester"]);
                }
                
                //otherwise it is new "object", so insert
                $temp = $this->insert();
                
                //if a taskgroup is copied to another semester, all tasks, have to be copied to (like "copy to semester")
                if((isset($copySem))&&($copySem=='true')&&($this->getIdColumn()==TaskGroup::getIdColumn()))
                {
                    $this->referenceTasksForTaskGroup();
                }
                
                
                /* if copy is set and copy is true, the preparation
                 * and the solution of the original task have to copy too
                */
                if(isset($copy)&&$copy=='true'){
                    if(($this->getIdColumn()==Task::getIdColumn())&&($this->getCopyofTskId()==null))
                    {
                        //get the new id via function getIdColumn
                        $idColumn = static::getIdColumn();
                        $newID =  $this->values[$idColumn];
                        //get the preparation of the task via the original id
                        $p = TaskPreparation::getByCondition('tskp_tsk_id = ?', array($id));
                        //get the solution of the task via the original id
                        $s = Statement::getByCondition('stmt_tsk_id = ?', array($id));
                        
                        foreach($p AS $currPrep)
                        {
                            //get columnlist via getColumnsToInsert() from TaskPreparation.class.php
                            $columnlist_p = $currPrep->getColumnsToInsert();
                            //the valuelist consists of 3 elements
                            $valuelist_p = "?,?,?";
                            //the params are language, the new id and the sql data
                            $params_p = array($currPrep->getLang(),$newID,$currPrep->getSql());
                            //for each preparation of the task, check if it is already copied
                            if($idColumn=="tsk_id") 
                            {
                                //join: convert an array to string
                                $columnlist_p = join(', ', $columnlist_p);
                                //the sql statement is an insert in to the database
                                $sql_p = "insert into {$currPrep->getTableName()} ({$columnlist_p}) values({$valuelist_p})";
                                //via queryWithParams in the sql statement the valuelist would replace with the params
                                //and uploaded to the database
                                $db->queryWithParams($sql_p, $params_p);
                            }
                        }
                        
                        //the same for the solution
                        foreach($s AS $currSol)
                        {
                            //get columnlist via getColumnsToInsert() from Statement.class.php
                            $columnlist_s = $currSol->getColumnsToInsert();
                            //the valuelist consists of 5 elements
                            $valuelist_s = "?,?,?,?,?";
                            //the params are title, language, the new id, the sql actual data and the sql desired data
                            $params_s = array($currSol->getTitle(),$currSol->getLang(),$newID,$currSol->getSqlActual(),$currSol->getSqlDesired());
                            //for each solution of the task, check if it is already copied
                            if($idColumn=="tsk_id") 
                            {
                                //join: convert an array to string
                                $columnlist_s = join(', ', $columnlist_s);
                                //the sql statement is an insert in to the database
                                $sql_s = "insert into {$currSol->getTableName()} ({$columnlist_s}) values({$valuelist_s})";
                                //via queryWithParams in the sql statement the valuelist would replace with the params
                                //and uploaded to the database
                                $db->queryWithParams($sql_s, $params_s);
                            }
                        }
                    }
                } 
                return $temp;
            }
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
    public static function getByCondition($condition = null, $params = array(), $orderCols = null) {
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

        $sql = '';
        if(!is_null($condition)) {
            $sql = "select {$joinedColumns} from {$tableName} where {$condition} order by {$orderCols}";
            if(!$db->queryWithParams($sql, $params)) {
                return false;
            }
        } else {
            $sql = "select {$joinedColumns} from {$tableName} order by {$orderCols}";
            if(!$db->queryWithParams($sql)) {
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
    
    //copies all task from the referenced taskgroup to this taskgroup
    //the copies are just referencing the original tasks (like "copy to semester" on every single task)
    private function referenceTasksForTaskGroup()
    {
        //check if the current object is a taskgroup
        //and this taskgroup is referencing another
        if(($this->getIdColumn()==TaskGroup::getIdColumn())&&($this->getCopyofTskgId()!=null))
        {
            //get all important ids
            //id of current taskgroup
            $ID=$this->getId();
            //id of referenced taskgroup
            $refID=$this->getCopyofTskgId();
            //semester id
            $semID=$this->getSemId();
            
            //get all tasks that are connected with the referenced taskgroup
            $refTasks=Task::getByCondition("tsk_tskg_id=?",array($refID));
            
            //copy every task (like "copy to semester")
            foreach($refTasks as $t)
            {
                $t->setTskgId($ID);
                if($t->getCopyofTskId()==null)
                {
                    $t->setCopyofTskId($t->getId());
                }
                $t->setSemId($semID);
                $t->setId(null);
                //call save with reorder = false, ignoreCopy=true
                $t->save(false, true);
            }
        }
    }
    
    //cuts all references to the current task
    private function cutReferencesToTask()
    {
        $ID=$this->getId();
        
        //get all tasks, that reference the current task
        $t=Task::getByCondition("tsk_copyof_tsk_id=?", array($ID));
        
        //if there are some tasks
        if(count($t)>=1)
        {
            //cut reference from first task
            $this->dereferenceTask($t[0]);
            $newID=$t[0]->getId();
            //all other tasks, reference the first, which is already dereferenced
            for($i=1;$i<count($t);$i++)
            {
                $t[$i]->setCopyofTskId($newID);
                $t[$i]->save(true);
            }
        }
    }
    
    //cuts the reference of $task, so that $task is a complete copy of the referenced task
    //if $tskpId (preparation-id) or $tsksId (statement-id) is set, the id of the corresponding copied preparation/statement has to be returned
    public function dereferenceTask($task, $tskpId=null, $tsksId=null)
    {
        //check if $task is a Task-object and if its referencing another
        if(($task->getIdColumn()==Task::getIdColumn())&&($task->getCopyofTskId()!=null))
        {
            //id of $task
            $ID=$task->getId();
            //id of referenced task
            $refID=$task->getCopyofTskId();
            //referenced task
            $t=Task::getById($refID);
            
            //id, that has to be returned
            $returnId=null;
            
            //preparations:
            $refP=$t->getPreparations();
            //copy all preparations
            foreach($refP as $currP)
            {
                $newP=TaskPreparation::create();
                $newP->setTskId($ID);
                $newP->setLang($currP->getLang());
                $newP->setSql($currP->getSql());
                $newP->save();
                
                //check, if an preparation-id has to be returned
                if(!empty($tskpId))
                {
                    //check if id of this copy has to be returned
                    if($currP->getId()==$tskpId)
                    {
                        $returnId=$newP->getId();
                    }
                }
            }
            
            //statements:
            $refS=$t->getStatements();
            //copy all statements
            foreach($refS as $currS)
            {
                $newS=Statement::create();
                $newS->setTskId($ID);
                $newS->setTitle($currS->getTitle());
                $newS->setLang($currS->getLang());
                $newS->setSqlDesired($currS->getSqlDesired());
                $newS->setSqlActual($currS->getSqlActual());
                $newS->save();
                
                //check, if an statement-id has to be returned
                if(!empty($tsksId))
                {
                    //check if id of this copy has to be returned
                    if($currS->getId()==$tsksId)
                    {
                        $returnId=$newS->getId();
                    }
                }
            }
            
            //localizations:
            $refL=TaskLocalization::getByCondition("tskl_tsk_id=?",array($refID));
            //copy all localizations
            foreach($refL as $currL)
            {
                $newL=TaskLocalization::create();
                $newL->setTskId($ID);
                $newL->setLang($currL->getLang());
                $newL->setTitle($currL->getTitle());
                $newL->setDescription($currL->getDescription());
                $newL->save();
            }
            
            //$task is no longer referencing another
            $task->setCopyofTskId(null);
            $task->save(true);
            
            //if an id has to be returned, return it
            if(!empty($returnId))
            {
                return $returnId;
            }
        }
    }
    
    //cuts all references to the current taskgroup
    private function cutReferencesToTaskGroup()
    {
        $ID=$this->getId();
        //get all taskgroup, that reference the current taskgroup
        $t=TaskGroup::getByCondition("tskg_copyof_tskg_id=?", array($ID));
    
        //if there are some taskgroups
        if(count($t)>=1)
        {
            //first taskgroup has to be fully dereferenced
            $this->dereferenceTaskGroup($t[0]);
            $newID=$t[0]->getId();
            
            //all other taskgroups reference to the first dereferenced taskgroup
            for($i=1;$i<count($t);$i++)
            {
                $t[$i]->setCopyofTskgId($newID);
                $t[$i]->save(true);
            }
        }
    }
    
    //cuts the reference of $taskGroup, so that $taskGroup is a complete copy of the referenced taskgroup
    private function dereferenceTaskGroup($taskGroup)
    {
        //check if $taskGroup is a taskgroup and if its referencing another
        if(($taskGroup->getIdColumn()==TaskGroup::getIdColumn())&&($taskGroup->getCopyofTskgId()!=null))
        {
            //id of $taskGroup
            $ID=$taskGroup->getId();
            //id of referenced taskgroup
            $refID=$taskGroup->getCopyofTskgId();
            //referenced taskgroup
            $t=TaskGroup::getById($refID);
            
            //localizations:
            $refL=TaskGroupLocalization::getByCondition("tskgl_tskg_id=?",array($refID));
            //copy all localizations
            foreach($refL as $currL)
            {
                $newL=TaskGroupLocalization::create();
                $newL->setTskgId($ID);
                $newL->setLang($currL->getLang());
                $newL->setName($currL->getName());
                $newL->save();
            }
    
            //$taskGroup is no longer referencing another taskgroup
            $taskGroup->setCopyofTskgId(null);
            $taskGroup->save(true);
        }
    }
}