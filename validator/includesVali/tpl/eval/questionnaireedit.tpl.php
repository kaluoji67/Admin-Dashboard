<?php if ($questionnaire != null): ?>
    <script src="js/questionnaireedit.js"></script>
    <h1>Editing Questionnaire <?php echo $questionnaire->getPKs()[0] . ": " . $questionnaire->getTitle(); ?></h1>
    <form <?php echo 'action="index.php?action=eval/questionnaireedit&q='.$questionnaire->getPKs()[0].'&l='.$questionnaire->getPKs()[1].'"'?> id="formula" method="Post">
    <!-- Error information -->
    <?php if (isset($errors) and count($errors) != 0): ?>
        <!-- //TODO: Error announcement-->
    <div class="alert alert-danger">
        <?php echo $l->getString("questionnaire_error_message");?>
        <ul>
            <?php foreach ($errors as $error):?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'):?>
        <div class="alert alert-success">
            <?php echo $l->getString("questionnaire_success_edit");?>
        </div>
    <?php endif; ?>
    <!-- Header Information -->
    <div>
        <div class="row">
            <div class="col-1 text-right">
                <label for="NewQID" class="control-label">ID</label>
            </div>
            <div class="col-1">
                <input class="form-control" type="text" name="NewQID"
                       value="<?php echo $questionnaire->getPKs()[0]; ?>" required>
            </div>
            <div class="col-1 text-right">
                <label for="language" class="control-label">Language</label>
            </div>
            <div class="col-1">
                <select class="form-control" name="language">
                    <?php foreach ($l->getSupportedLanguages() as $language) {
                        echo "<option value='" . $language . "'";
                        if ($language == $questionnaire->getPKs()[1]) echo "selected";
                        echo ">" . $language . "</option>";
                    }
                    ?>
                </select></div>
            <div class="col-1 text-right"><label for="title" class="control-label">Title </label></div>
            <div class="col"><input class="form-control" type="text" name="title"
                                    value="<?php echo $questionnaire->getTitle(); ?>" required></div>
        </div>
        <div class="row mt-4">
            <div class="col-1 text-right">
                <label for="type" class="control-label">type</label></div>
            <div class="col"><select class="form-control" name="type">
                    <?php foreach (Questionnaire::getAvailableTypes() as $type) {
                        echo "<option value='" . $type . "'";
                        if ($type == $questionnaire->getType()) echo "selected";
                        echo ">" . $type . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col text-right">
                <label for="proceededBy" class="control-label">Proceeded By</label>
            </div>
            <div class="col">
                <select class="form-control" name="proceededBy">
                    <option value="-1">none</option>
                    <?php //var_dump($questionnaire);
                    foreach ($questsCont as $quest){
                        if ($quest["Q_ID"] != $questionnaire->getPks()[0]) {
                            echo '<option value="' . $quest["Q_ID"] . '"';
                            if ($quest["Q_ID"] == $questionnaire->getProceededby()) echo "selected";
                            echo '>'.$quest["Q_ID"] . ": " . $quest["Q_title"] . ' </option>';
                        }
                    } ?>
                </select>
            </div>
            <div class="col text-right">
                <label for="proceededBy" class="control-label">Active Status</label>
            </div>
            <div class="col">
                <select class="form-control" name="activeStatus">
                    <option value="0" <?php if($questionnaire->getActive() == 0) echo "selected"; ?>>inactive</option>
                    <option value="1" <?php if($questionnaire->getActive() == 1) echo "selected"; ?>>active</option>
                </select>
            </div>
            <div class="col">
                <button type="button" class="btn btn-danger" id="deleterButton" onclick="deleteQuestFunction()"> <span class="glyphicon glyphicon-trash"></span> Delete Questionnaire</button>
                <input type="hidden" name="deleteQuest" id="deleteQuest" value="0">
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-1"><label for="description"> Description </label></div>
            <div class="col"><textarea class="form-control" name="description"
                                       style="height:100%;"><?php echo $questionnaire->getDescription(); ?></textarea>
            </div>
            <div class="col-1"><label for="Semester"> Semester </label></div>
            <div class="col p-2" style="border: solid 1px;">
                <table class="table" id="semesterContainerTable">
                    <tr>
                        <td colspan="2"><select class="form-control" id="addSemester">
                                <?php foreach ($allSemester as $sem) {
                                    echo "<option value='" . $sem[0].";".$sem[1] . "'";
                                    if (in_array($sem[0],$pairedSemIds))
                                        echo "disabled";
                                    echo " id='semesterChooserOption".$sem[0]."'>" . $sem[1] . "</option>";
                                } ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-default" onclick="addSemesterClicker()" >add Semester</button>
                        </td>
                    </tr>
                    <?php //$pairedSemIds = array();//collects the ids of the already paired semester to prepare them for the js operations
                    foreach ($pairedSem as $pSem):
                        //$pairedSemIds[] = $pSem["Sem_Id"]; ?>
                        <tr class="text-center" id="pairedSem<?php echo $pSem["Sem_Id"]; ?>">
                            <td><?php echo $pSem["Sem_Descr"]; ?></td>
                            <td>
                                <button type="button" class="btn btn-default" onclick="EditHiddenTaskClicker(<?php echo $pSem["Sem_Id"];?>)">Edit hidden task</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="deleteSemester(<?php echo $pSem["Sem_Id"]; ?>)">
                                    <span class="glyphicon glyphicon-trash"></span></button>
                            </td>
                        </tr>
                    <?php endforeach;
                    if (count($pairedSem) <= 0)
                        echo "<tr><td colspan='3' class='text-center' >No semesters paired</td></tr>"; ?>
                </table>
                <input type="hidden" id="pairedSemIds" name="pairedSems" value='<?php echo json_encode($pairedSem);//implode(';',$pairedSemIds); ?>'>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col text-right">
                <button type="submit" class="btn btn-success">Save everything</button>
            </div>
        </div>
    </div>
    <!-- Single Tasks -->
    <div id="accordion" class="panel-group mt-3">
        <?php
        $counter = 1;
        $totalQGs = count($questGroups);
        foreach ($questGroups as $qg):?>
            <div class="panel panel-default" id="panel<?php echo $counter; ?>">
                <div class="panel-heading">
                    <div class="row text-center">
                        <!-- taskOrder name gets modified by javascript to establish the right order -> this allows looping through the names in backend
                         - the value stays the same and works as lookup for all the other POST variables
                         - taskID never gets modified and stays there to mark the old ID of the task -> marks whether we should update or insert -->
                        <input type="hidden" class="taskOrder" name="taskOrder<?php echo $counter; ?>" value="<?php echo $counter; ?>">
                        <input type="hidden" name="taskID<?php echo $counter; ?>" value="<?php echo $counter; ?>">
                        <div class="col-1 text-left" id="taskGroupOrderDisplay<?php echo $counter; ?>"><?php echo $counter . ":"; ?></div>
                        <div class="col"><input type="text" class="form-control" name="groupName<?php echo $counter; ?>"
                                                value="<?php echo $qg["Tdescription"] ?>"></div>
                        <div class="col-1">
                            <button type="button" class="btn btn-danger" onclick="deleteTaskGroup(<?php echo $counter;?>)"><span style="color:#000000;"
                                                                               class="glyphicon glyphicon-trash"
                                                                               aria-hidden="true"></span></button>
                        </div>
                        <div class="col-2">
                            <button type="button"
                                    class="btn btn-default" id="buttonOrderDown<?php echo $counter; ?>" onclick="MoveTaskGroup('D',<?php echo $counter; ?>)" <?php if ($counter == $totalQGs) echo "disabled"; ?>>
                                <span style="color:#000000;" class="glyphicon glyphicon-triangle-bottom"
                                      aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn btn-default" id="buttonOrderUp<?php echo $counter; ?>" onclick="MoveTaskGroup('U',<?php echo $counter; ?>)" <?php if ($counter == 1) echo "disabled"; ?>>
                                <span style="color:#000000;" class="glyphicon glyphicon-triangle-top"
                                      aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="col-1"><a class="collapsed" data-toggle="collapse" data-parent="#accordion"
                                              href="#collapse<?php echo $counter; ?>">
                                <span style="color:#000000;" class="glyphicon glyphicon-triangle-bottom"
                                      aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="collapse<?php echo $counter; ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <input type="hidden" value="<?php echo $qg["type"]; ?>"
                               name="originalType<?php echo $counter; ?>">
                        <?php switch ($qg["type"]):
                            case 1://Freetext
                                ?>
                                <!-- Headerinformation task-->
                                <div class="row">
                                    <div class="col-1 text-right">type</div>
                                    <div class="col-3"><select class="form-control" name="newType<?php echo $counter; ?>" onchange="switchTaskType(<?php echo $counter; ?>)">
                                            <?php foreach (Questionnaire::getAvailableTaskTypes() as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $qg["type"]) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Content aka task items -->
                                <table class="table mt-2" id="itemContainertable<?php echo $counter; ?>">
                                <?php $itemCounter=1;
                                if (isset($qg["item"]) && count($qg["item"]) != 0):
                                foreach($qg["item"] as $titem): ?>
                                    <tr id="itemRow<?php echo $counter.".".$itemCounter; ?>"><td>
                                        <input type='hidden' name='taskItemOrder<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                        <input type='hidden' name='taskItemOldID<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                        <div class="row">
                                            <div class="col-2" id="taskItemOrderDisplay<?php echo $counter.".".$itemCounter; ?>" ><?php echo $counter.".".$itemCounter; ?></div>
                                            <div class="col">
                                                 <input type="text" class="form-control" name="itemTitle<?php echo $counter.".".$itemCounter; ?>"
                                                       value="<?php if (isset($titem["Idescription"])) echo htmlspecialchars($titem["Idescription"]); ?>">
                                            </div>
                                            <!-- Delete Task Item Button -->
                                            <div class="col-1">
                                                <button type="button" class="btn btn-danger text-center taskItemDeleter" id="taskItemDeleter<?php echo $counter.".".$itemCounter; ?>" onclick="deleteItem(<?php echo $counter.".".$itemCounter; ?>)" >
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-2 text-right">max input length</div>
                                            <div class="col"><input type="text" class="form-control" name="maxInputLength<?php echo $counter.".".$itemCounter; ?>"
                                                                    value="<?php if (isset($titem["inputLength"])) echo $titem["inputLength"]; ?>"></div>
                                            <div class="col-2 text-right">input type</div>
                                            <div class="col"><select name="inputType<?php echo $counter.".".$itemCounter; ?>" class="form-control">
                                                    <option value="text" <?php if($titem["inputType"] =="text") echo "selected";?>>text</option>
                                                    <option value="int" <?php if($titem["inputType"] =="int") echo "selected";?>>number</option>
                                                </select></div>
                                            <!-- Blank cell to even the adder button out -->
                                            <div class="col-1"></div>
                                        </div>
                                    </td></tr>
                                <?php $itemCounter+=1;
                                endforeach;
                                else:
                                    //echo "<tr><td> No items added </td></tr>";
                                endif;?>
                                <!-- Item Adder Button -->
                                <tr><td class="text-right">
                                        <button type="button" class="btn btn-default itemAdder" onclick="addItem(<?php echo $counter; ?>)" id="itemAdderTask<?php echo $counter; ?>">
                                            <span class="glyphicon glyphicon-plus"></span> Add Item
                                        </button>
                                 </td></tr>
                                </table>
                                <?php break; ?>
                            <?php case 2://Choices?>
                                <!-- Headerinformation task-->
                                <div class="row">
                                    <div class="col-1 text-right">type</div>
                                    <div class="col-3"><select class="form-control" name="newType<?php echo $counter; ?>" onchange="switchTaskType(<?php echo $counter; ?>)">
                                            <?php foreach (Questionnaire::getAvailableTaskTypes() as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $qg["type"]) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Content aka task items -->
                                <table class="table mt-2" id="itemContainertable<?php echo $counter; ?>">
                                    <?php $itemCounter=1;
                                    if (isset($qg["item"]) && count($qg["item"]) != 0):
                                    foreach($qg["item"] as $titem): ?>
                                        <tr id="itemRow<?php echo $counter.".".$itemCounter; ?>"><td>
                                                <input type='hidden' name='taskItemOrder<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                <input type='hidden' name='taskItemOldID<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                <div class="row">
                                                <div class="col-1" id="taskItemOrderDisplay<?php echo $counter.".".$itemCounter; ?>"><?php echo $counter.".".$itemCounter; ?></div>
                                                <div class="col">
                                                    <input type="text" class="form-control" name="itemTitle<?php echo $counter.".".$itemCounter; ?>"
                                                           value="<?php if (isset($titem["Idescription"])) echo htmlspecialchars($titem["Idescription"]); ?>">
                                                </div>
                                                <!-- Delete Task Item Button -->
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-danger text-center taskItemDeleter" id="taskItemDeleter<?php echo $counter.".".$itemCounter; ?>" onclick="deleteItem(<?php echo $counter.".".$itemCounter; ?>)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row mt-3"><div class="col" id="optionsContainer<?php echo $counter.".".$itemCounter;?>">
                                                <?php $options = explode(';',$titem["possibleChoices"]);
                                                $listItemCounter = 1;
                                                foreach($options as $option): ?>
                                                <div class="row mt-2">
                                                    <div class="col-1 text-right"> <span class="glyphicon glyphicon-triangle-right"></span> </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" value="<?php echo $option; ?>" name="listItem<?php echo $counter.".".$itemCounter.".".$listItemCounter; ?>">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="button" class="btn btn-danger text-center" id='listItemDeleter<?php echo $counter.".".$itemCounter.".".$listItemCounter; ?>' onclick="deleteOption(this)">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php $listItemCounter++;
                                                endforeach; ?>
                                                <div class="row mt-2"><div class="col"></div> <div class="col-1">
                                                    <button type="button" class="btn btn-default text-center" onclick="addOption(<?php echo $counter.".".$itemCounter;?>,2)"><span class="glyphicon glyphicon-plus"></span></button>
                                                </div></div>
                                            </div></div>
                                        </td></tr>
                                    <?php $itemCounter++;
                                    endforeach;
                                    else:
                                       // echo "<tr><td> No items added </td></tr>";
                                    endif;?>
                                    <!-- Item Adder Button -->
                                    <tr><td class="text-right">
                                            <button type="button" class="btn btn-default itemAdder" onclick="addItem(<?php echo $counter; ?>)" id="itemAdderTask<?php echo $counter; ?>">
                                                <span class="glyphicon glyphicon-plus"></span> Add Item
                                            </button>
                                        </td></tr>
                                </table>
                                <?php break; ?>
                            <?php case 3://Likerscale
                                $extrema = explode(';', $qg["extrema"]) ?>
                                <!-- Headerinformation task-->
                                <div class="row">
                                    <div class="col-1 text-right">type</div>
                                    <div class="col-3"><select class="form-control" name="newType<?php echo $counter; ?>" onchange="switchTaskType(<?php echo $counter; ?>)">
                                            <?php foreach (Questionnaire::getAvailableTaskTypes() as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $qg["type"]) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-1">scalesize</div>
                                    <div class="col-1"><input type="text" class="form-control" maxlength="2"
                                                              name="scaleScope<?php echo $counter; ?>"
                                                              value="<?php echo $qg["scalesize"] ?>"
                                                              onchange="changeExtremaInputs(<?php echo $counter; ?>)"></div>
                                    <div class="col-1">extrema left</div>
                                    <div class="col"><input type="text" class="form-control"
                                                            name="extremaLeft<?php echo $counter; ?>"
                                                            value="<?php echo $extrema[0]; ?>"></div>
                                    <div class="col-1">extrema right</div>
                                    <div class="col"><input type="text" class="form-control"
                                                            name="extremaRight<?php echo $counter; ?>"
                                                            value="<?php echo $extrema[count($extrema) - 1] ?>"></div>
                                </div>
                                <?php if ($qg["scalesize"] > 2):
                                for ($i = 1; $i < $qg["scalesize"] - 2; $i += 3):?>
                                    <div class="row mt-2">
                                        <div class="col-4"></div>
                                        <div class="col-1">middle extrema</div>
                                        <div class="col"><input type="text" class="form-control"
                                                                name="extrema<?php echo $counter . ";" . $i; ?>"
                                                                value="<?php if (count($extrema)-2 >=$i) echo $extrema[$i]; ?>"
                                                                placeholder="<?php echo $i+1; ?>"></div>
                                        <div class="col"><input type="text" class="form-control"
                                                                name="extrema<?php echo  $counter . ";" .($i + 1); ?>"
                                                                value="<?php if (count($extrema)-2 >=$i+1)  echo $extrema[$i + 1]; ?>"
                                                                placeholder="<?php echo $i+2; ?>"></div>
                                        <div class="col"><input type="text" class="form-control"
                                                                name="extrema<?php echo $counter. ";" . ($i + 2) ; ?>"
                                                                value="<?php if (count($extrema)-2 >=$i+2)  echo $extrema[$i + 2]; ?>"
                                                                placeholder="<?php echo $i+3; ?>"></div>
                                    </div>
                                <?php endfor; ?>
                            <?php endif; ?>
                                <!-- task content - task items-->
                                    <table class="table mt-3" id="itemContainertable<?php echo $counter; ?>">
                                        <?php $itemCounter=1;
                                        if (isset($qg["item"]) && count($qg["item"]) != 0):
                                        foreach($qg["item"] as $titem): ?>
                                            <tr id="itemRow<?php echo $counter.".".$itemCounter; ?>"><td>
                                                    <input type='hidden' name='taskItemOrder<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                    <input type='hidden' name='taskItemOldID<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                    <div class="row">
                                                    <div class="col-1" id="taskItemOrderDisplay<?php echo $counter.".".$itemCounter; ?>"><?php echo $counter.".".$itemCounter; ?></div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" name="itemTitle<?php echo $counter.".".$itemCounter; ?>" value="<?php echo htmlspecialchars($titem["Idescription"]); ?>">
                                                    </div>
                                                    <!-- Delete Task Item Button -->
                                                    <div class="col-1">
                                                        <button type="button" class="btn btn-danger text-center taskItemDeleter" id="taskItemDeleter<?php echo $counter.".".$itemCounter; ?>" onclick="deleteItem(<?php echo $counter.".".$itemCounter; ?>)">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td></tr>
                                        <?php $itemCounter++;
                                        endforeach;
                                        else:
                                           // echo "<tr><td> No items added </td></tr>";
                                        endif;?>
                                        <!-- Item Adder Button -->
                                        <tr><td class="text-right">
                                                <button type="button" class="btn btn-default itemAdder" onclick="addItem(<?php echo $counter; ?>)" id="itemAdderTask<?php echo $counter; ?>">
                                                    <span class="glyphicon glyphicon-plus"></span> Add Item
                                                </button>
                                            </td></tr>
                                    </table>
                                <?php break; ?>
                            <?php case 4://Multiple/Single Choices?>
                                <!-- Headerinformation task-->
                                <div class="row">
                                    <div class="col-1 text-right">type</div>
                                    <div class="col-3"><select class="form-control" name="newType<?php echo $counter; ?>" onchange="switchTaskType(<?php echo $counter; ?>)">
                                            <?php foreach (Questionnaire::getAvailableTaskTypes() as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $qg["type"]) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col text-right">Test type</div>
                                    <div class="col">
                                        <select name="testType<?php echo $counter; ?>" class="form-control">
                                            <option value="mc" <?php if ($qg["extrema"] == "mc") echo "selected"; ?>>
                                                Multiple choice
                                            </option>
                                            <option value="sc" <?php if ($qg["extrema"] == "sc") echo "selected"; ?>>
                                                Single choice
                                            </option>
                                        </select>
                                    </div>
                                        <div class="col text-right">tasks drawn</div>
                                        <div class="col-1"><input type="text" class="form-control" maxlength="2"
                                                                  name="tasksDrawn<?php echo $counter; ?>"
                                                                  value="<?php echo $qg["scalesize"] ?>"></div>
                                </div>
                                <!-- task content - task items-->
                                <table class="table mt-3" id="itemContainertable<?php echo $counter; ?>">
                                    <?php $itemCounter=1;
                                    if (isset($qg["item"]) && count($qg["item"]) != 0):
                                    foreach($qg["item"] as $titem): ?>
                                        <tr id="itemRow<?php echo $counter.".".$itemCounter; ?>"><td>
                                                <input type='hidden' name='taskItemOrder<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                <input type='hidden' name='taskItemOldID<?php echo $counter.".".$itemCounter; ?>' value='<?php echo $counter.".".$itemCounter; ?>' >
                                                <div class="row">
                                                    <div class="col-1" id="taskItemOrderDisplay<?php echo $counter.".".$itemCounter; ?>"><?php echo $counter.".".$itemCounter; ?></div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" value="<?php echo $titem["Idescription"]; ?>">
                                                    </div>
                                                    <!-- Delete Task Item Button -->
                                                    <div class="col-1">
                                                        <button type="button" class="btn btn-danger text-center taskItemDeleter" id="taskItemDeleter<?php echo $counter.".".$itemCounter; ?>" onclick="deleteItem(<?php echo $counter.".".$itemCounter; ?>)">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-3"><div class="col" id="optionsContainer<?php echo $counter.".".$itemCounter;?>">
                                                    <?php
                                                    $options = explode(';',$titem["possibleChoices"]);
                                                    $correctOptions = explode(';',$titem["correctChoices"]);
                                                    $correctOptions = array_map("trim",$correctOptions);//trim all elements to counter any spaces that may occurred
                                                    $listItemCounter = 1;
                                                    foreach($options as $option): ?>
                                                        <div class="row mt-2">
                                                            <div class="col-1 text-right"> <input type="checkbox" name="listItemCB<?php echo $counter.".".$itemCounter.".".$listItemCounter; ?>" class="checkbox" <?php if(in_array(trim($option),$correctOptions)) echo "checked"; ?> > </div>
                                                            <div class="col">
                                                                <input type="text" class="form-control" value="<?php echo $option; ?>" name="listItem<?php echo $counter.".".$itemCounter.".".$listItemCounter; ?>">
                                                            </div>
                                                            <div class="col-1">
                                                                <button type="button" class="btn btn-danger text-center" id='listItemDeleter<?php echo $counter.".".$itemCounter.".".$listItemCounter; ?>' onclick="deleteOption(this)" ><span class="glyphicon glyphicon-trash"></span></button>
                                                            </div>
                                                        </div>
                                                        <?php $listItemCounter++;
                                                    endforeach; ?>
                                                <div class="row mt-2"><div class="col"></div> <div class="col-1">
                                                        <button type="button" class="btn btn-default text-center" onclick="addOption(<?php echo $counter.".".$itemCounter;?>,4)"><span class="glyphicon glyphicon-plus"></span></button>
                                                    </div></div>
                                                </div></div>
                                            </td></tr>
                                        <?php $itemCounter++;
                                    endforeach;
                                    else:
                                      //  echo "<tr><td> No items added </td></tr>";
                                    endif;?>
                                    <!-- Item Adder Button -->
                                    <tr><td class="text-right">
                                            <button type="button" class="btn btn-default itemAdder" onclick="addItem(<?php echo $counter; ?>)" id="itemAdderTask<?php echo $counter; ?>">
                                                <span class="glyphicon glyphicon-plus"></span> Add Item
                                            </button>
                                        </td></tr>
                                </table>
                                <?php break; ?>
                            <?php case 5://SQL tasks?>
                                <!-- Headerinformation task-->
                                <div class="row">
                                    <div class="col-1 text-right">type</div>
                                    <div class="col-3"><select class="form-control" name="newType<?php echo $counter; ?>" onchange="switchTaskType(<?php echo $counter; ?>)">
                                            <?php foreach (Questionnaire::getAvailableTaskTypes() as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $qg["type"]) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-1 text-right">task pool</div>
                                    <div class="col-1"><input type="text" class="form-control" maxlength="2"
                                                              name="sqlTaskPool<?php echo $counter; ?>"
                                                              value="<?php echo $qg["extrema"] ?>"></div>
                                        <div class="col text-right">tasks drawn</div>
                                        <div class="col-1"><input type="text" class="form-control" maxlength="2"
                                                                  name="tasksDrawn<?php echo $counter; ?>"
                                                                  value="<?php echo $qg["scalesize"] ?>"></div>
                                </div>
                                <?php break; ?>
                            <?php endswitch; ?>
                    </div>
                </div>
            </div>
            <?php $counter++;
        endforeach; ?>
        <input type="hidden" name="latestFrontTaskID" id="latestFrontTaskID" value="<?php echo $counter-1; ?>">
        <input type="hidden" name="numberOfTasks" id="numberOfTasks" value="<?php echo $counter-1; ?>">
    </div>
    <!-- Adder and Saver -->
    <div>
        <div class="row mt-4 mb-4">
            <div class="col text-left">
                <button type="button" class="btn btn-default" onclick="addNewTaskGroup()">Add Taskgroup</button>
            </div>
            <div class="col text-right">
                <button type="submit" class="btn btn-success">Save everything</button>
            </div>
        </div>
    </div>
    </form>
    <div id="overOverlay" style="display: none;">
        <div id="overlay" class="container align-content-around" style="z-index: 2;position: fixed;width: 100%;height: 100%;top:0;left:0;right:0;bottom: 0;background: rgba(0,0,0,0.5);">
            <input type="hidden" id="pairedSemTG" value='<?php echo json_encode($tgSemPaired);?>'>
            <div style="position: absolute;top:20%;left:10%;right:10%;background-color:#fff; " class="p-4">
                <h3 class="text-center">Hidden Tasks</h3>
                <table class="table text-center" id="tableHiddenTasks">
                    <tr>
                        <td colspan="2">
                            <select id="hiddenTaskSelecter" class="form-control"></select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" onclick="addHiddenTask()">add Task</button>
                        </td>
                    </tr>
                </table>
                <div class="text-right">
                    <button type="button" class="btn btn-success" onclick="CloseHiddenTaskWindow()">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <h1>No questionnaire found matching this description // New questionnaire</h1>
<?php endif; ?>