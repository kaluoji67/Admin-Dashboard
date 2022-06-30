<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Janice Schmidtke, Alice Stang, Sören Prilop, Fabian Krause">
    <title>SQL-Validator 2.1</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-grid.min.css" rel="stylesheet"><!-- Newly added with V 4.4 -- Rest has to get updated-->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <link href="css/summernote.css" rel="stylesheet">
    <script src="js/summernote.min.js"></script>
    <script src="js/summernote-ext-hello.js"></script>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

    <style>
        body {
            padding-top: 10px;
        }
    </style>

    <link rel="stylesheet" href="css/codemirror.css">
    <script src="js/codemirror.js"></script>
    <script src="js/codemirror_sql.js"></script>

    <script type="text/javascript">
        function loadMirror() {
            $("textarea.sql-input").each(function (index) {
                window.editor = CodeMirror.fromTextArea($(this).get(0), {
                    mode: 'text/x-sql',
                    indentWithTabs: true,
                    smartIndent: true,
                    lineNumbers: true,
                    matchBrackets: true,
                    autofocus: true,
                    extraKeys: {"Ctrl-Space": "autocomplete"}
                });

            });
        }

        //if select "semSelection" was changed, this function, will reload the site and adds ID of the selected semester to the url
        function changeSemester() {
            selection = document.getElementById("semSelection");

            loc = document.location.href.split("index.php?");

            if (loc.length == 1) {
                document.location += "?semSelection=" + selection.value;
            } else {
                newloc = "index.php?";

                restrictedLeftParts = ["delete", "semSelection"];
                restrictedRightParts = ["register", "login"];

                param = loc[1].split("&");
                firstItem = true;
                for (i = 0; i < param.length; i++) {
                    splitted = param[i].split("=");
                    keep = false;

                    if ((restrictedLeftParts.indexOf(splitted[0]) == -1) && (restrictedRightParts.indexOf(splitted[1]) == -1)) {
                        keep = true;
                        if (splitted[0] == 'editSemId') {
                            param[i] = 'editSemId=' + selection.value;
                        }
                    }

                    if (keep) {
                        if (firstItem) {
                            firstItem = false;
                            newloc += param[i];
                        } else {
                            newloc += "&" + param[i];
                        }
                    }
                }

                if (firstItem) {
                    newloc += "semSelection=" + selection.value;
                } else {
                    newloc += "&semSelection=" + selection.value;
                }
                document.location.href = newloc;
            }
        }

        function editSemester_open() {
            selection = document.getElementById("semSelection");
            document.location = "index.php?action=admin/editSemester&editSemId=" + selection.value;
        }

        $(document).ready(loadMirror);
        $(document).ready(function () {
            var url = window.location;
            // Will only work if string in href matches with location
            $('ul.nav a[href="' + url + '"]').parent().addClass('active');

            // Will also work for relative and absolute hrefs
            $('ul.nav a').filter(function () {
                return this.href == url;
            }).parent().addClass('active');
        });

        $(document).ready(function () {
            $('.summernote').summernote();
        });
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div style="float: right; padding-right: 20px;">
    <?php if (@$user && $user->getFlagAdmin() == 'N'): ?>
        <div>
            <label><?php $semDescr = Semester::getByCondition('sem_id = ?', array($_SESSION["sem_id"]));
                echo $semDescr[0]->getDescr(); ?></label>
        </div>
    <?php elseif (@$user && $user->getFlagAdmin() == 'Y'): ?>
        <div>
            <select id='semSelection' name='semSelection' onchange='changeSemester()'>
                <?php foreach (Semester::getAll() as $sem) { ?>
                    <option value='<?php echo $sem->getId() ?>' <?php echo($_SESSION["sem_id"] == $sem->getId() ? "selected" : "") ?>><?php echo $sem->getDescr() ?></option>
                <?php } ?>
            </select>
            <a href="index.php?action=admin/editSemester">+</a>
            <button class="glyphicon glyphicon-pencil" style="border:none; background-color:white;"
                    onclick="editSemester_open();"></button>
        </div>
    <?php endif; ?>
    <a id="flag_de" href="index.php?action=set_lang&lang=de"><img src="img/flag_de.png"/></a>
    <a id="flag_en" href="index.php?action=set_lang&lang=en"><img src="img/flag_en.png"/></a>
