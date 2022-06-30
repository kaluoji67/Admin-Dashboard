<?php

require_once __DIR__ . '/ModelObject.class.php';

class TaskLocalization extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task_localization";
    }

    public static function getPrefix() {
        return "tskl";
    }

    public static function getColumns()
    {
        return array(
            "tskl_id",
            "tskl_tsk_id",
            "tskl_lang",
            "tskl_title",
            "tskl_description"
        );
    }
}

?>