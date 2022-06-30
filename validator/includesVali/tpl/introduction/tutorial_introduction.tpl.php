

<?php
if(
    !(
        isset($l) &&
        $l instanceof FileLocalizer
    )
) {
    throw new Exception('FileLocalizer was not found');
}

/* make sure site is not working anymore */
if (
        isset($user) &&
        $user instanceof User &&
        $user->hasEnoughTutorials()
) { ?>
    <script type="text/javascript">window.location.href="http://localhost/testViktor/test_tutor/publicRoot/sqlvali/index.php"</script>
<?php } ?>


<!DOCTYPE html>
<html>
<head>
        <link href="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.css" rel="stylesheet">

</head>
<body>

<div><p><?php echo $l->getString("tutorial_intro_text"); ?></p></div>
<button id="starttutorial" type="button" class="btn btn-default" onclick="startIntroductionTutorial('<?php echo $l->getLanguage()?>')"><?php echo $l->getString("pageNav_startTutorial"); ?></button>
<div class="main-container"><h1><?php echo $l->getString("view_tasks"); ?></h1>

    <div class="panel-group">
        <div class="panel panel-default">
            <div id="bootstraptourexercise1" class="panel-heading">
                <h4 id="exercise1" class="panel-title"><a id='clickanchor' data-toggle="collapse" data-target="#collapse1"><?php echo $l->getString("tutorial_exerciseSheet") . " 1"; ?></a></h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <ul id='ulid' class="list-group">
                        <li id="exercise1a"class="list-group-item"><a id="exercise1aanchor" href="index.php?action=introduction/tutorial_exercise_page&id=1"><strong><?php echo $l->getString("tutorial_exerciseSheetExampleTask") . " 1"; ?></strong></a><br>
                            <div class="panel"><?= $l->getString("tutorial_exerciseSyntaxText") ?></div></li>
                        <li class="list-group-item"><a href="index.php?action=introduction/tutorial_exercise_page&id=2"><strong><?php echo $l->getString("tutorial_exerciseSheetExampleTask") . " 2"; ?></strong></a>
                            <div class="panel"><?= $l->getString("tutorial_exerciseSchemaText") ?></div></li>
                        <li class="list-group-item"><a href="index.php?action=introduction/tutorial_exercise_page&id=3"><strong><?php echo $l->getString("tutorial_exerciseSheetExampleTask") . " 3"; ?></strong></a>
                        <div class="panel"><?= $l->getString("tutorial_exerciseSchema2Text") ?></div></li>
                    </ul>
                </div>
            </div>
            <div class="panel-heading">
                <h4 class="panel-title"><a data-toggle="collapse" data-target="#collapse2"><?php echo $l->getString("tutorial_exerciseSheet") . " 2"; ?></a>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">Item1
                        <div class = "panel js-panel"></div></li>
                        <li class="list-group-item">Item2</li>
                    </ul>
                </div>
            </div>
    </div>
</div>




    <!--Bootstrap Tour -->
    <script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.js"></script>
    <script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstraptour.tpl.js"></script>


</body>
</html>
