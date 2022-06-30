<?php

/**
 * Class questionnaire
 * Class to handle everything around the questionnaires
 */
class questionnaire extends BaseClassEval
{
    public static function getTableName()
    {
        return "sqlvali_eval.questionnaire";
    }

    public static function getTaskTableName()
    {
        return "sqlvali_eval.qtask";
    }

    public static function getTaskItemTableName()
    {
        return "sqlvali_eval.taskitem";
    }

    public static function getAnswersTableName()
    {
        return "sqlvali_eval.qanswers";
    }

    public static function getPrefix()
    {
        return "Q";
    }

    public static function getColumns()
    {
        return array(
            "Q_ID",
            "Q_language",
            "Q_title",
            "Q_description",
            "Q_validity_check",
            "Q_active",
            "Q_type",
            "Q_proceededby"
        );
    }

    public static function getTaskColumns()
    {
        return array(
            //"_Qid",
            //"Q_language",
            "TaskNum",
            "Tdescription",
            "type",
            "scalesize",
            "extrema"
        );
    }

    public static function getTaskItemColumns()
    {
        return array(
            // "Q_ID",
            // "Q_language",
            "TaskNum",
            "INum",
            "Idescription",
            "possibleChoices",
            "correctChoices",
            "inputType",
            "inputLength"
        );
    }

    public static function getAnswersColums()
    {
        return array(
            "UserID",
            "Q_ID",
            "Q_language",
            "TaskNum",
            "INum",
            "result",
            "ts",
            "trial"
        );
    }

    public static function getQuestSemestColums()
    {
        return array(
            "Q_ID",
            "Q_language",
            "sem_id",
            "tskg_id"
        );
    }

    public static function getPKColumns()
    {
        return array("Q_ID", "Q_language");
    }
    public static function getTaskPKColumns()
    {
        return array("Q_ID", "Q_language","TaskNum");
    }
    public static function getTaskItemPKColumns()
    {
        return array("Q_ID", "Q_language","TaskNum","INum");
    }
    public static function getAnswersPKColumns()
    {
        return array("UserID", "Q_ID", "Q_language","TaskNum","INum","trial");
    }

    public static function getByIDaLanguage($ID, $lang)
    {
        $quest = self::getByCondition("Q_ID = ? AND Q_language = ?", array($ID, $lang));
        if (!empty($quest)) {
            return $quest[0];
        }
        return FALSE;
    }

    public static function getByTitle($title)
    {
        $quest = self::getByCondition("Q_title = ?", array($title));
        if (!empty($quest)) {
            return $quest[0];
        }
        return FALSE;
    }

    /** Returns all the tasks and related items in an multidimensional array belonging to this semester
     * @return array|bool
     */
    public function getAllTasksWithItems()
    {
        $pks = $this->getPKs();
        $where = join("= ? AND ", self::getPKColumns());
        $where .= " = ?";
        $tasks = self::getGeneralConditionedJoined(array(self::getTableName(), "QTask"), array_merge(self::getTaskColumns(), self::getColumns()), $where, $pks);
        if (!empty($tasks))
            foreach ($tasks as &$task) {
                $task["item"] = $this->getTaskItems($task["TaskNum"]);
            }
        return $tasks;
    }

    public function getSelfcheckTasksWithItems()
    {
        $pks = $this->getPKs();
        $where = join("= ? AND ", self::getPKColumns());
        $where .= " = ?";
        $tasks = self::getGeneralConditionedJoined(array(self::getTableName(), "QTask"), array_merge(self::getTaskColumns(), self::getColumns()), $where, $pks);
        if (!empty($tasks))
            foreach ($tasks as &$task) {
                //tasks are interpreted in a selfcheck as containers for the actual task items.
                //They provide the context in such that the description describes which tier should be chose and
                // the scalesize how many items should be picked
                //Only accept MC and SQL tasks for this
                $items = array();
                if ($task["type"] == 4) {
                    $items = $this->getTaskItems($task["TaskNum"]);
                }else if ($task["type"] == 5){
                    $tskgID = $task["extrema"];//Id of the taskgroup is stored inside the extrema value
                    $items = Task::getByCondition("tsk_tskg_id=?",array($tskgID));
                }
                if ($items != null) {
                    $usedIndice = array();
                    $task["item"] = array();
                    //randomly draw from the itempool the given number(scalesize)
                    $numberDrawn = $task["scalesize"] != null ? $task["scalesize"] : 0;
                    for ($i = 0; $i < $numberDrawn && $i < count($items); $i++) {
                        $index = rand(0, count($items) - 1);
                        while (in_array($index, $usedIndice))
                            $index = rand(0, count($items) - 1);
                        //append the chose to the task and declare it as used
                        $usedIndice[] = $index;
                        $task["item"][] = $items[$index];
                    }
                }else
                    $task["item"] = array();
            }
        return $tasks;
    }

