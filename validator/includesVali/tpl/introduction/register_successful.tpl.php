<h1><?php echo $l->getString('register_success_header'); ?></h1>
<?php echo $l->getString('register_success_message'); ?>


<?php
//Used for Log-In general Tutorial
//making sure user is not used to the tool yet.
if (
    isset($user) &&
    $user instanceof User) {
    echo '<link href="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.css" rel="stylesheet"><script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.js"></script>
        <script src="/testViktor/test_tutor/includesVali/tpl/introduction/firstLoginTour.tpl.js"></script>
        ';
    //language parameter einbauen
    $language = $l->getLanguage();
    //Token to start Tutorial coming from register only
    echo '<script> sessionStorage.setItem("start_initial_tutorial", "true"); </script>';
    //if selected language is german
    if ($language === 'de') {
        echo '<script>
        startTour("de");</script>';
    }
    //if selected language is english
    if ($language === 'en'){
        echo '<script>
        startTour("en");
    </script>';
    }
} ?>
