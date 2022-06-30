<?php

require_once __DIR__ . '/db/Result.class.php';

class Validator
{
    private $actualResult;
    private $desiredResult;
    private $compareHead = true; //Should we check the table head
    private $compareBody = true; //Should we check the table body
    private $compareColumnOrder = false;//Standard to false, because column order is never important for us
    private $compareRowOrder = true;//Standard true, da nur bei Tabellen überprüft
    private $checkForNULL = false;
    private $checkForDEFAULT = false;
    private $checkCase = false;
    private $columnMapping;
    private $success;
    private $errors = array();
    private $type;
    private $errorIndices = array();//stores all occurring error with their indices from the EvalModule dictionary

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function setActualResult(Result $actualResult)
    {
        $this->actualResult = $actualResult;
    }

    public function setDesiredResult(Result $desiredResult)
    {
        $this->desiredResult = $desiredResult;
    }

    public function setCheckNull($checkNull)
    {
        $this->checkForNULL = $checkNull;
    }

    public function setCheckDefault($checkForDEFAULT)
    {
        $this->checkForDEFAULT = $checkForDEFAULT;
    }

    public function setCheckCase($checkCase)
    {
        $this->checkCase = $checkCase;
    }

    /**
     * @return mixed
     */
    public function getDesiredResult()
    {
        return $this->desiredResult;
    }

    /**
     * @return mixed
     */
    public function getActualResult()
    {
        return $this->actualResult;
    }


    /**
     * @param bool $compareRowOrder
     */
    public function setCompareRowOrder($compareRowOrder)
    {
        $this->compareRowOrder = $compareRowOrder;
    }

    public function setCompareHead(\boolean $compareHead)
    {
        $this->compareHead = $compareHead;
    }

