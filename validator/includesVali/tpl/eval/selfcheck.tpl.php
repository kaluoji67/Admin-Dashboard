<style type="text/css">
    .task{
        display: none;
        opacity: 0;
        transition: opacity 1s;
        z-index: 1;
    }
    .task_active{
        opacity: 1;
        display:block;
        z-index: 2;
    }
    .pagination_filled{
        background-color: #00A000 !important;
        color: #F7FBFF !important;
    }

    .img-rounded{
        margin:5px;
    }
</style>

<H1><?php echo $questionnaire->getTitle();?> </H1>
<?php if (($tookPart == True || (isset($errors) && count($errors) == 0 && $somethingInserted)) && $nextTrial == 0):
    //Evaluation of the selfcheck
    $resPoints = $questionnaire->evaluateSelfcheck($user->getUserEvalID());
    $percentage = number_format((float)($resPoints[1]/$resPoints[0]), 2, '.', '');
?>
    <?php if (isset($otherLanguage)) echo "<div class='alert alert-warning'>The questionnaire is not available in your language!</div>";?>
    <div class="alert <?php if ($percentage >= 0.75) echo "alert-success"; else if ($percentage >= 0.5) echo "alert-warning"; else echo "alert-danger";?>">
        <?php
        //You've ticked the following percentage correctly
        echo $l->getString("selfcheck_feedback_Heading")." ". $resPoints[1]."/".$resPoints[0]." ~ ".($percentage*100)."%.";?>
        <br><br>
        <?php
        if ($percentage >= 0.75)
            echo $l->getString("selfcheck_feedback_contentPositive");
        else if ($percentage >= 0.5)
            echo $l->getString("selfcheck_feedback_contentWarning");
        else
            echo $l->getString("selfcheck_feedback_contentNegative");
        ?>
        <div class="row">
            <div class="col text-left"></div>
            <div class="col text-right">
                <a href="index.php?action=eval/selfcheck&q=<?php echo $QID;?>&t=1">
                    <button type="button" class="btn btn-warning"><?php echo $l->getString("selfcheck_retake")?></button>
                </a>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <ul class="list-group">
            <?php $tNum=1;
            foreach ($resPoints[2] as $task):?>
            <li class="list-group-item">
                <?php if($task["type"] == 4):?>
                    <div class="mt-2 ml-2">
                        <?php  echo $tNum." ".$task["Idescription"];  ?>
                    </div>
                <ul class="list-group">
                <?php $allChoices = explode(';',$task["possibleChoices"]);
                $allChoices = str_replace("'",'',str_replace('"','',$allChoices));
                $chosenChoices = explode(';',$task["result"]);
                $correctChoices = explode(';',$task["correctChoices"]);
                $correctChoices = str_replace("'",'',str_replace('"','',$correctChoices));
                for ($i = 0; $i < count($allChoices); $i++):
                    $isChecked = in_array($allChoices[$i],$chosenChoices) ? "checked" : "";
                    $isCorrect =    ((in_array($allChoices[$i],$correctChoices) && $isChecked) ||
                                    (!in_array($allChoices[$i],$correctChoices) && !$isChecked)) &&
                                    count($chosenChoices)-1 != 0 && floor(count($chosenChoices)/2) != count($allChoices);
                    ?>
                    <li class="list-group-item <?php if($isCorrect)echo "list-group-item-success"; else echo "list-group-item-danger";?>">
                        <div class="input-group">
                            <input type="checkbox" <?php echo $isChecked; ?> disabled>
                            <label><?php echo $allChoices[$i] ?>
                            </label>
                        </div>
                    </li>
                <?php endfor;?>
                </ul>
                <?php $tNum++;//End of task with task type 4?>
                <?php elseif ($task["type"] == 5):
                    $partsTask = explode('||',$task["result"]);
                for($i = 0; $i <count($partsTask)-1;$i++)://Iterate all saved tasks. last one is skipped, because it is empty
                $parts = explode('|',$partsTask[$i]);
                if (count($parts) >1):?>
                    <div class="mt-2 ml-2">
                        <?php  echo $tNum." ".$parts[0];  ?>
                    </div>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $parts[1]; ?></li>
                    <li class="list-group-item"><textarea style="width:100%;" type="text" class="form-group" value="<?php echo $parts[2]; ?>" disabled><?php echo $parts[2]; ?></textarea></li>
                    <?php if ($parts[3] != "(Correct)"):?>
                        <li class="list-group-item list-group-item-danger"><?php echo $parts[3]."<br>";
                        if (count($parts) >=5) echo $parts[4];?></li>
                    <?php else:?>
                        <li class="list-group-item list-group-item-success"><?php echo $parts[3]; ?></li>
                    <?php endif; ?>
                </ul>
                <?php else: ?>
                    <div class="mt-2 ml-2">
                        <?php  echo $tNum." ".$parts[0];  ?>
                    </div>
                <?php endif;//parts if
                    $tNum++;endfor;//for loop for different items
                endif;//tasktype if?>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
