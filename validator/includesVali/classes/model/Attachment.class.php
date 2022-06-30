<?php

require_once __DIR__ . '/ModelObject.class.php';

class Attachment extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.attachment";
    }

    public static function getPrefix() {
        return "att";
    }

    public static function getColumns()
    {
        return array(
            "att_id",
            "att_content_type",
            "att_content",
            "att_sem_id"
        );
    }
}

?>