<?php

/**
 * Class questionnaire
 * Class to handle everything around the questionnaires
 */
class trackingLog extends BaseClassEval
{
    public static function getTableName() {
        return "sqlvali_eval.trackinglogs";
    }

    public static function getPrefix() {
        return "tl";
    }

    public static function getColumns() {
        return array(
            "tl_id",
            "tl_semester",
            "tl_user",
            "tl_timestamp",
            "tl_actioncode",
            "tl_actionsupp"
        );
    }

    public static function getPKColumns()
    {
        return array ("tl_id");
    }

    public static function addLog($user, $action, $GET, $POST){
        $aC = 0; //actionCode to shorthand the performed user action
        if (isset($user) && $user->getFlagAdmin() != 'Y') {
            $userId = EvalModule::getRegisteredUser($user->getId());
            switch ($action) {
                case "viewTasks":
                    self::basicInsert(self::getTableName(), array_diff(self::getColumns(), self::getPKColumns()), array($_SESSION["sem_id"], $userId, null, 1, null));
                    break;
                case "viewTask":
                    //Task is loaded
                    //Further distinction is written within the viewTask action aka viewTask.php
                    break;
                case "eval/questionnaire":
                    $relatedQuest = (isset($GET["q"]) && $GET["q"] != null) ? $GET["q"] : -1;
                    if ($POST != null) {
                        //questionnaire is submitted
                        self::basicInsert(self::getTableName(), array_diff(self::getColumns(), self::getPKColumns()), array($_SESSION["sem_id"], $userId, null, 6, $relatedQuest));
                    } else {
                        //questionnaire is started/looked at
                        self::basicInsert(self::getTableName(), array_diff(self::getColumns(), self::getPKColumns()), array($_SESSION["sem_id"], $userId, null, 5, $relatedQuest));
                    }
                    break;
                case "eval/selfcheck":
                    //Further distinction within selfcheck action - selfchekc.php
                    break;
                case "account":
                    self::basicInsert(self::getTableName(), array_diff(self::getColumns(), self::getPKColumns()), array($_SESSION["sem_id"], $userId, null, 8, null));
                    break;
                default:
                    self::basicInsert(self::getTableName(), array_diff(self::getColumns(), self::getPKColumns()), array($_SESSION["sem_id"], $userId, null, 0, null));
                    break;
            }
        }
    }

}

?>