    public function setCompareBody(\boolean $compareBody)
    {
        $this->compareBody = $compareBody;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorIndices()
    {
        return $this->errorIndices;
    }

    public function validate()
    {
        //Initialise
        $this->errors = array();
        $success = false; //stores whether all checks where performed successfully

        /* //only check basic properties on Tables
         if ($this->type=="Table"){
             if( $this->validateBasics($this->errors)) {
                 if(!$this->compareHead || $this->validateHead($this->errors)) {
                     if(!$this->compareBody || $this->validateBody($this->errors)) {
                         $success = true;
                     }
                 }
             }

         }
         else {*/
        //Check whether the result set is empty for schemas and Constraints - This means that the table was not created with the right name
        if ($this->type == "Schema") {
            if (count($this->actualResult->getResultSet()) == 0) {
                $this->errors[] = "er00r_head_table_title";
                $this->errorIndices[] = 16;
            }
        }
        if (count($this->errors) == 0) {
            if (!$this->compareHead || $this->validateHead($this->errors, $this->errorIndices)) {//Check for the rest
                if (!$this->compareBody || $this->validateBody($this->errors, $this->errorIndices)) {
                    $success = true;
                }
            }
        }


        // }


        if ($success == false && $this->type == "Foreign Keys") {
            //$this->errors[] = "er00r_constraint_foreign_key";
        }

        if ($success == false && $this->type == "Constraints") {
            //$this->errors[] = "er00r_constraints";
        }

        $this->success = $success;
        return $success;
    }

    private function validateBasics(&$errors)
    {
        $actualRowCount = count($this->actualResult->getResultSet());
        $desiredRowCount = count($this->desiredResult->getResultSet());
        $actualColumnCount = count($this->actualResult->getColumns());
        $desiredColumnCount = count($this->desiredResult->getColumns());
        $result = true;


        if ($actualRowCount != $desiredRowCount) {

            if ($this->type == "Constraints") {
                $errors[] = "er00r_constraints";
            }

            $result = false;

        }

        return $result;
    }

    /** Performs the checks on the table header
     * @param &$errors reference parameter to write errors to object
     * @return bool whether there were any errors found
     */
    private function validateHead(&$errors, &$errorIndices)
    {
        $this->columnMapping = array();
        $desiredColumns = $this->desiredResult->getColumns();
        $actualColumns = $this->actualResult->getColumns();
        $noErrors = true;

        //Errortype: column count too low (only applied on tables, since on all other types the columns are constructed automatically)
        //"Column count should be ..., but is ..."
        if (count($desiredColumns) < count($actualColumns) || count($desiredColumns) > count($actualColumns)) {
            $errors[] = array("er00r_basic_error_column_count1", " ", count($desiredColumns), "er00r_basic_error_column_count2", " ", count($actualColumns));
            $errorIndices[] = 2;
            $noErrors = false;
        }        //compareColumnOrder is only important for comparing tables
        else if ($this->compareColumnOrder) {
            for ($i = 0; $i < count($desiredColumns); $i++) {
                $this->columnMapping[] = $i;
                //Errorype: "Column name should be ..., but is ..." //Attention error is no longer used and not represented in the language dictionaries
                if (mb_strtolower($desiredColumns[$i]) != mb_strtolower($actualColumns[$i])) {
                    $errors[] = array("er00r_head_error_column_count1 ", ($i + 1), "er00r_head_error_column_count2 ", " " . $desiredColumns[$i], "er00r_head_error_column_count3 ", " " . $actualColumns[$i], ".");
                    $errorIndices[] = 3;
                    $noErrors = false;
                }
            }
        } else {//Check whether the columns are named correctly
            for ($i = 0; $i < count($desiredColumns); $i++) {
                $found = false;
                $desiredColumn = mb_strtolower($desiredColumns[$i]);
                for ($j = 0; $j < count($actualColumns) & !$found; $j++) {
                    $actualColumn = mb_strtolower($actualColumns[$j]);
                    if ($actualColumn == $desiredColumn) {
                        $found = true;
                        $this->columnMapping[] = $j;
                    }
                }

                if (!$found) {
                    //Errortype: There should be a column named ..., but ... no, there isn't one. (only important for comparing tables)
                    $errors[] = array("er00r_head_error_column_missing1", " " . $desiredColumn . " ", "er00r_head_error_column_missing2");
                    $errorIndices[] = 4;
                    $noErrors = false;
                }
            }
        }

        return $noErrors;
    }

    private function validateBody(&$errors, &$errorIndices)
    {
        $actualRows = $this->actualResult->getResultSet();
        $desiredRows = $this->desiredResult->getResultSet();
        $actual = array();
        $desired = array();
        $noErrors = true;

        //represents each row in string. Columns are separated by §§
        foreach ($actualRows as $a) {
            $a = array_map("trim",$a);
            $str_repr = join("§§", $a);
            if (!$this->checkCase)
                $actual[] = mb_strtolower($str_repr);
        }


        //represents each row in string (in the same order as actual)
        foreach ($desiredRows as $desiredRow) {
            $a = array();
            for ($i = 0; $i < count($desiredRow); $i++) {
                $a[] = $desiredRow[$this->columnMapping[$i]];
            }
            $a = array_map("trim",$a);
            $str_repr = join("§§", $a);
            if (!$this->checkCase)
                $desired[] = mb_strtolower($str_repr);
        }

        //Check column/row count - dominates the other errors, because else the following errors would wrongfully trigger
        if (count($actual) != count($desired) && ($this->type == "Schema" || $this->type == "Table")) {
            if ($this->type == "Schema") {
                //Errortype: Column count should be ..., but is ...
                $errors[] = array("er00r_basic_error_column_count1", " ", count($desired), "er00r_basic_error_column_count2", " ", count($actual));
                $errorIndices[] = 5;
                $noErrors = false;
            }

            if ($this->type == "Table") {
                //Errortype: Row count should be ..., but is ...
                $errors[] = array("er00r_basic_error_row_count1", " ", count($desired), "er00r_basic_error_row_count2", " ", count($actual));
                $errorIndices[] = 6;
                $noErrors = false;
            }


        } else {
            //We now check tables content,schemas and constraints

            //compareRowOrder is only set for tables (order by statement) - Needs to be checked here, because in the following actual and desired are sorted
            if ($this->type == "Table" && $this->compareRowOrder) {
                $diff = ($actual !== $desired);
                //Errortype: There is a mistake in the row order.
                if ($diff) {
                    $noErrors = false;
                    $errors[] = "er00r_body_error_table";
                    $errorIndices[] = 21;
                }
            }
            /* sorts actual and desired and compares them. If a difference exists, the corresponding strings (which represent the row) are converted to arrays.
            These are compared, now to extract the information about what is wrong in particular (e.g. name, default value)
            */
            asort($actual);
            asort($desired);
            $desired_without_actual = implode(";", array_diff($desired, $actual));
            $actual_without_desired = implode(";", array_diff($actual, $desired));

            if (strlen($desired_without_actual) > 0 || strlen($actual_without_desired) > 0) {
                $desired_missing_array = explode('§§', $desired_without_actual);
                $actual_missing_array = explode('§§', $actual_without_desired);
                $diff_array = array_diff($desired_missing_array, $actual_missing_array);

                if ($this->type == "Table") {
                    $row = array();
                    for($i=0; $i < count($desiredRows);$i++){
                        if (!in_array($desiredRows[$i],$actualRows)){
                            $row[] = ($i+1);
                        }
                    }
                    $desc = " ".implode(",",$row)." / ".count($desiredRows);
                    if (count($row) > 0) {
                        $noErrors = false;
                        //Errortype: The content of your table is wrong. Please check row number ... again.
                        $errors[] = array("er00r_row_content", $desc, "er00r_row_content2");
                        $errorIndices[] = 20;
                    }
                } else if ($this->type == "Constraints") {
                    $noErrors = false;
                    //Errortype:
                    if (count($actual) > count($desired)) {// changed from || count($diff_array) == 1
                        //Errortype: Your solution contains too many constraints.
                        $errors[] = "er00r_constraint_diff";
                        $errorIndices[] = 7;
                    } else if (count($diff_array) == 1 && isset($diff_array[0])) {
                        //Constraint was correctly given, but at a wrong column
                        $pos = array_search($diff_array[0], $desired_missing_array);//search for the corresponding column
                        $errorType = $desired_missing_array[$pos + 1];//+1 to get type indicator
                        if ($errorType == "primary key") {
                            //Errortype: Your primary key seems to contain an error or is missing.
                            $errors[] = "er00r_constraint_primary_key";
                            $errorIndices[] = 8;
                        } else if ($errorType == "unique") {
                            //Errortype: Check again the property unique
                            $errors[] = "er00r_constraint_unique";
                            $errorIndices[] = 9;
                        } else if ($errorType == "foreign key") {
                            //Errortype: Your foreign key seems to contain an error or is missing.
                            $errors[] = "er00r_constraint_foreign_key";
                            $errorIndices[] = 10;
                        }
                    } else if ($diff_array[1] == "unique") {
                        //Errortype: Check again the property unique
                        $errors[] = "er00r_constraint_unique";
                        $errorIndices[] = 9;
                    } else if ($diff_array[1] == "primary key") {
                        //Errortype: Your primary key seems to contain an error or is missing.
                        $errors[] = "er00r_constraint_primary_key";
                        $errorIndices[] = 8;
                    } else if ($diff_array[1] == "foreign key") {
                        //Errortype: Your foreign key seems to contain an error or is missing.
                        $errors[] = "er00r_constraint_foreign_key";
                        $errorIndices[] = 10;
                    } else {
                        //Errortype: Foreign key and/ or primary key are incorrectly formed.
                        $errors[] = "er00r_constraints";
                        $errorIndices[] = 11;
                    }


                } /*
                else if ($this->type=="Foreign Keys"){
                    $this->errors[] = "er00r_constraints";
                }*/


                else if ($this->type == "Schema") {
                    //No errors is declared later, because of the optionality of some mistakes
                    $desired_without_actual_array = explode(';', $desired_without_actual);
                    $actual_without_desired_array = explode(';', $actual_without_desired);

                    //Loop through the desired array and perform row based comparison
                    //This is more calculative expensive, but it is easier to check
                    for ($i = 0; $i < count($desired_without_actual_array); $i++) {
                        $row_cols_desired = explode("§§", $desired_without_actual_array[$i]);
                        //Search for the column in the actual set
                        $foundColumn = false;
                        for ($j = 0; $j < count($actual_without_desired_array) && !$foundColumn; $j++) {
                            $row_cols_actual = explode("§§", $actual_without_desired_array[$j]);
                            if ($row_cols_desired[0] == $row_cols_actual[0]) {
                                $foundColumn = true;
                                //Check now for content violation
                                //Errortype: Some datatypes are wrong
                                if ($row_cols_desired[1] != $row_cols_actual[1] && !in_array(12, $errorIndices)) {
                                    $errors[] = "er00r_body_error_schema_data_type";
                                    $errorIndices[] = 12;
                                    $noErrors = false;
                                }
                                //Errortype: Check the property is_nullable for column ...
                                if ($row_cols_desired[2] != $row_cols_actual[2] && !in_array(13, $errorIndices) && $this->checkForNULL) {
                                    $errors[] = array("er00r_body_error_schema_is_nullable1", " ", ucfirst($row_cols_desired[0]), " ", "er00r_body_error_schema_is_nullable2");
                                    $errorIndices[] = 13;
                                    $noErrors = false;
                                }
                                //Errortype: Some default values are wrong
                                if ($row_cols_desired[3] != $row_cols_actual[3] && !in_array(14, $errorIndices) && $this->checkForDEFAULT) {
                                    $errors[] = "er00r_body_error_schema_default";
                                    $errorIndices[] = 14;
                                    $noErrors = false;
                                }
                            }
                        }
                        //A row name from the desired set is misspelled
                        if (!$foundColumn && count($actual) == count($desired)) {
                            //Check whether the error was already added
                            if (!in_array(1, $errorIndices)) {
                                //Errortype: The column names are not entirely correct
                                $errors[] = "er00r_body_error_schema_name";
                                $errorIndices[] = 15;
                                $noErrors = false;
                            }
                        }
                    }

                    /*                   OLD CHECK - is neat, but makes too many mistakes
                                       $counterColumns = 0;
                                       $optionalTriggered = false;
                                       $requiredTriggered = false;
                                       //Errortype: The column names are not entirely correct
                                       if ($desired_missing_array[0] !== $actual_missing_array[0]) {
                                           $errors[] = "er00r_body_error_schema_name";
                                           $requiredTriggered = true;
                                       } //Errortype: Some datatypes are wrong
                                       else if ($desired_missing_array[1] !== $actual_missing_array[1]) {
                                           $errors[] = "er00r_body_error_schema_data_type";
                                           $requiredTriggered = true;
                                       } //Errortype: Check the property is_nullable for column ...
                                       if (isset($desired_missing_array[2]) && isset($actual_missing_array[2]) && $desired_missing_array[2] !== $actual_missing_array[2]) {
                                           if ($this->checkForNULL) {
                                               $errors[] = array("er00r_body_error_schema_is_nullable1", " ", ucfirst($actual_missing_array[0]), " ", "er00r_body_error_schema_is_nullable2");
                                               $optionalTriggered = true;
                                           }
                                       } //Errortype: Some default values are wrong
                                       if (isset($desired_missing_array[3]) && isset($actual_missing_array[3]) && $desired_missing_array[3] !== $actual_missing_array[3]) {
                                           if ($this->checkForDEFAULT){
                                               $optionalTriggered = true;
                                               $errors[] = "er00r_body_error_schema_default";
                                           }
                                       }
                                       //Check whether an we should revoke the error
                                       //This is the case when the potential error was one that is not checked
                                       //Neither an optional nor an required error was actually triggered -> revoke the error
                                       if (!$optionalTriggered && !$requiredTriggered)
                                           $noErrors = true;*/
                }//Check for special foreign key violations
                else if ($this->type == "Foreign Keys") {
                    if (count($actual) == count($desired)) {
                        $desired_without_actual_array = explode(';', $desired_without_actual);
                        $actual_without_desired_array = explode(';', $actual_without_desired);
                        //Loop through the desired array and perform row based comparison
                        //This is more calculative expensive, but it is easier to check
                        for ($i = 0; $i < count($desired_without_actual_array); $i++) {
                            $row_cols_desired = explode("§§", $desired_without_actual_array[$i]);
                            //Search for the column in the actual set
                            $foundColumn = false;
                            for ($j = 0; $j < count($actual_without_desired_array) && !$foundColumn; $j++) {
                                $row_cols_actual = explode("§§", $actual_without_desired_array[$j]);
                                if ($row_cols_desired[0] == $row_cols_actual[0]) {
                                    $foundColumn = true;
                                    //Check now for content violation
                                    //Errortype: Referenced table name is wrong
                                    if ($row_cols_desired[1] != $row_cols_actual[1] && !in_array(18, $errorIndices)) {
                                        $errors[] = "er00r_body_error_FK_RefTable";
                                        $errorIndices[] = 18;
                                        $noErrors = false;
                                    }
                                    //Errortype: The Name of the Column in the referenced table
                                    if ($row_cols_desired[2] != $row_cols_actual[2] && !in_array(19, $errorIndices)) {
                                        $errors[] = "er00r_body_error_FK_RefTableName";
                                        $errorIndices[] = 19;
                                        $noErrors = false;
                                    }
                                }
                            }
                            //A row name from the desired set is misspelled
                            if (!$foundColumn && count($actual) == count($desired)) {
                                //Check whether the error was already added
                                if (!in_array(1, $errorIndices)) {
                                    //Errortype: The column names are not entirely correct
                                    $errors[] = "er00r_body_error_FK_name";
                                    $errorIndices[] = 17;
                                    $noErrors = false;
                                }
                            }
                        }


                    } else {
                        //error of missing or too many foreign keys
                        // -> error was most likely already tangled by Constraint statement ->Just show that the Foreign key is wrong
                        $noErrors = false;
                    }
                }

            }
        }

        return $noErrors;
    }
}
