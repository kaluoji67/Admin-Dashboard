<?php

/**
 * Class EvalModule
 * Class to handle every function regarding the evaluation of user interactions and the preparation for the research data
 */
class EvalModule
{
    //error Dictionary to save all possible occurring errors and useful information for them
    //The array index is used as identifier and later stored in the database
    //*Note* It is not often used and shouldn't be changed outside of the code therefore we can define it inside the class
    private $errorDictionary = array(
        array("name" => "No error","description" => "0 is preserved as no error, because the later evaluation may set 0 as standard"),//0
        array("name" => "Syntax_error","description" => "Error resulting when the query has a syntax error and could not be performed by the dbms"),//1
        array("name" => "ColumnCountS","description" => "The user selected a wrong amount of columns from the database with a SELECT Statement"),//2
        array("name" => "ColumnOrder","description" => "Columns are in a wrong oder. Is no longer used."),//3
        array("name" => "ColumnNameError","description" => "The Name of a column is wrong/missing. For example when renaming in a select class"),//4
        array("name" => "ColumnCountCT","description" => "The user created a wrong amount of columns in a CT statement."),//5
        array("name" => "RowCount","description" => "The user selected a wrong amount of rows from the original table"),//6
        array("name" => "ConstraintCount","description" => "The user provided more constraints than needed"),//7
        array("name" => "PrimaryKey","description" => "The primary key is either not set or missing"),//8
        array("name" => "Unique","description" => "There should be a Unique constraint formulated and it is wrong or missing"),//9
        array("name" => "ForeignKey","description" => "A foreign Key Constraint is incorrectly formed"),//10
        array("name" => "GeneralKeys","description" => "A Foreign or primary key is incorrectly formed"),//11
        array("name" => "SDataTypes","description" => "A datatype mismatches the reference data type"),//12
        array("name" => "SIsNULL","description" => "A column is either NULL where it shouldn't be NULL or it is not NULL where it should be"),//13
        array("name" => "SIsDEFAULT","description" => "A DEFAULT value is incorrectly set"),//14
        array("name" => "SColumnName","description" => "A column Name is misspelled."),//15
        array("name" => "STableName","description" => "The TableName is misspelled and the created table couldn't be loaded."),//16
        array("name" => "FKName","description" => "The Column name of the foreign key constraint is wrong"),//17
        array("name" => "FKRefTable","description" => "The referenced table is wrong."),//18
        array("name" => "FKRefColumn","description" => "The the referenced column in the referenced table is wrong"),//19
        array("name" => "TableContent","description" => "The table content doesn't match the desired table. For example in an insert into statement"),//20
        array("name" => "TableRowOrder","description" => "The rows are in a wrong order. Changed by Order By Statement"),//21

    );//array("name" => " ","description" => ""),

    /**
     * EvalModule constructor.
     */
    function __construct(){
    }

    function getErrorDictionary(){
        return $this->errorDictionary;
    }

    public static function getRegisteredUser($normalUserID){
        global $dbEval;

        $sql = "SELECT UserID FROM Evaluser WHERE UserHash = SHA2(?,256)";
        if (!$dbEval->queryWithParams($sql,array($normalUserID)))
            throw new Exception("Critical: Couldn't progress UserID Query");
        $result = $dbEval->fetch();
        if ($result == null OR count($result) == 0)
        {
            if (!$dbEval->queryWithParams('INSERT INTO EvalUser(UserHash) VALUES(SHA2(?,256))',array($normalUserID))) {
                print_r($dbEval->getErrorText());
                return FALSE;
            }
        }
        if(!$dbEval->queryWithParams($sql,array($normalUserID))) {
            print_r($dbEval->getErrorText());
            return false;
        }
        $result = $dbEval->fetch();
        return $result[0];
    }

    /** gets all tasks of the semester, their group and the amount of queries produced with them
     * @param $semester
     * @param $lang
     * @return array
     */
    public static function getTaskStatistics($semester,$lang){
        global $dbEval; //Connection to eval Database for custom query
        $tskGStatsContainer = array();//Return container

        //First get all taskgroups and their corresponding Name
        $groupPointer = 0;
        foreach (TaskGroup::getByCondition("tskg_sem_id = ?",array($semester),array("tskg_order")) as $tskg){
            $groupCount = 0;
            $tskGStatsContainer[] = ["id" => $tskg->getId(),"name" => $tskg->getName($lang),"tasks"=> [],"count" => 0];
            //Select now all corresponding tasks with their id and title
            foreach(Task::getByCondition("tsk_tskg_id = ?", array($tskg->getId()), array('tsk_order')) as $task){
                $sql = "SELECT COUNT(*) as count FROM evalquery WHERE eq_taskid = ? ;";
                if (!$dbEval->queryWithParams($sql,array($task->getId())))
                    echo "error parsing SQL <br>".$dbEval->getErrorText();
                else {
                    $countTask = $dbEval->fetch()[0];
                    $groupCount += $countTask;
                    $tskGStatsContainer[$groupPointer]["tasks"][] = ["id" => $task->getId(),"name"=> $task->getTitle($lang),"count"=>$countTask];
                }
            }
            $tskGStatsContainer[$groupPointer]["count"] = $groupCount;
            $groupPointer += 1;
        }

        return $tskGStatsContainer;
    }

    public static function getQuestionnaires(){
        global $dbEval; //Connection to eval Database for custom query
        $quests = Questionnaire::getAvailableQuestionnaires();
        $questPointer= 0;
        foreach ($quests as $quest){
            $sql = "SELECT COUNT(DISTINCT UserID) FROM ".Questionnaire::getAnswersTableName()." WHERE Q_ID = ? AND Q_language = ?";
            if (!$dbEval->queryWithParams($sql,array($quest["Q_ID"],$quest["Q_language"])))
                echo "error parsing SQL <br>".$dbEval->getErrorText();
            else {
                $participants = $dbEval->fetch()[0];
                $quests[$questPointer]["participants"] = $participants;
            }
            $questPointer+=1;
        }
        return $quests;
    }

}

?>