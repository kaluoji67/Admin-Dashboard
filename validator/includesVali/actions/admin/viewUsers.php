<?php

$edited = false;
$deleted = false;

if(array_key_exists("delete", $_GET)) {
    $id = $_GET["delete"];
    $stmt = User::getById($id);
    $stmt->delete();
    $deleted = true;
}

if(array_key_exists("submit", $_POST)) {
    $action = $_POST["action"];
    if($action == "edit_user") {
        $username = $_POST["username"];
        $fullname = $_POST["fullname"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $email2 = (isset($_POST["email2"])?$_POST["email2"]:'');
        if(!empty($email2))
        {
            $full_email = $email.$email2;
        }
        $group = intval(@$_POST["group"]);
        $group = $group == 0 ? null : $group;
        //15.05.17: get the value of admin
        $admin = $_POST["admin"] == 'Y' ? 'Y' : 'N';
        $user = User::getByName($username);
        
        if($user) {
            if(empty($email2)) {
                $errors[] = "The e-mail address is not valid.";
                $email = "";
            }
            else if(User::getByCondition("usr_email=? and usr_id!=?",array($full_email,$user->getId()))) {
                $errors[] = "Email is already in use.";
                $username = "";
            }
            else
            {
                if(!empty($password)) {
                    $user->setPassword(sha1($password));
                }
                $user->setFullname($fullname);
                $user->setEmail($full_email);
                $user->setEgrpId($group);
                //15.05.17: set the value of admin in database
                $user->setFlagAdmin($admin);
                $user->save();
                $success = true;
                //15.05.17: get the id of the user which is to be edited
                //get the values of the session user via unserialize
                //get the id of the session user
                $edituserid = $user->getId();
                $sessionuser = unserialize($_SESSION["user"]);
                $sessionuserid = $sessionuser->getId();
                //prove whether the id of the edited user and the session user is equal
                //--> if both are equal, the session user is created
                if($edituserid==$sessionuserid) {
                	$_SESSION["user"] = serialize($user);
                }
            }
        }
        
        //12.05.17: unserialize the session user -> get the admin view when a user was edited
        $user = unserialize($_SESSION["user"]);
    } else if($action == "create_user") {
        $username = $_POST["username"];
        $fullname = $_POST["fullname"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $email2 = $_POST["email2"];
        $full_email = $email.$email2;
        $group = @$_POST["group"];
        $admin = $_POST["admin"] == 'Y' ? 'Y' : 'N';

        if(User::getByName($username)) {
            $errors[] = "Username is already in use.";
            $username = "";
        }

        if(strlen($username) < 4) {
            $errors[] = "The username has to be at least 4 characters long.";
            $username = "";
        }

        if(strlen($fullname) < 2) {
            $errors[] = "Please enter your fullname.";
            $fullname = "";
        }

        if(empty($email) || strpos($email, '@')) {
            $errors[] = "The e-mail address is not valid.";
            $email = "";
        }
        else if(User::getByCondition("usr_email=?",array($full_email))) {
            $errors[] = "Email is already in use.";
            $username = "";
        }

        if(strlen($password) < 4) {
            $errors[] = "The password has to be at least 4 characters long.";
            $password = "";
        }




        if(empty($errors)) {
            $user = User::create();
            $user->setName($username);
            $user->setFullname($fullname);
            $user->setEmail($full_email);
            $user->setPassword(sha1($password));
            $user->setEgrpId($group);
            $user->setFlagAdmin($admin);
            $user->save();

            $success = true;
            //23.05.17: unserialize the session user -> get the admin view when a user was created
            $user = unserialize($_SESSION["user"]);
        }
    }

    else if(array_key_exists("viewSubmissionsUser", $_POST)) {
        echo "Hallo Welt 12124124";


    }
}