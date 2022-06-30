<?php
if(array_key_exists("submit", $_POST)) {
    $errors = array();
    
    if(isset($_POST["semid"]))
    {
        $id=$_POST["semid"];
        $sem=Semester::getById($id);
        if ($sem != null)
        {
            $user->setSemId($id);
            $user->setEgrpId(null);
            $user->save();
            $_SESSION["user"] = serialize($user);
            $_SESSION["sem_id"] = $id;
        }else
            $errors[] = "Semester couldn't be found";

    }
    else
        $errors[] = "Semester was not delivered";
}

?>