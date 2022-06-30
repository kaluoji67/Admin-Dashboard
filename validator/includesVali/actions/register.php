<?php

if(array_key_exists("submit", $_POST)) {
    $errors = array();
    $username = $_POST["username"];
    $fullname = $_POST["fullname"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];
    $semester = $_POST["semester"];
    $passphrase = $_POST["passphrase"];
    $email = $_POST["email"];
    $email2 = $_POST["email2"];
    $lang = $_POST["language"];
	$full_email = $email.$email2;
    
    if(User::getByName($username)) {
        $errors[] = $l->getString('register_error_usernameAU');//Username is already in use
        $username = "";
    }
	//Username as length
    if(strlen($username) < 4) {
        $errors[] = $l->getString('register_error_usernameLength');
        $username = "";
    }
	//Fullname at least two character
    if(strlen($fullname) < 2) {
        $errors[] = $l->getString('register_error_fullname');
        $fullname = "";
    }
	//Failsafe Mail - No Mail - Mail altered 
    if(empty($email) || strpos($email, '@')) {
        $errors[] = $l->getString('register_error_mail');
        $email = "";
    }//Mail already in Use
    elseif(User::getByCondition("usr_email=?",array($full_email))) {
        $errors[] = $l->getString('register_error_mail');
        $email = "";
    }
	//Password at least 4 characters
    if(strlen($password) < 4) {
        $errors[] = $l->getString('register_error_pwdLength');
        $password = "";
        $password2 = "";
    }
	//Password mismatch
    if($password != $password2) {
        $errors[] = $l->getString('register_error_pwdMismatch');
        $password = "";
        $password2 = "";
    }
	//Check whether the given passphrase matches the givven semester
    $sem = Semester::getByPassphrase($passphrase);
    if(is_null($sem) || empty($sem)) {
        $errors[] = $l->getString('register_error_passWrong');
        $passphrase = "";
    }
    else if(($sem)&&($sem->getId()!=$semester))
    {
        
        $errors[] = $l->getString('register_error_passWrong');
        $passphrase = "";
    }
    //Set selected language
    if (isset($lang) and ($lang == "en" or $lang == "de"))
    {
        $l->setLanguage($lang);
        $_SESSION["lang"] = $lang;
    }
    else
        $errors[] = "Wrong language submitted";

    //Set selectGroup to true to allow template loading the second stage
    if(empty($errors))
    {
        $selectGroup=true;
    }

    if((empty($errors))&&(isset($_POST["group"]))) {
        $group = $_POST["group"];
        
        $user = User::create();
        $user->setName($username);
        $user->setFullname($fullname);
        $user->setEmail($full_email);
        $user->setPassword(sha1($password));
        $user->setEgrpId($group);
        $user->setFlagAdmin('N');
        $user->setSemId($sem->getId());
        $user->setlang($lang);
        $user->save();
        
        $_SESSION["user"] = serialize($user);
        $_SESSION["sem_id"] = $user->getSemId();
        
        $page = 'register_successful';
    }

}

?>