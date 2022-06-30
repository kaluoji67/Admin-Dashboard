<?php
/**
 * Created by PhpStorm.
 * User: Sören
 * Date: 26.04.2015
 * Time: 13:33
 */

$edited = false;
$deleted = false;

if(array_key_exists("delete", $_GET)) {
    $id = $_GET["delete"];
    $stmt = TaskGroup::getById($id);
    $stmt->delete();
    $deleted = true;
}

if(array_key_exists("submitCopyTaskGroups", $_POST)) {
    $selectedTaskGroups = $_POST["selectedTaskGroups"];
    $semID = $_POST["semester"];
    
    $taskGroupIDs=explode(";",$selectedTaskGroups);
    for($i=0; $i<count($taskGroupIDs)-1;$i++)
    {
        $t = TaskGroup::getById($taskGroupIDs[$i]);
        $t->setSemId($semID);
        $t->setOrder(0);
        $t->save();
    }
}

if(array_key_exists("submit", $_POST)) {
    $name_de = $_POST["name_de"];
    $name_en = $_POST["name_en"];
    $visible = $_POST["visible"] == 'Y' ? 'Y' : 'N';
    $pos = intval($_POST["order"]);
    $id = intval($_POST["id"]);
    $taskGroup = $id > 0 ? TaskGroup::getById($id) : null;
    $copySem = $_POST["copySem"];
    
    if(empty($taskGroup)) {
        $taskGroup = TaskGroup::create();
        $taskGroup->setSemId($_SESSION["sem_id"]);
    }
    
    $semId=((isset($_POST["semester"]))? $_POST["semester"]:$taskGroup->getSemId());
    
    if((@$taskGroup->getOrder() != $pos)||($copySem=='true')) {
        $taskGroup->setOrder($pos);
        $taskGroups = TaskGroup::getByCondition('tskg_order >= ? and tskg_sem_id = ?', array($pos, $semId), array('tskg_order'));
        foreach($taskGroups as $taskGrpTmp) {
            $taskGrpTmp->setOrder(++$pos);
            $taskGrpTmp->save(true);
        }
    }
    $taskGroup->setVisible($visible);
    $taskGroup->save();

    
    if(($copySem=='false')||($taskGroup->getCopyofTskgId()==null))
    {
        foreach(array('de', 'en') as $lang) {
            $loc = @TaskGroupLocalization::getByCondition('tskgl_tskg_id = ? and tskgl_lang = ?', array($taskGroup->getId(), $lang));
            if (!$loc) {
                $loc = TaskGroupLocalization::create();
                $loc->setTskgId($taskGroup->getId());
                $loc->setLang($lang);
            }
            else
            {
                $loc = $loc[0];
            }
            $loc->setName($_POST["name_$lang"]);
            $loc->save();
        }
    }

    $edited = true;
}