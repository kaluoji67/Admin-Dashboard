<?php

require_once __DIR__ . '/ModelObject.class.php';

class TaskGroupLocalization extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task_group_localization";
    }

    public static function getPrefix() {
        return "tskgl";
    }

    public static function getColumns()
    {
        return array(
            "tskgl_id",
            "tskgl_tskg_id",
            "tskgl_lang",
            "tskgl_name"
        );
    }
}

?>