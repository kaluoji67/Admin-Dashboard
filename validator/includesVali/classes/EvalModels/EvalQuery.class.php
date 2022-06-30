<?php

/**
 * Class questionnaire
 * Class to handle everything around the questionnaires
 */
class EvalQuery extends BaseClassEval
{
    public static function getTableName() {
        return "sqlvali_eval.evalquery";
    }

    public static function getPrefix() {
        return "eq";
    }

    public static function getColumns() {
        return array(
            "eq_ID",
            "eq_UserID",
            "eq_user_query",
            "eq_errors",
            "eq_taskid",
            "eq_ts"
        );
    }

    public static function getPKColumns()
    {
        return array ("eq_ID");
    }

    public static function insertQuery($userID,$userQuery,$errors,$taskid){
        //stringify optional multiple errors or 0 if there is no error listed
        $errorsInsert = (count($errors) == 0) ? "0" : implode(";",$errors);
        return self::basicInsert(self::getTableName(),array_slice(self::getColumns(),1,4),array($userID,$userQuery,$errorsInsert,$taskid));
    }

}

?>