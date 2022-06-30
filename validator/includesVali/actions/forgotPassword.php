<?php
//created on 14.06.17

if(array_key_exists("submit", $_POST)) {
    $errors = array();
    
    
    //if there exist an id, there are also fields for password and password repetition
    if(isset($_POST["id"]))
    {
        $id = $_POST["id"];
        $password= $_POST["password"];
        $password_repetition= $_POST["password_repetition"];
        
        if(strcmp($password,$password_repetition)!=0){
            $errors[] = "The entered passwords have to be identical.";
            $password = "";
            $password2 = "";
        }
        else
        {
            //get the value of the id
            $fid=$_POST["id"];
            //the last places (from position 16) ist the user id
            $uid=substr($fid,16);
            //the first 16 places is the random value
            $fid=substr($fid,0,16);
        
            $user_temp=User::getById($uid);
        
            //if no errors exist, the new password is st in the database and the random forgot id is set to null
            $user = $user_temp;
            $user->setPassword(sha1($password));
            $user->setForgotId(null);
            $user->save();
        
        
            $_SESSION["user"] = serialize($user);
            $_SESSION["sem_id"] = $user->getSemId();
        
            $page = 'home';
        }
    }
    else
    {
        //get the value of username and email
        $username = $_POST["username"];
        $email = (isset($_POST["email"])?$_POST["email"]:'');
        
        if(($username=='')&&($email==''))
        {
            $errors[]="Please enter username or email.";
        }
        
        //if errors exist, the action will not be execute
        if(empty($errors))
        {
            if($username!='')
            {
                if(User::getByName($username)==null){
                    $errors[] = "The username or the mail doesn't exist.";
                    $username = "";
                    $email = "";
                }
                else
                {
                    $user_fg=User::getByName($username);
                }
            }
            else if($email!='')
            {
                
                if(!strpos($email, '@') || !strpos($email, '.')) {
                    $errors[] = "The e-mail address is not valid.";
                    $email = "";
                }
                
                if((empty($errors))&&(!User::getByCondition("usr_email=?",array($email)))){
                    $errors[] = "The username or the mail doesn't exist.";
                    $username = "";
                    $email = "";
                }
                else
                {
                    $user_fg=User::getByCondition("usr_email=?",array($email));
                    $user_fg=$user_fg[0];
                }
            }
        }
    }
    
    
    
    
    if(empty($errors)&&!isset($_POST["id"])&&isset($user_fg)) {
        //if no error exist and id isn't set, a reset mail is sent
        $to=$user_fg->getEmail();
        $betreff="SQLValidator Reset Password";
        //the rest link use a random value
        $random = mt_rand(1000000000000000,9999999999999999);
        $user_temp = User::getByName($username);
        $text="[deutsche Version unten]
        		
Hello SQL-Validator-User,
        		
now you get a reset link for your password:
"
.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&id=".$random.$user_fg->getId()
."\n \n"
."Please, open this link and enter the new password there.
If you get some errors you have to open this link again.
		
If you do not want to reset, or you remember your password, please ignore this email.
		
Please, do not answer on this mail!
		
Best Regards,
Your SQL-Validator-Team
		
-----------------------------------------------------------
		
Hallo SQL-Validator-Nutzer,
		
hiermit bekommst du einen Link zum Zur".chr(252)."cksetzen deines Passwortes:
"
.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&id=".$random.$user_fg->getId() 
."\n \n"
		
."Bitte ".chr(246)."ffne diesen Link und gebe dort dein neues Passwort ein.
Wenn dabei Fehler auftreten, musst du erneut diesen Link ".chr(246)."ffnen.
		
Wenn du dein Passwort nicht zurücksetzen m".chr(246)."chtest, oder du dich wieder an dein Passwort erinnerst, ignoriere diese Email bitte.
		
Bitte, antworte nicht auf diese Mail!
		
Viele Gr".chr(252)."".chr(223)."e,
Dein SQL-Validator-Team";
		
        $from="From: no-reply@sqlvali.de";
        
        if(mail($to,$betreff,$text,$from))
        {
        	//if mail is sent, the random value is saved in the database for te user
        	$user_fg->setForgotId($random);
        	$user_fg->save();
        }
        else
        {
        	$errors[] = "Error at sending mail";
        }
        if(empty($errors)){
        	$page = 'forgotPassword_successful';
        }
    }
}

?>