
<H1><?php echo $questionnaire->getTitle(); ?> </H1>
<?php if ($tookPart == True): ?>
    <div class="alert alert-warning">
        <?php echo $l->getString("questionnaire_doubleTake_message"); ?>
    </div>
<?php elseif (isset($errors) and count($errors) == 0):?>
    <div class="alert alert-success">
        <?php echo $l->getString("questionnaire_success_message"); ?>
    </div>
<?php else:
    if (isset($errors)): ?>
        <div class="alert alert-danger">
            <ul>
            <?php echo $l->getString("questionnaire_error_message");
            foreach ($errors as $error):?>
                <li><?php echo $error;?></li>
            <?php endforeach;?>
            </ul>
        </div>
<?php endif; ?>
<?php if (isset($otherLanguage)) echo "<div class='alert alert-warning'>The questionnaire is not available in your language!</div>";?>
    <?php $descr = $questionnaire->getDescription();
    if ($descr != NULL)
        echo $descr;?>
    <div class="panel-group mt-2">
    <form class="form-horizontal" <?php echo 'action="index.php?action=eval/questionnaire&q='.$QID.'"'?> method="Post" role="form" >
        <?php foreach ($tasks as &$task):?>
        <div class="panel panel-default mb-4">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <?php echo $task["TaskNum"]." ".$task["Tdescription"] ?>
                </h4>
            </div>
            <div class="panel-body">
                <ul class="list-group">

                    <?php switch($task["type"]){
                        case 1://Normal input area
                            foreach ($task["item"] as $item):
                                $itemID = "T" . $task["TaskNum"] . 'I' . $item["INum"];
                    ?>
                                <li class="list-group-item">
                                    <div class="form-check form-check-inline">
                                        <label class="control-label" style="text-align: left;" for="<?php echo $itemID; ?>">
                                            <?php if ($item["Idescription"] != NULL) {
                                                if (count($task["item"]) > 1)
                                                    echo $item["INum"] . " " . $item["Idescription"];
                                                else
                                                    echo $item["Idescription"];
                                            }?>
                                        </label>
                                        <input class="form-control" type="text" name="<?php echo $itemID; ?>"
                                        value="<?php if (isset($_POST[$itemID])) echo $_POST[$itemID]; ?>" required
                                        maxlength= "<?php if (isset($item["inputLength"]) AND $item["inputLength"] != NULL) echo $item["inputLength"];?>">
                                    </div>
                                </li>
                        <?php
                            endforeach;
                        break;
                        case 2://Choices
                            foreach ($task["item"] as $item) {
                                //Break choices down
                                $choices = explode(";", $item["possibleChoices"]);
                                $itemID = "T" . $task["TaskNum"] . 'I'.$item["INum"];
                                $i = 0; ?>
                                <?php if (isset($item["Idescription"]) AND $item["Idescription"] != NULL): ?>
                                    <div class="mt-2 ml-2">
                                        <?php if (count($task["item"])==1) echo $item["Idescription"]; else echo $item["INum"]." ".$item["Idescription"];?>
                                    </div>
                                <?php endif; ?>
                                <li class="list-group-item ">
                                    <div class="row">
                                        <?php //Display each possible choice
                                        for ($i = 0; $i < count($choices); $i++): ?>
                                            <div class="col">
                                                <div class="input-group">
                                                    <input type="radio"
                                                           name="<?php echo $itemID; ?>"
                                                           id="<?php echo $itemID . $i; ?>"
                                                           value="<?php echo $choices[$i]; ?>" required
                                                        <?php //Display previous answer if we got back to the page due an error
                                                        if (isset($_POST[$itemID]) AND $_POST[$itemID]== $choices[$i]) echo "checked";?>>
                                                    <label for="<?php echo $itemID . $i; ?>"><?php echo $choices[$i] ?></label>
                                                </div>
                                            </div>
                                            <?php //Split the current row to display only 3 choices next to each other
                                            if (($i + 1) % 3 == 0 and $i != 0)
                                                echo '</div><div class="row">';
                                        endfor;
                                        //Fill the remaining spaces up so that the grid is complete
                                        while ($i % 3 != 0) {
                                            echo '<div class="col"></div>';
                                            $i += 1;
                                        } ?>
                                    </div>
                                </li>
                                <?php
                            }
                            break;
                        case 3://Likerscale
                            $scale = $task["scalesize"]; //Defines how many steps are possible between the extrema
                            $extrema = explode(";",$task["extrema"]); //Gets the extrema from the database. First one is left, second right?>
                            <li class="list-group-item">
                                <!-- Scaleheading -->
                                <div class="row mb-2 text-center">
                                    <?php //Check whether the item has an description and if not don't reserve the extra space for it
                                    if (count($task["item"])> 1 OR (count($task["item"]) == 1 AND $task["item"][0]["Idescription"] != NUll )): ?>
                                        <div class="col-lg-6"></div>
                                    <?php endif;
                                        if (count($extrema) == 2):?>
                                        <div class="col"><?php echo $extrema[0]; ?></div>
                                    <?php for ($i = 0;$i<($scale);$i++) echo'<div class="col"></div>'; ?>
                                    <div class="col"> <?php echo $extrema[1]; ?></div>
                                    <?php else:
                                    for ($i = 0;$i<($scale);$i++) echo'<div class="col">'.$extrema[$i].'</div>';
                                    endif;?>

                                </div>
                                <!-- Scalecontent -->
                                <?php $i=1;
                                foreach ($task["item"] as $item):
                                $itemID = "T" . $task["TaskNum"] . 'I' . $item["INum"];?>
                                    <div class="row mb-2 mt-2" <?php if ($i%2==0) echo 'style="background-color:lightgrey;"'?> >
                                        <?php if ($item["Idescription"] != Null): ?>
                                            <div class="col-lg-6"><?php echo $item["Idescription"]; ?></div>
                                        <?php endif; ?>
                                        <?php for($j=0;$j < $scale;$j++):?>
                                            <div class="col">
                                                    <label style="width:100%;text-align:center;" for="<?php echo $itemID.$j;?>">
                                                        <input type="radio"  name="<?php echo $itemID;?>" value="<?php echo $j; ?>" id="<?php echo $itemID.$j;?>"
                                                        <?php //Display previous answer if we got back to the page due an error
                                                        if (isset($_POST[$itemID]) AND $_POST[$itemID]==$j) echo"checked"; ?> required>
                                                    </label>

                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php $i+=1;endforeach;?>
                            </li>
                            <?php break;
                        case 4://Multiple Choice
                            //Go through every task item
                            foreach ($task["item"] as $item){
                                //Break choices down
                                $choices = explode(";",$item["possibleChoices"]);
                                $itemID = "T" . $task["TaskNum"] . 'I'.$item["INum"];
                                $i = 0;
                                //Check for multi answer Question and switch to checkboxes if necessary
                                $solutions = explode(';',$item["correctChoices"]);
                                $type = count($solutions) > 1 ? "checkbox" : "radio";
                                ?>
                                <?php //if there are multiple MC questions open a new list
                                if (count($task["item"])>1):?>
                                <li class="list-group-item ">
                                    <ul class="list-group">
                                <?php endif;?>
                                    <?php if (isset($item["Idescription"]) AND $item["Idescription"] != NULL): ?>
                                    <div class="mt-2 ml-2">
                                        <?php if (count($task["item"])==1) echo $item["Idescription"]; else echo $item["INum"]." ".$item["Idescription"];?>
                                    </div>
                                    <?php endif;?>
                                        <?php for ($i=0; $i < count($choices);$i++):?>
                                                <li class="list-group-item ">
                                                    <div class="input-group">
                                                        <input type="<?php echo $type;?>" name="<?php echo $itemID;?>" id="<?php echo $itemID.$i;?>" value='<?php echo $choices[$i]; ?>'
                                                            <?php //Display previous answer if we got back to the page due an error
                                                            if (isset($_POST[$itemID]) AND $_POST[$itemID]==$choices[$i]) echo"checked"; ?> required>
                                                        <label  for="<?php echo $itemID.$i; ?>"><?php echo $choices[$i]?>
                                                        </label>
                                                    </div>
                                                </li>
                                        <?php endfor;?>
                                <?php if (count($task["item"])>1):?>
                                    </ul>
                                </li>
                            <?php endif;
                            }
                            break;
                    }?>
                </ul>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="row justify-content-end">
            <div class="col">
               <!-- <button class="btn btn-danger " type="submit"><?php echo $l->getString("questionnaire_abort"); ?> </button>-->
            </div>
            <div class="col"></div>
            <div class="col-4" style="text-align:right;">
                <?php if($questionnaire->getProceededby() != NULL): ?>
                    <button class="btn btn-success " type="submit"><?php echo $l->getString("questionnaire_submit_delegate"); ?> </button>
                <?php else:?>
                    <button class="btn btn-success " type="submit"><?php echo $l->getString("questionnaire_submit"); ?> </button>
                <?php endif;?>
            </div>
        </div>

    </form>
    </div>
<?php endif;?>