<?php else:
    if (isset($errors) and count($errors) != 0):?>
        <div class="alert alert-danger">
            <ul>
                <?php echo $l->getString("questionnaire_error_message");
                foreach ($errors as $error):?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php $descr = $questionnaire->getDescription();
    $paginationIds = array()?>
    <div class="panel-group mt-2">
        <form class="form-horizontal" <?php echo 'action="index.php?action=eval/selfcheck&q=' . $QID . '"' ?>
              method="Post" role="form">
            <input type="hidden" name="QType" value="sc">
            <div id="taskContainer">
                <div class =" task task_active" id="task0">
                    <?php if ($descr != NULL && strlen(trim($descr)) > 0)
                        echo $descr;
                        else
                            echo $l->getString("selfcheck_defaultMessageStart");
                        $paginationIds[] = 0;
                    ?>
                </div>
                <?php ;
                $paginationIdCounter = 1;
                foreach ($tasks as &$task):
                    switch ($task["type"]) {
                        case 1://Normal input area
                            break;
                        case 2://Choices
                            break;
                        case 3://Likerscale
                            break;
                        case 4://Multiple Choice
                            //Go through every task item
                            foreach ($task["item"] as $item):
                                $paginationIds[] = $paginationIdCounter;
                                //Break choices down
                                $choices = explode(";", $item["possibleChoices"]);
                                $itemID = "T" . $task["TaskNum"] . 'I' . $item["INum"];
                                $i = 0;
                                //Check for multi answer Question and switch to checkboxes if necessary
                                $solutions = explode(';', $item["correctChoices"]);
                                $type = $task["extrema"] == "mc" ? "checkbox" : "radio";
                                ?>
                                <div class="task" id="task<?php echo $paginationIdCounter;?>">
                                    <input type="hidden" name="<?php echo $itemID; ?>_numChoices" value="<?php echo count($choices); ?>">
                                    <?php if (isset($item["Idescription"]) and $item["Idescription"] != NULL): ?>
                                        <div class="mt-2 ml-2">
                                            <?php  echo $item["Idescription"];  ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php for ($i = 0; $i < count($choices); $i++): ?>
                                        <li class="list-group-item ">
                                            <!--<div class="input-group">-->
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                <input type="<?php echo $type; ?>" name="<?php echo $itemID."_".$i; ?>"
                                                       id="<?php echo "input_".$paginationIdCounter."_".$i; ?>"
                                                       value='<?php echo str_replace('"','',str_replace("'",'',$choices[$i])); ?>'
                                                    <?php //Display previous answer if we got back to the page due an error
                                                    if (isset($_POST[$itemID]) and $_POST[$itemID] == $choices[$i]) echo "checked"; ?>>
                                                    </div>
                                                    <div class="col">
                                                    <label for="<?php echo "input_".$paginationIdCounter."_".$i; ?>"><?php echo $choices[$i] ?>
                                                </label></div>
                                                </div>
                                            <!--</div>-->
                                        </li>
                                    <?php endfor; ?>
                                </div>
                                <?php $paginationIdCounter++;
                            endforeach;
                            break;
                        case 5:
                            $itemNum = 0;
                            foreach ($task["item"] as $item):
                                $paginationIds[] = $paginationIdCounter;
                                $itemID = "T" . $task["TaskNum"] . 'I' . $itemNum;?>
                            <div class="task" id="task<?php echo $paginationIdCounter;?>">
                                <input type="hidden" name="<?php echo $itemID; ?>_itemID" value="<?php echo $item->getId(); ?>" >
                                <div class="mt-2 ml-2">
                                    <?php  echo $item->getTitle($lang);  ?>
                                </div>
                            <?php echo parseDescription($item->getDescription($lang));
                            //display the item description if given
                            if ($task["Tdescription"] != null) echo "<br>".$task["Tdescription"]."<br><br>";
                            ?>
                                <textarea type="text" name="<?php echo $itemID; ?>"
                                       id="<?php echo "input_".$paginationIdCounter."_0"; ?>"
                                          class="form-control"></textarea>
                            </div>
                            <?php $paginationIdCounter++;$itemNum++;
                            endforeach;
                            break;
                    } ?>
                <?php endforeach; ?>
            </div>
            <div class="text-center">
                <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class='page-item disabled' id="pagination_prev"><a class='page-link' onclick="changeActiveTask('prev')" href="#"> < </a> </li>
                    <?php
                    foreach ($paginationIds as $id):?>
                    <li class='page-item <?php if ($id ==0) echo "active"; ?>' id="pageItem_<?php echo $id; ?>">
                            <a class='page-link ' href='#' id='pagination_<?php echo $id; ?>'
                            onclick='changeActiveTask(<?php echo $id; ?>)' ><?php echo $id; ?></a>
                    </li>
                    <?php endforeach;?>
                    <li class='page-item' id="pagination_next"><a class='page-link' onclick="changeActiveTask('next')" href="#"> > </a> </li>
                </ul>
                </nav>
            </div>
            <div class="row justify-content-end">
                <div class="col">
                    <!-- <button class="btn btn-danger " type="submit"><?php echo $l->getString("questionnaire_abort"); ?> </button>-->
                </div>
                <div class="col"></div>
                <div class="col-4" style="text-align:right;">
                    <?php if ($questionnaire->getProceededby() != NULL): ?>
                        <button class="btn btn-success "
                                type="submit"><?php echo $l->getString("questionnaire_submit_delegate"); ?> </button>
                    <?php else: ?>
                        <button class="btn btn-success "
                                type="submit"><?php echo $l->getString("questionnaire_submit"); ?> </button>
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
<?php endif; ?>

