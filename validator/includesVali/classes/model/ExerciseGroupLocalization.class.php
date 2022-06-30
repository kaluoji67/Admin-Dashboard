<?php

require_once __DIR__ . '/ModelObject.class.php';

class ExerciseGroupLocalization extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.exercise_group_localization";
    }

    public static function getPrefix() {
        return "egrpl";
    }

    public static function getColumns()
    {
        return array(
            "egrpl_id",
            "egrpl_egrp_id",
            "egrpl_lang",
            "egrpl_name"
        );
    }
}

?>