<?php

require_once __DIR__ . '/ModelObject.class.php';

class TaskPreparation extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task_preparation";
    }

    public static function getPrefix() {
        return "tskp";
    }

    public static function getColumns()
    {
        return array(
            "tskp_id",
            "tskp_lang",
            "tskp_tsk_id",
            "tskp_sql"
        );
    }
    
    //created on 10.05.17
    //return the array of the columns without tskp_id, necessary for the function save() in ModelObject.class.php
    public static function getColumnsToInsert() 
    {
    	return array(
    			"tskp_lang",
    			"tskp_tsk_id",
    			"tskp_sql"
    	);
    }
}

?>