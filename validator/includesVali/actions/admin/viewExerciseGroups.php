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
    $stmt = ExerciseGroup::getById($id);
    if(!$stmt->delete()) {
        print_r($db->getErrorText());
    }
    $deleted = true;
}

if(array_key_exists("submit", $_POST)) {
    $name_de = $_POST["name_de"];
    $name_en = $_POST["name_en"];
    $instructor = $_POST["instructor"];
    $id = intval($_POST["id"]);
    $exerciseGroup = $id > 0 ? ExerciseGroup::getById($id) : null;

    if(empty($exerciseGroup)) {
        $exerciseGroup = ExerciseGroup::create();
        $exerciseGroup->setSemId($_SESSION["sem_id"]);
    }

    $exerciseGroup->setInstructor($instructor);
    $exerciseGroup->save();

    foreach(array('de', 'en') as $lang) {
        $loc = @ExerciseGroupLocalization::getByCondition('egrpl_egrp_id = ? and egrpl_lang = ?', array($exerciseGroup->getId(), $lang));
        if (!$loc) {
            $loc = ExerciseGroupLocalization::create();
            $loc->setEgrpId($exerciseGroup->getId());
            $loc->setLang($lang);
        }
        else
        {
            $loc=$loc[0];
        }
        $loc->setName($_POST["name_$lang"]);
        $loc->save();
    }

    $edited = true;
}