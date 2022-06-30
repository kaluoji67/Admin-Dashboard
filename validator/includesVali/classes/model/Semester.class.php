<?php

require_once __DIR__ . '/ModelObject.class.php';

class Semester extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.semester";
    }

    public static function getPrefix() {
        return "sem";
    }

    public static function getColumns() {
        return array(
            "sem_id",
            "sem_descr",
            "sem_passphrase"
        );
    }

    public static function getByPassphrase($passphrase) {
        $sem = self::getByCondition("sem_passphrase = ?", array($passphrase));
        if(!empty($sem)) {
            return $sem[0];
        }
        return FALSE;
    }
    
    public static function getByDescr($descr) {
        $sem = self::getByCondition("lower(sem_descr) = lower(?)", array($descr));
        if(!empty($sem)) {
            return $sem[0];
        }
        return FALSE;
    }
}

?>