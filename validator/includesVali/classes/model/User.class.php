<?php

require_once __DIR__ . '/ModelObject.class.php';

class User extends ModelObject {

    private const CONSTRAINT_HIDE_TUTORIAL = 25;
    public static function getTableName() {
        return "sqlvali_data.user";
    }

    public static function getPrefix() {
        return "usr";
    }

    public static function getColumns() {
        return array(
            "usr_id",
            "usr_name",
            "usr_fullname",
            "usr_email",
            "usr_password",
            "usr_egrp_id",
            "usr_flag_admin",
            "usr_sem_id",
			"usr_lang",
        	"usr_forgot_id"
        );
    }

    public static function getByName($name) {
        $users = self::getByCondition("lower(usr_name) = lower(?)", array($name));
        if(!empty($users)) {
            return $users[0];
        }
        return FALSE;
    }

    /**
     * Provides the transition from the User ID from the sqlvali system to the
     * UserEvalID from the eval system
     * @return mixed Database ID of the current user
     */
    public function getUserEvalID()
    {
        return EvalModule::getRegisteredUser($this->getId());
        //return $this->getId();
    }

    /* Check if the user has enough Tutorials and there not showing the Tutorial Nav View */
    public function hasEnoughTutorials(): bool
    {
        global $db;
        $sql = "select count(usq_usr_id) from user_query where usq_usr_id = ?";
        if(!$db->queryWithParams($sql, [$this->getId()])) {
            return false;
        }
        $result = $db->fetch();
        $value = reset($result);
        return $value >= self::CONSTRAINT_HIDE_TUTORIAL;
    }

}



?>