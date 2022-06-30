<H1><?php echo $l->getString("evaladmin_title") ?></H1>

<!-- Block 1: Queries Statistics overview combined with reset and export-->
<h3><?php echo $l->getString("evaladmin_querystats_title"); ?></h3>
<form action="export.php" method="POST">
    <input type="hidden" name="type" value="task">
    <div id="accordion" class="panel-group">
        <?php $tskgCounter = 0;
        foreach ($tskgStatisticsCont as $tskg): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-1 text-center">
                            <input type="checkbox" class="cb_taskGroup"
                                   id="cb_tskg<?php echo $tskgCounter; ?>"
                                   name="tskgC<?php echo $tskgCounter; ?>">
                            <input type="hidden" name="tskgId<?php echo $tskgCounter; ?>"
                                   value="<?php echo $tskg["id"]; ?>">
                        </div>
                        <div class="col"><?php echo $tskg["name"]; ?></div>
                        <div class="col-2 text-center"><?php echo $tskg["count"]; ?></div>
                        <div class="col-1 text-center">
                            <a data-toggle="collapse" data-parent="#accordion"
                               href="#collapse<?php echo $tskg["id"]; ?>">
                                            <span style="color:#000000;" class="glyphicon glyphicon-triangle-bottom"
                                                  aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="collapse<?php echo $tskg["id"]; ?>" class="panel-collapse collapse ">
                    <div class="panel-body">
                        <ul class="list-group">
                            <?php $tskCounter = 0;
                            foreach ($tskg["tasks"] as $task): ?>
                                <li class="list-group-item pr-0 mr-0">
                                    <div class="row">
                                        <div class="col-1 text-center">
                                            <input type="checkbox" class="cb_task"
                                                   name="tskC<?php echo $tskgCounter . '_' . $tskCounter; ?>">
                                            <input type="hidden"
                                                   name="tskId<?php echo $tskgCounter . '_' . $tskCounter; ?>"
                                                   value="<?php echo $task["id"]; ?>">
                                        </div>
                                        <div class="col"><?php echo $task["name"]; ?></div>
                                        <div class="col-2 text-center"><?php echo $task["count"]; ?></div>
                                        <div class="col-1"></div>
                                    </div>
                                </li>
                                <?php $tskCounter += 1;
                            endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php $tskgCounter += 1;
        endforeach; ?>
    </div>

    <div class="row">
        <div class="col text-center">
            <button type="button" class="btn btn-default"
                    disabled><?php echo $l->getString("evaladmin_queststats_btnreset"); ?></button>
        </div>
        <div class="col text-center">
            <button type="submit"
                    class="btn btn-default"><?php echo $l->getString("evaladmin_queststats_btnexport"); ?></button>
        </div>
    </div>
</form>
<!-- Block2: Statistics for Questionnaires and the possibility to edit them-->
<h3><?php echo $l->getString("evaladmin_queststats_title"); ?></h3>
<table class="table">
    <tr>

        <th>ID</th>
        <th>Name</th>
        <th><?php echo $l->getString("evaladmin_heading_type") ?></th>
        <th>Status</th>
        <th><?php echo $l->getString("evaladmin_heading_participants") ?></th>
        <th colspan="3"></th>

    </tr>
    <?php $questIDs = array();
    foreach ($questsCont as $quest): ?>
        <tr>
            <td class="col-sm-1"><?php echo $quest["Q_ID"]; ?></td>
            <td class="col"><?php echo $quest["Q_language"] . ":" . $quest["Q_title"]; ?></td>
            <td class="col"><?php echo $quest["Q_type"] != NULL ? $quest["Q_type"] : "Not specified"; ?></td>
            <td class="col"><?php
                echo $quest["Q_active"] == 1 ? $l->getString("evaladmin_active") : $l->getString("evaladmin_inactive");
                ?></td>
            <td class="col-sm"><?php echo $quest["participants"]; ?></td>
            <td class="col"><!-- Edit questionnaire-->
                <a href="index.php?action=eval/questionnaireedit&q=<?php echo $quest["Q_ID"] . "&l=" . $quest["Q_language"]; ?>">
                    <button type="button" class="btn btn-default">
                        <?php echo $l->getString("evaladmin_queststats_btneditquest"); ?>
                    </button>
                </a>
            </td>
            <td class="col-1"><!-- View questionnaire-->
                <a href="index.php?action=eval/questionnaire&q=<?php echo $quest["Q_ID"]; ?>">
                    <button type="button" name="viewQuestionnaire<?php echo $quest["Q_ID"]; ?>"
                            class="btn btn-default">
                        <?php echo $l->getString("evaladmin_queststats_btnviewquest"); ?>
                    </button>
                </a>
            </td>
            <td class="col-1"><!-- Export questionnaire-->
                <form action="export.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $quest["Q_ID"]; ?>">
                    <input type="hidden" name="lang" value="<?php echo $quest["Q_language"]; ?>">
                    <input type="hidden" name="type" value="quest">
                    <button type="submit" class="btn btn-default">
                        <?php echo $l->getString("evaladmin_queststats_btnexportquest"); ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php if (!in_array($quest["Q_ID"] . ":" . $quest["Q_language"], $questIDs))
            $questIDs[] = $quest["Q_ID"] . ":" . $quest["Q_language"];
    endforeach; ?>
