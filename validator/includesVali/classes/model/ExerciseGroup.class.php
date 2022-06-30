<?php

require_once __DIR__ . '/ModelObject.class.php';

class ExerciseGroup extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.exercise_group";
    }

    public static function getPrefix() {
        return "egrp";
    }

    public static function getColumns()
    {
        return array(
            "egrp_id",
            "egrp_name",
            "egrp_instructor",
            "egrp_sem_id"
        );
    }

    public function getName($lang) {
        $l = @ExerciseGroupLocalization::getByCondition('egrpl_egrp_id = ? and egrpl_lang = ?', array($this->getId(), $lang));
        $l = @$l[0];
        return @$l->getName();
    }
}

?>