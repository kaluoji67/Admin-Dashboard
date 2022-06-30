<?php

require_once __DIR__ . '/ModelObject.class.php';

class UserQuery extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.user_query";
    }

    public static function getPrefix() {
        return "usq";
    }

    public static function getColumns() {
        return array(
            "usq_id",
            "usq_tsk_id",
            "usq_usr_id",
            "usq_sql",
            "usq_timestamp",
            "usq_success"
        );
    }

    /** provides all userqueries saved from a specific user
     * @param $userID
     * @return array|bool
     */
    public static function getUserQueries($userID){
        $queries = array();
        //Gather all of the ids from the queries the user produced
        global $db;
        //Formulate SQL in that way that only the last query is retrieved
        //SELECT usq_id FROM user_query as q1 WHERE q1.`usq_timestamp` IN
        // (SELECT Max(`usq_timestamp`) FROM user_query as q2 WHERE q2.`usq_usr_id` = ? GROUP BY q2.`usq_tsk_id` )
        $sql = "SELECT usq_id FROM ".self::getTableName()." as q1 WHERE q1.usq_timestamp IN( 
        SELECT Max(usq_timestamp) FROM ".self::getTableName()." as q2 WHERE q2.usq_usr_id = ? GROUP BY q2.usq_tsk_id);";
        $db->queryWithParams($sql,array($userID));
        $ids = $db->fetchAll();
        foreach ($ids as $id){
            $queries[] = static::getByCondition("usq_id = ?",$id)[0];//result is always an array even it has only one entry
        }
        return $queries;
    }
}

?>