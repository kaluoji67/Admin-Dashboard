<?php

require_once __DIR__ . '/ModelObject.class.php';

class Statement extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.statement";
    }

    public static function getPrefix() {
        return "stmt";
    }

    public static function getColumns()
    {
        return array(
            "stmt_id",
            "stmt_title",
            "stmt_lang",
            "stmt_tsk_id",
            "stmt_sql_actual",
            "stmt_sql_desired",
            "stmt_checknull",
            "stmt_checkdefault",
            "stmt_checkcase"
        );
    }
    
    //created on 10.05.17
    //return the array of the columns without stmt_id, necessary for the function save() in ModelObject.class.php
    public static function getColumnsToInsert() 
    {
    	return array(
    			"stmt_title",
    			"stmt_lang",
    			"stmt_tsk_id",
    			"stmt_sql_actual",
    			"stmt_sql_desired"
    	);
    }
}

?>