    /** Returns alle items in an array belonging to the questionnaire and the related Task
     * @param $taskID TaskID
     * @return array|bool|null
     */
    public function getTaskItems($taskID)
    {
        $pks = $this->getPKs();
        $where = join("= ? AND ", self::getPKColumns());
        $where .= " = ? and TaskNum = ?";
        $items = self::getGeneralConditionedJoined(array("TaskItem"), self::getTaskItemColumns(), $where, array_merge($pks, array($taskID)));
        if (!empty($items))
            return $items;
        else
            return null;
    }

    /** Checks whether the user completed a questionnaire independent from its language only by ID
     * @param $UserID EvalUserID from the sqlvali_eval database
     * @return bool
     */
    public function checkUserParticipation($UserID)
    {
        $where = "Q_ID = ? and UserID = ?";
        $pks = $this->getPKs();

        $awnsers = self::getGeneralConditionedJoined(array(Questionnaire::getAnswersTableName()), self::getAnswersColums(), $where, array($pks[0], $UserID));

        if (count($awnsers) > 0)
            return true;
        else
            return false;
    }

    /** Checks if there is an uncompleted questionnaire for the user in the current semester
     * @param $semester Semester to be checked
     * @param $UserID User who is online
     * @return object questionnaire object that
     */
    public static function checkForquestionnaires($semester, $userID, $userLang)
    {
        $returners = array();//all possible questionnaires
        $quests = self::getGeneralConditionedJoined(array("questionnaire", "questsemest"), array_merge(self::getColumns(), array("sem_id")), 'sem_id = ? AND Q_active=1', array($semester));
        if ($quests != null and count($quests) > 0) {
            $oldID = -1;
            foreach ($quests as $quest) {
                if ($quest["Q_ID"] != $oldID) {
                    $oldID = $quest["Q_ID"];
                    $questionnaire = self::getByPK(array($quest["Q_ID"], $quest["Q_language"]));
                    if (!$questionnaire->checkUserParticipation($userID)) {
                        $pre = $questionnaire->CheckForPredecessor();
                        if ($pre != null){
                            if ($pre->checkUserParticipation($userID)){
                                if (self::getByPK(array($quest["Q_ID"], $userLang)))
                                    $questionnaire =self::getByPK(array($quest["Q_ID"], $userLang));
                                $returners[] = $questionnaire;
                            }
                        }else {
                            if(self::getByPK(array($quest["Q_ID"], $userLang)))
                                $questionnaire = self::getByPK(array($quest["Q_ID"], $userLang));
                            $returners[] = $questionnaire;
                        }
                    }
                }
            }
        }
        return $returners;
    }

    public static function getavailableSelfchecks($semester){
        $returners = array();//all selfchecks
        $quests = self::getGeneralConditionedJoined(array("questionnaire", "questsemest"), array_merge(self::getColumns(), array("sem_id")), 'sem_id = ?', array($semester));
        if ($quests != null and count($quests) > 0) {
            $oldID = -1;
            foreach ($quests as $quest) {
                if ($quest["Q_ID"] != $oldID AND strpos(strtolower($quest["Q_type"]), 'selfcheck') !== false) {
                    $oldID = $quest["Q_ID"];
                    $questionnaire = self::getByPK(array($quest["Q_ID"], $quest["Q_language"]));
                    $returners[] = $questionnaire;
                }
            }
        }
        return $returners;
    }

