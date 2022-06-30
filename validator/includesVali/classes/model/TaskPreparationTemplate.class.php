<?php

require_once __DIR__ . '/ModelObject.class.php';

class TaskPreparationTemplate extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task_preparation_template";
    }

    public static function getPrefix() {
        return "tskpt";
    }

    public static function getColumns()
    {
        return array(
            "tskpt_id",
            "tskpt_title",
            "tskpt_sql"
        );
    }
}

?>