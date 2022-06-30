<?php
if(array_key_exists("delete", $_POST)) {
    $errors = array();
    $id=$_POST["semid"];
    //check, if semester is used already, if yes, its not allowed to delete this semester
    $delete=true;
    //1) attachment
    /*if(Attachment::getByCondition("att_sem_id=?",array($id)))
    {
        $delete=false;
        $errors[]="Couldn't delete Semester. Attachment connected.";
    }*/
    //2) exercisegroup
    if(ExerciseGroup::getByCondition("egrp_sem_id=?",array($id)))
    {
        $delete=false;
        $errors[]="Couldn't delete Semester: Connected Exercise Group";
    }
    //3) task
    if(Task::getByCondition("tsk_sem_id=?",array($id)))
    {
        $delete=false;
        $errors[]="Couldn't delete Semester: Connected Task";
    }
    //4) taskgroup
    if(Taskgroup::getByCondition("tskg_sem_id=?",array($id)))
    {
        $delete=false;
        $errors[]="Couldn't delete Semester: Connected Task Group";
    }
    //5) user
    if(User::getByCondition("usr_sem_id=?",array($id)))
    {
        $delete=false;
        $errors[]="Couldn't delete Semester: Connected User";
    }
    
    if(empty($errors))
    {
        $sem=Semester::getById($id);
        $sem->delete();
        $deleted=true;
        $page = 'home';
    }
}


if(array_key_exists("submit", $_POST)) {
    $errors = array();
    $semester = $_POST["semester"];
    $passphrase = $_POST["passphrase"];
    
    if(isset($_POST["semid"]))
    {
        $id=$_POST["semid"];
        $sem=Semester::getById($id);
        $edit=true;
    }
    else 
    {
        $edit=false;
    }

    if(Semester::getByDescr($semester)) {
        if($edit)
        {
            $sem_temp = Semester::getByDescr($semester);
            if($id!=$sem_temp->getId())
            {
                $errors[] = "Semester already exists.";
                $semester = "";
            }
        }
        else
        {
            $errors[] = "Semester already exists.";
            $semester = "";
        }
    }

    if(Semester::getByPassphrase($passphrase)) {
        if($edit)
        {
            $sem_temp = Semester::getByPassphrase($passphrase);
            if($id!=$sem_temp->getId())
            {
                $errors[] = "Passphrase is already in use.";
                $passphrase = "";
            }
        }
        else
        {
            $errors[] = "Passphrase is already in use.";
            $passphrase = "";
        }
    }
    
    if(empty($errors)) {
        if(!$edit)
        {
            $sem = Semester::create();
        }
        $sem->setDescr($semester);
        $sem->setPassphrase($passphrase);
        $sem->save();
        
        $page = 'home';
    }

}

?>