<script type="text/javascript">
    var tasks = document.getElementsByClassName("task");
    var pageLinksRaw = document.getElementsByClassName("page-link");
    var pageItems = new Array(pageLinksRaw.length-2);
    var pageLinkPrev = document.getElementById("pagination_prev");
    var pageLinkNext = document.getElementById("pagination_next");
    for ( var i = 0; i < pageLinksRaw.length;i++){
        id = pageLinksRaw[i].id.split('_')[1];
        pageItems[id] = pageLinksRaw[i].parentNode;
    }
    var currentTask = 0;

    /** Changes the active task in the pagination bottom slider
     * Hides the current active task and shows the next task
     * @param id - id of the next shown task
     */
    function changeActiveTask(id){
        //Special consideration of next and previous buttons
        if (id == "next"){
            //Check whether we already at the last task
            if (currentTask < (tasks.length-1)){
                id = currentTask+1;
            }
        }
        if (id == "prev"){
            //Check whether we are above 0
            if (currentTask > 0){
                id = currentTask-1;
            }
        }
        //mark task as "filled out" if there is an answer given
        var filled = false;
        var inputCounter = 0;
        while (document.getElementById("input_"+currentTask+"_"+inputCounter) != null && !filled){
            var inputArea = document.getElementById("input_"+currentTask+"_"+inputCounter);
            if (((inputArea.type == "checkbox" || inputArea.type == "radio") && inputArea.checked == true) ||
                (inputArea.type == "textarea" && inputArea.value.length != 0))
                filled = true;
            inputCounter++;
        }

        if (filled && document.getElementById("pagination_"+currentTask) != null)
            document.getElementById("pagination_"+currentTask).classList.add("pagination_filled");

        //Remove old disabled prev and next
        if (pageLinkPrev != null && pageLinkPrev.classList.contains("disabled"))
            pageLinkPrev.classList.remove("disabled");
        if (pageLinkNext != null && pageLinkNext.classList.contains("disabled"))
            pageLinkNext.classList.remove("disabled");
        //If there is a valid id update the corresponding css-classes
        if (id != "next" && id != "prev") {
            //hide/show task
            tasks[currentTask].classList.remove("task_active");
            tasks[id].classList.add("task_active");
            //Switch pagination display
            pageItems[currentTask].classList.remove("active");
            pageItems[id].classList.add("active");
            currentTask = id;
            //Disable next and previous in case we are at the end/start
            if ((id == tasks.length-1) && pageLinkNext != null)
                pageLinkNext.classList.add("disabled");
            if ((id == 0) && pageLinkPrev != null)
                pageLinkPrev.classList.add("disabled");
            //Remove the filled class
            if (document.getElementById("pagination_"+currentTask) != null &&
                document.getElementById("pagination_"+currentTask).classList.contains("pagination_filled"))
                document.getElementById("pagination_"+currentTask).classList.remove("pagination_filled");
        }
    }

</script>