    public static function getAvailableQuestionnaires()
    {
        $quests = self::getGeneralConditionedJoined(array("questionnaire"), self::getColumns());
        if ($quests != null and count($quests) > 0)
            return $quests;
        return NULL;
    }

    public function isVoluntarily($semester){
        $quests = self::getGeneralConditionedJoined(array("questionnaire", "questsemest"), array_merge(self::getColumns(), array("tskg_id")), 'Q_ID = ? AND sem_id = ?', array($this->getPKs()[0],$semester));
        $tskGIds = array();
        if ($quests[0]["tskg_id"] == "-1")
            return $tskGIds;
        foreach ($quests as $qu)
            $tskGIds[] = $qu["tskg_id"];
        return $tskGIds;
    }

    public function CheckForPredecessor()
    {
        $pre = self::getByCondition("Q_proceededby = ?",array($this->getPKs()[0]));
        if (count($pre) > 0)
            return $pre[0];
        else
            return NULL;
    }

    public function evaluateSelfcheck($userId){
        $trial = $this->latestTrial($userId);
        $tasks = self::getGeneralConditionedJoined(array(Questionnaire::getAnswersTableName(),Questionnaire::getTaskTableName(),Questionnaire::getTaskItemTableName()),
            array_unique(array_merge(self::getAnswersColums(),self::getTaskColumns(),self::getTaskItemColumns())),
            "Q_ID = ? AND UserID = ? AND trial = ?",array($this->getPKs()[0],$userId,$trial),array("TaskNum","INum"));
        //Evaulate tasks
        $points = 0;
        $totalPoints = 0;
        foreach($tasks as $task){
            //MC Question
            if ($task["type"] == 4) {
                $resultAnswers = explode(';', $task["result"]);
                $correctAnswers = explode(';', $task["correctChoices"]);
                $allAwnsers = explode(';', $task["possibleChoices"]);
                if (count($resultAnswers)-1 != 0 AND floor(count($resultAnswers)/2) != count($allAwnsers)) {
                    foreach ($allAwnsers as $possAnswer) {
                        if (in_array(trim($possAnswer), $correctAnswers)) {
                            //Answer is correct and also ticked correctly
                            if (in_array($possAnswer, $resultAnswers))
                                $points += 1;
                        } else {
                            //Answer is not correct and also not ticked as correct
                            if (!in_array(trim($possAnswer), $resultAnswers))
                                $points += 1;
                        }
                    }
                }

                $totalPoints += count(explode(';', $task["possibleChoices"]));
            }else //SQL Question
                if ($task["type"] == 5){
                    $partsTask = explode('||',$task["result"]);
                    for($i = 0; $i <count($partsTask)-1;$i++) {//Iterate all saved tasks. last one is skipped, because it is empty
                        $parts = explode('|', $partsTask[$i]);
                        if (count($parts) >= 4 && $parts[3] == "(Correct)")
                            $points += 5;
                        $totalPoints += 5;
                    }
            }
        }
        return array($totalPoints,$points,$tasks);
    }

    /** Returns the last trial the user performed on the given questionnaire;Returns -1 if no trial exists
     * @param $UserId
     * @return int
     */
    public function latestTrial($UserId){
        $trial = -1;//-1 if no trial exists
        $where = "Q_ID = ? and UserID = ?";
        $pks = $this->getPKs();
        $answers = self::getGeneralConditionedJoined(array(Questionnaire::getAnswersTableName()), self::getAnswersColums(), $where, array($pks[0], $UserId),array("trial"));
        if (count($answers) > 0)
            $trial = $answers[count($answers)-1]["trial"];
        return $trial;
    }

    /** Returns the possible types of questionnaires we distinguish
     * Note: These are exactly as defined here, saved in the database. A change in this position has also to consider a change in the database
     * @return array containing strings
     */
    public static function getAvailableTypes(){
        return array("Selfcheck","Questionnaire");
    }

    /** Returns the possible types of tasks. A further description is given in the wiki under questionnaire
     * @return string[]
     */
    public static function getAvailableTaskTypes(){
        return array(1 => "Freetext",2 => "Choices",3 => "Likerscale",4 => "MultipleChoice",5 => "SQLTask");
    }

}

?>