</table>
<div class="row mb-5">
    <div class="col-2 text-right">
        <select class="form-control" id="copySource">
            <?php foreach ($questIDs as $qID): ?>
                <option value="<?php echo $qID; ?>"><?php echo $qID; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col text-left">
        <button type="button" class="btn btn-default" onclick="copyQuest()">
            <?php echo $l->getString("evaladmin_queststats_btncopyquest"); ?>
        </button>
    </div>
    <div class="col text-center">
        <a href="index.php?action=eval/questionnaireedit&q=new0">
            <button type="button" class="btn btn-default">
                <?php echo $l->getString("evaladmin_queststats_btnnewquest"); ?>
            </button>
        </a>
    </div>
</div>
<!-- Block3: Export opportunities for the time tracking system -->
<h3>Time Tracking System</h3>
<table class="table">
    <tr>
        <th>semester</th>
        <th></th>
    </tr>
    <tr><form action="export.php" method="POST">
            <input type="hidden" id="TTSType" name="type" value="tts">
        <td>
            <select name="semester" class="form-control">
                <?php foreach ($semester as $sem):?>
                    <option value="<?php echo $sem->getId();?>"><?php echo $sem->getDescr();?></option>
                <?php endforeach;?>
            </select>
        </td>
        <td>
            <button type="submit" class="btn btn-default"> export raw </button>
        </td>
        <td>
            <button type="submit" class="btn  btn-default" onclick="changeTTSExport()">export calculated</button>
        </td>
        </form>
    </tr>
</table>
<!-- Javascript part - Only used in this side-->
<script type="text/javascript">
    function changeTTSExport() {
        var hiddenType = document.getElementById("TTSType");
        hiddenType.value = "ttsC";
    }
    function copyQuest() {
        var source = document.getElementById("copySource");
        var value = source.options[source.selectedIndex].value;
        value = value.split(':');
        location.replace("index.php?action=eval/questionnaireedit&q=new" + value[0] + "&l=" + value[1]);
    }

    //Create on Click Function for taskgroup Checkbox
    function ListenerClickTaskGroup(event) {
        if (event != null && event.target != null) {
            var target = event.target;
            var groupID = target.name.substr(5);
            var tasks = document.getElementsByClassName("cb_task");
            //Either check or uncheck all tasks when the task group is checked or unchecked
            for (var i = 0; i < tasks.length; i++) {
                if (tasks[i].name.split('_')[0].substr(4) == groupID) {
                    if (target.checked)
                        tasks[i].checked = true;
                    else
                        tasks[i].checked = false;
                }
            }
        }

    }

    var tskGroups = document.getElementsByClassName("cb_taskGroup");
    for (var i = 0; i < tskGroups.length; i++) {
        tskGroups[i].addEventListener('click', ListenerClickTaskGroup, false);
    }

    //Create On Click for Tasks
    function ListenerClickTask(event) {
        if (event != null && event.target != null) {
            var target = event.target;
            var groupID = target.name.split('_')[0].substr(4);
            var tasks = document.getElementsByClassName("cb_task");
            var countOn = 0;
            var countOff = 0;
            //Count how many of the tasks in the task group are checked. Stop when there aren't all unchecked or checked
            for (var i = 0; i < tasks.length && (countOff == 0 || countOn == 0); i++) {
                if (tasks[i].name.split('_')[0].substr(4) == groupID) {
                    if (tasks[i].checked)
                        countOn += 1;
                    else
                        countOff += 1;
                }
            }
            //get parent task group and set it either to indeterminate if the single tasks are mixed ot to the whole task selection
            var parenttaskG = document.getElementById("cb_tskg" + groupID);
            if (parenttaskG != null) {
                if (countOff != 0 && countOn != 0) {
                    parenttaskG.checked = false;
                    parenttaskG.indeterminate = true;
                } else if (countOn != 0) {
                    parenttaskG.checked = true;
                    parenttaskG.indeterminate = false;
                } else {
                    parenttaskG.checked = false;
                    parenttaskG.indeterminate = false;
                }
            }
        }

    }

    var tsks = document.getElementsByClassName("cb_task");
    for (var i = 0; i < tsks.length; i++) {
        tsks[i].addEventListener('click', ListenerClickTask, false);
    }
</script>