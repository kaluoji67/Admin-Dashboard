<?php

require_once __DIR__ . '/ModelObject.class.php';

class TaskGroup extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task_group";
    }

    public static function getPrefix() {
        return "tskg";
    }

    public static function getColumns()
    {
        return array(
            "tskg_id",
            "tskg_visible",
            "tskg_order",
            "tskg_sem_id",
            "tskg_copyof_tskg_id"
        );
    }

    public function getName($lang) {
        if($this->getCopyofTskgId()==null)
        {
            $l = TaskGroupLocalization::getByCondition('tskgl_tskg_id = ? and tskgl_lang = ?', array($this->getId(), $lang));
        }
        else
        {
            $l = TaskGroupLocalization::getByCondition('tskgl_tskg_id = ? and tskgl_lang = ?', array($this->getCopyofTskgId(), $lang));
        }
        $l = $l[0];
        return $l->getName();
    }
}

?>