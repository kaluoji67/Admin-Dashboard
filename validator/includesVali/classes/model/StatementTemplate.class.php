<?php

require_once __DIR__ . '/ModelObject.class.php';

class StatementTemplate extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.statement_template";
    }

    public static function getPrefix() {
        return "stmtt";
    }

    public static function getColumns()
    {
        return array(
            "stmtt_id",
            "stmtt_title",
            "stmtt_sql_actual",
            "stmtt_sql_desired"
        );
    }
}

?>