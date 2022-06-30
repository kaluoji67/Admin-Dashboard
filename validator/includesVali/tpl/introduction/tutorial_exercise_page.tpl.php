<?php
if (!isset($l) || !$l instanceof FileLocalizer) {
    throw new Exception('localiser not found');
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.css" rel="stylesheet">

</head>
<body onload="startTutorial(<?= $_GET["id"]; ?>, '<?= $l->getLanguage() ?>')">
<style>
    .solveButtonRight {
        margin-left: auto;
        display: block;
    }

    .img-div {
        text-align: center;
    }
</style>

<?php if (isset($user)) {

}
?>

<main>
    <nav class="row">
        <nav class="col">
            <a href="index.php?action=introduction/tutorial_introduction">
                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"
                                                                    aria-hidden="true"><?= $l->getString("pageNav_backToTutorial") ?></span>
                </button>
            </a>
        </nav>
        <nav class="col text-right pr-1"><a
                    href="index.php?action=introduction/tutorial_exercise_page&id=<?php if ($_GET["id"] == 1) {
                        echo 1;
                    }
                    if ($_GET["id"] == 2) {
                        echo 1;
                    }
                    if ($_GET["id"] == 3) {
                        echo 2;
                    } ?>">
                <button id="predecessorbutton" type="button"
                        class="btn btn-default <?php if ($_GET["id"] == 1) echo "disabled"; ?>"><span
                            class="glyphicon glyphicon-arrow-left"
                            aria-hidden="true"></span><?= ' ' . $l->getString("pageNav_predecessorTask"); ?></button>
            </a></nav>
        <nav class="col text.left pl-0"><a
                    href="index.php?action=introduction/tutorial_exercise_page&id=<?php if ($_GET["id"] == 1) {
                        echo 2;
                    } else if ($_GET["id"] == 2) {
                        echo 3;
                    } else if ($_GET["id"] == 3) {
                        echo 3;
                    }
                    ?>">
                <button id="successorbutton" type="button"
                        class="btn btn-default <?php if ($_GET["id"] == 3) {
                            echo "disabled";
                        } ?>"><?= $l->getString("pageNav_successorTask") . ' '; ?><span
                            class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></button>

            </a></nav>
    </nav>
    <button id="starttutorial" type="button" class="btn btn-default"
            onclick="startTutorial(<?= $_GET["id"]; ?>, '<?= $l->getLanguage() ?>')"><?= $l->getString("pageNav_startTutorial") ?></button>
    <div>
        <article id="solvingsheet">
            <h1><?php echo $l->getString("task_task");
                if ($_GET["id"] == 1) echo " 1"; else if ($_GET["id"] == 2) echo " 2"; else echo " 3"; ?></h1>
            <h2><?php echo $l->getString("task_description_label"); ?></h2>
            <?php if ($_GET["id"] == 1) {
                echo "<div class='exercise'>" . $l->getString("tutorial_exerciseSyntaxText") . "</div>";
            }
            if ($_GET["id"] == 2) {
                echo "<div class='exercise'>" . $l->getString("tutorial_exerciseSchemaText") . '<div class="panel panel-default">  <div class="panel-heading">    <h4 class="panel-title">      <a>Hints</a>    </h4>  </div>  <table class="table table-bordered table-condensed">    <thead>      <tr style="background: #f4f4f4;">        <th><center>employee</center></th>      </tr>    </thead>    <tbody><tr>      <td><table class="table table-hover table-bordered table-condensed"><tbody><tr style="background: #fcfcfc;"><td><b>employee_ID</b></td><td><b>age</b></td><td><b>salary</b></td></tr><tr></tr><tr><td>1</td><td>33</td><td>2900</td></tr><tr><td>2</td><td>38</td><td>3100</td></tr><tr><td>3</td><td>43</td><td>1900</td></tr><tr><td>8</td><td>23</td><td>2200</td></tr><tr><td>21</td><td>48</td><td>3000</td></tr><tr><td>25</td><td>63</td><td>5600</td></tr></tbody></table></td>    </tr>  </tbody></table></div>' . "</div>";
            }
            if ($_GET["id"] == 3) {
                echo "<div class='exercise'>" . $l->getString("tutorial_exerciseSchema2Text") . '<div class="panel panel-default">  <div class="panel-heading">    <h4 class="panel-title">      <a>Hints</a>    </h4>  </div>  <table class="table table-bordered table-condensed">    <thead>      <tr style="background: #f4f4f4;">        <th><center>employee</center></th>      </tr>    </thead>    <tbody><tr>      <td><table class="table table-hover table-bordered table-condensed"><tbody><tr style="background: #fcfcfc;"><td><b>employee_ID</b></td><td><b>Name</b></td><td><b>age</b></td><td><b>salary</b></td></tr><tr></tr><tr><td>1</td><td>John Doe</td><td>33</td><td>2900</td></tr><tr><td>2</td><td>Foo Bar</td><td>38</td><td>3100</td></tr><tr><td>3</td><td>Tony Smith</td><td>43</td><td>1900</td></tr><tr><td>8</td><td>Jennifer Smith</td><td>23</td><td>2200</td></tr><tr><td>21</td><td>Robert Davis</td><td>48</td><td>3000</td></tr><tr><td>25</td><td>Patricia Brown</td><td>63</td><td>5600</td></tr></tbody></table></td>    </tr>  </tbody></table></div>' . "</div>";
            }
            ?>
        </article>
        <br>
        <br>
        <div>
            <div id="result">
                <h2> <?= $l->getString("task_taskresult"); ?> </h2>
                <div id="predefinedQuery"></div>
                <div>
                    <form id="myform">
                        <textarea id='mytextarea' class="form-control sql-input"
                                  placeholder="Put your SQL Query in here!"></textarea>
                    </form>
                </div>
                <button id="solutionbutton" class="btn btn-primary solveButtonRight"
                        onclick="validate_sql_query(<?= $_GET["id"] ?>)"> <?= $l->getString("pageNav_sendSolution"); ?></button>
            </div>

            <br>
            <br>
        </div>
        <div id="success"
             class="alert alert-success hide "><?php echo $l->getString("task_result_success_headline") . ' ' . $l->getString("task_result_success"); ?></div>
        <div id="danger"
             class="alert alert-danger hide"><?php echo $l->getString("task_result_error_headline") . ' ' . $l->getString("task_result_error"); ?>        </div>
        <div id="warning" class="alert alert-warning hide"><b><?php echo $l->getString("task_result_warning_headline");
                echo $l->getString("task_result_warning") . ' ' . $l->getString("er00r_basic_error_row_count1") . ' 4';
                echo $l->getString("er00r_basic_error_row_count2");
                echo ' 6'; ?></b><br>
        </div>'
        <div id="warningSchema2" class="alert alert-warning hide"><b><?php echo $l->getString("task_result_warning_headline");
        echo $l->getString("task_result_warning") . ' ' . $l->getString("er00r_basic_error_column_count1") . ' 2';
        echo $l->getString("er00r_basic_error_column_count2");
                echo ' 4'; ?></b><br>
        </div>

        <?php if($_GET["id"] == 2){echo '
        <div id="wrapresultdiv">
            <div id="resultdiv" class="panel panel-default hide">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a>
                            What the solution should look like </a>
                    </h4>
                </div>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr style="background: #f4f4f4;">
                        <th>
                            <center>employee</center>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table class="table table-hover table-bordered table-condensed">
                                <tbody>
                                <tr style="background: #fcfcfc;">
                                    <td><b>employee_ID</b></td>
                                    <td><b>age</b></td>
                                    <td><b>salary</b></td>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <td>2</td>
                                    <td>38</td>
                                    <td>3100</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>43</td>
                                    <td>1900</td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>48</td>
                                    <td>3000</td>
                                </tr>
                                <tr>
                                    <td>25</td>
                                    <td>63</td>
                                    <td>5600</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>
            <div id="yourresultdiv" class="panel panel-default hide">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a>
                            What your result actually looks like </a>
                    </h4>
                </div>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr style="background: #f4f4f4;">
                        <th>
                            <center>
                                <span id="resultspan" class="glyphicon glyphicon-remove"></span>
                                employee
                            </center>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table class="table table-hover table-bordered table-condensed">
                                <tbody>
                                <tr style="background: #fcfcfc;">
                                    <td><b>employee_ID</b></td>
                                    <td><b>age</b></td>
                                    <td><b>salary</b></td>
                                </tr>
                                <tr></tr>
                                <tr id="age33">
                                    <td>1</td>
                                    <td>33</td>
                                    <td>2900</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>38</td>
                                    <td>3100</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>43</td>
                                    <td>1900</td>
                                </tr>
                                <tr id="age23">
                                    <td>8</td>
                                    <td>23</td>
                                    <td>2200</td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>48</td>
                                    <td>3000</td>
                                </tr>
                                <tr>
                                    <td>25</td>
                                    <td>63</td>
                                    <td>5600</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>';
        }
    if ($_GET["id"] == 3){ echo '<div id="wrapresultdiv2">
            <div id="resultdiv2" class="panel panel-default hide">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a>
                            What the solution should look like </a>
                    </h4>
                </div>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr style="background: #f4f4f4;">
                        <th>
                            <center>employee</center>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table class="table table-hover table-bordered table-condensed">
                                <tbody>
                                <tr style="background: #fcfcfc;">
                                    <td><b>Name</b></td>
                                    <td><b>age</b></td>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <td>Foo Bar</td>
                                    <td>38</td>
                                </tr>
                                <tr>
                                    <td>Tony Smith</td>
                                    <td>43</td>
                                </tr>
                                <tr>
                                    <td>Robert Davis</td>
                                    <td>48</td>
                                </tr>
                                <tr>
                                    <td>Patricia Brown</td>
                                    <td>63</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>
            <div id="yourresultdiv2" class="panel panel-default hide">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a>
                            What your result actually looks like </a>
                    </h4>
                </div>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr style="background: #f4f4f4;">
                        <th>
                            <center>
                                <span id="resultspan" class="glyphicon glyphicon-remove"></span>
                                employee
                            </center>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table class="table table-hover table-bordered table-condensed">
                                <tbody>
                                <tr style="background: #fcfcfc;">
                                    <td id="wrongcolumn" class="wrongcolumn-hide"><b>employee_ID</b></td>
                                    <td><b>Name</b></td>
                                    <td><b>age</b></td>
                                    <td id="wrongcolumn2" class="wrongcolumn-hide"><b>salary</b></td>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <td class="wrongcolumn-hide">2</td>
                                    <td>Foo Bar </td>
                                    <td>38</td>
                                    <td class="wrongcolumn-hide">3100</td>
                                </tr>
                                <tr>
                                    <td class="wrongcolumn-hide">3</td>
                                    <td>Tony Smith</td>
                                    <td> 43</td>
                                    <td class="wrongcolumn-hide">1900</td>
                                </tr>
                                <tr>
                                    <td class="wrongcolumn-hide">21</td>
                                    <td>Robert Davis</td>
                                    <td>48</td>
                                    <td class="wrongcolumn-hide">3000</td>
                                </tr>
                                <tr>
                                    <td class="wrongcolumn-hide">25</td>
                                    <td>Patricia Brown</td>
                                    <td>63</td>
                                    <td class="wrongcolumn-hide">5600</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>';}
    ?>
</main>

<!-- testViktor/test_tutor/includesVali/ -->
<script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstrap-tourist.js"></script>
<script src="/testViktor/test_tutor/includesVali/tpl/introduction/bootstraptour.tpl.js"></script>

<? echo var_dump();//echo getcwd(); ?>
</body>
</html>
