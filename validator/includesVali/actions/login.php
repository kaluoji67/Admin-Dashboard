<?php

require_once __DIR__ . '/../classes/model/ExerciseGroup.class.php';
require_once __DIR__ . '/../classes/model/User.class.php';

$loginSuccessful = false;
$loginFailed = false;

if(array_key_exists("submit", $_POST)) {
    $username = @$_POST["username"];
    $password = @$_POST["password"];
    $success = true;

    $user = User::getByName($username);
    if($user && $user->getPassword() == sha1($password)) {
        if(!is_null($user->getForgotId()))
        {
            $user->setForgotId(null);
            $user->save();
        }
        $_SESSION["user"] = serialize($user);
        $_SESSION["sem_id"] = $user->getSemId();
        $loginSuccessful = true;
        //26.05.17: get  the language which the user prefer (show the website with this language)
        $l->setLanguage($user -> getLang());
        $lang = $user -> getLang();
    } else {
        unset($user);
        $loginFailed = true;
    }
}