</div>
<div class="container">

    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header"> <!-- MC: Not sure what this does. At least it does nothing on the start page -->
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">SQL-Validator 2.1</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php if (@$user && $user->getFlagAdmin() == 'Y'): ?>
                        <li>
                            <a href="index.php?action=admin/viewUsers"><?php echo $l->getString('edit_users'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=admin/viewExerciseGroups"><?php echo $l->getString('exercise_groups', 'Exercise Groups|Übungsgruppen'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=admin/viewTaskGroups"><?php echo $l->getString('edit_task_groups'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=admin/viewTasks"><?php echo $l->getString('edit_tasks'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=admin/viewSubmissions"><?php echo $l->getString('view_submissions'); ?></a>
                        </li>

                    <?php endif; ?>
                    <?php if (isset($user)): ?>
                    <?php if (!isset($_SESSION['hasEnoughTutorials'])){
                          if(!$user->hasEnoughTutorials()){ ?>
                        <li>
                            <a id="tutorial_navBar" href="index.php?action=introduction/tutorial_introduction"><?php echo $l->getString('tutorial_introduction'); ?></a>
                        </li>
                    <?php }
                          else {
                          $_SESSION['hasEnoughTutorials'] = true;}}?>

                        <li>
                            <a href="index.php?action=viewTasks"><?php echo $l->getString('view_tasks'); ?></a>
                        </li>

                        <li>
                            <a href="index.php?action=teams/teams"><?php echo $l->getString('teams'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=dashboard/dash_index"><?php echo $l->getString('dash_index'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=teams/joingroup"><?php echo $l->getString('joingroup'); ?></a>
                        </li>

                    <?php endif; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (!isset($user)): ?>
                        <li>
                            <form action="index.php?action=login" method="post" class="navbar-form navbar-right"
                                  role="form">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username"
                                           placeholder="<?php echo $l->getString('login_enter_username'); ?>">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password"
                                           placeholder="<?php echo $l->getString('login_enter_password'); ?>">
                                </div>
                                <button name="submit" type="submit"
                                        class="btn btn-default"><?php echo $l->getString('login_submit'); ?></button>
                            </form>
                        </li>
                        <li>
                            <a href="index.php?action=forgotPassword"><?php echo $l->getString("forgot_password", "Forgot Password|Passwort vergessen"); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=register"><?php echo $l->getString('register'); ?></a>
                        </li>
                    <?php else: ?>
                        <?php if (@$user && $user->getFlagAdmin() == 'Y'): ?>
                            <li>
                                <a href="index.php?action=eval/evaladmin"><?php echo $l->getString('evaladmin'); ?></a>
                            </li>
                        <?php endif; ?>
                        <!-- 12.05.17: show the option "Account" before the option "LogOut" -->
                        <li>
                            <a id="account_navBar"href="index.php?action=account"><?php echo $l->getString('account', 'Account|Account'); ?></a>
                        </li>
                        <li>
                            <a href="index.php?action=logout"><?php echo $l->getString('logout'); ?></a>
                        </li>

                    <?php endif; ?>
                    <li>
                        <a href="index.php?action=about"><?php echo $l->getString('about'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-lg-12">
            <?php
            if (isset($page)) {
                require __DIR__ . $rootDirectory . "/tpl/$page.tpl.php"; //Load given template
            }
            ?>
        </div>
    </div>

    <div id="ajax_container"></div>
</div>


<?php
//making sure user is not used to the tool yet.
if (
    isset($user) &&
    $user instanceof User &&
    !($user->hasEnoughTutorials())
) {
    echo '<link href="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.css" rel="stylesheet"><script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.js"></script>
        <script src="/testViktor/test_tutor/includesVali/tpl/introduction/firstLoginTour.tpl.js"></script>
        ';
    $language = $l->getLanguage();
    if ($language == 'de'){
        echo '<script> if (!document.getElementById("alert-warning") != null) {
        startTour("de");
    }</script>';
    }

    if ($language == 'en'){
        echo '<script> if (!document.getElementById("alert-warning") != null) {
        startTour("en");
    }</script>';
    }


}

?>

</body>

</html>
