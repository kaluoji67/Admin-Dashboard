<?php
$rootDirectory = "./../../../../../includesVali";
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/db/connection.tpl.php';
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/helpers/fetch_functions.tpl.php';


$questSemester= $_POST['semester'];
$questLang =$_POST['lang'];

//if change semester
if(!isset($_POST['qId'])){
    $available_quest = getQuest($questSemester,$questLang);
    $html ="";
    foreach($available_quest as $key=>$quest){
          $html.='<optgroup label="'.$quest["title"] .'">';
               foreach($quest["taskNum"] as $tkey=>$taskNum){
                   $html.='<option  value="'. $key.",".$tkey.'" >';
                   $html.= $tkey.".  ".$taskNum["title"];
                   $html.='</option>';
               }
          $html.='</optgroup>';
     }

    echo json_encode($html);
}
//update chart
else{
    $answers = getQuestAnswers($questSemester,$questLang,$_POST["qId"],$_POST["taskNum"]);
    echo json_encode($answers);
}
/*
 foreach($available_quest as $key=>$quest){
          $html.='<optgroup label="'.$quest["title"] .'">';
               foreach($quest["taskNum"] as $tkey=>$taskNum){
                    if(count($taskNum["items"]) <= 1){
                          $html.='<option  value="'. $key.",".$tkey.","."1" .'" >';
                              $html.= $tkey.".  ".$taskNum["title"];
                          $html.='</option>';
                    }
                    else{
                          foreach($taskNum["items"] as $ikey=>$item){
                              $html.='<option  value="'. $key.",".$tkey.",".$ikey .'" >';
                              $html.= $tkey.".".$ikey.".  ".$item;
                              $html.='</option>';
                          }
                    }
               }
          $html.='</optgroup>';
     }
 */