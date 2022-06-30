<?php

//created on 22.05.2017

//this file is for editAccount

$edited = false;
$deleted = false;

if(array_key_exists("delete", $_POST)) {
    $passwort_current="";
    if(!empty($_POST["password_current"])){
        $password_current = $_POST["password_current"];
    }
    
    if(empty($password_current)){
        $errors[] = "You have to enter your current password for changing your account data";
    }
    else if($user->getPassword()!=sha1($password_current)){
        $errors[] = "The entered password is not correct";
        $password_current = "";
    }
    
    $stmt = $user;
    if(empty($errors)){
        
        $stmt->delete();
        //after the account is deleted, the user is logged out
        unset($_SESSION["user"]);
        unset($user);
        $deleted = true;
        $page="home";
    }

}

if(array_key_exists("submit", $_POST)) {
	//after pushing the "Save-Button", get the action
	$action = $_POST["action"];
	//if the action ist edit account, ...
	if($action == "edit_account") {
		//get all the information which are in the form and also the username
		$username = $_POST["username"];
		$fullname = $_POST["fullname"];
		$password_current = $_POST["password_current"];
		$password_new = $_POST["password_new"];
		$email = $_POST["email"];
		$email2 = (isset($_POST["email2"])?$_POST["email2"]:'');
		if(!empty($email2))
		{
		    $full_email = $email.$email2;
		}
		$group = intval(@$_POST["group"]);
		$group = $group == 0 ? null : $group;
		$user = User::getByName($username);
		
		if(empty($password_current)){
			$errors[] = "You have to enter your current password for changing your account data";
		}
		elseif($user->getPassword()!=sha1($password_current)){
			$errors[] = "The entered password is not correct";
			$password_current = "";
		}
		
		
		if(empty($errors)){
			if(strlen($fullname) < 2) {
				$errors[] = "Please enter your fullname.";
				$fullname = "";
			}
			
			if(empty($email) || strpos($email, '@')) {
				$errors[] = "The e-mail address is not valid.";
				$email = "";
			}
			else if(empty($email2)) {
                $errors[] = "The e-mail address is not valid.";
                $email = "";
            }
            elseif(User::getByCondition("usr_email=? and usr_id!=?",array($full_email,$user->getId()))) {
                $errors[] = "Email is already in use.";
                $email = "";
            }
            
			if(!empty($password_new)){
				if(strlen($password_new) < 4) {
					$errors[] = "The password has to be at least 4 characters long.";
					$password = "";
				}
			}
			
		}
		
		if($user->getPassword()==sha1($password_current)&&empty($errors)){
			//if the edited password isn't empty, set the new password 
			if(!empty($password_new)) {
				$user->setPassword(sha1($password_new));
			}
			
			//also set the full name, email, exercise group
			//and save all changes
			$user->setFullname($fullname);
			$user->setEmail($full_email);
			$user->setEgrpId($group);
			$user->save();
			$success = true;
			$_SESSION["user"] = serialize($user);
		}
	}
} ?>