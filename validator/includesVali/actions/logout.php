<?php

$logoutSuccessful = false;

if(!empty($user)) {
    unset($_SESSION["user"]);
    unset($user);
    $logoutSuccessful = true;
}

?>