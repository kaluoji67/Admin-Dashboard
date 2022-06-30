function addSemesterClicker(){
    var selecter = document.getElementById("addSemester");
    var semester = selecter.value.split(';') ;
    var transmitter = document.getElementById("pairedSemIds");//transmitter is the hidden input area that transmits the paired semester to the backend
    var pairedSemIds = JSON.parse(transmitter.value);
    var tableContainer = document.getElementById("semesterContainerTable").firstElementChild;//table Container provides the table where we add and remove lines for semester display
    var optionElement = document.getElementById("semesterChooserOption" + semester[0]);
    //console.log(tableContainer.lastElementChild);
    //First check if it is able to add the semester
    //console.log(pairedSemIds);
    if (optionElement.disabled == false) {
        //if (pairedSemIds.length == 1 && pairedSemIds[0].length == 0) {
        if (pairedSemIds.length == 0) {
            //console.log(pairedSemIds);
            tableContainer.removeChild(tableContainer.lastElementChild);
        }
        var newLine = document.createElement("tr");
        newLine.classList.add("text-center");
        newLine.id = 'pairedSem' + semester[0];
        newLine.innerHTML = "<td>" + semester[1] + "</td>" +
            "<td><button type=\"button\" class=\"btn btn-default\" onclick=\"EditHiddenTaskClicker(" + semester[0] + ")\">Edit hidden task</button></td>" +
            "<td><button type=\"button\" class=\"btn btn-danger\" onclick='deleteSemester(" + semester[0] + ")'><span class=\"glyphicon glyphicon-trash\"></span></button></td>";
        tableContainer.appendChild(newLine);
        var nO = new Object();
        nO.Sem_Descr = semester[1];
        nO.Sem_Id = semester[0];
        nO.tskg_Ids = [-1];
        pairedSemIds.push(nO);
        transmitter.value = JSON.stringify(pairedSemIds);

        //Disable option to prevent that the same semester is added again
        optionElement.disabled = true;
    }

}

function deleteSemester(semId){
    var transmitter = document.getElementById("pairedSemIds");//transmitter is the hidden input area that transmits the paired semester to the backend
    var tableContainer = document.getElementById("semesterContainerTable").firstElementChild;//table Container provides the table where we add and remove lines for semester display
    var pairedSemIds = JSON.parse(transmitter.value);
    var optionElement = document.getElementById("semesterChooserOption" + semId);
    if (pairedSemIds.length >= 1 && pairedSemIds[0].length==0)
        pairedSemIds.shift();
    var removerElement = document.getElementById("pairedSem"+semId);
    tableContainer.removeChild(removerElement);

    var index = -1;
    for (var i = 0; i < pairedSemIds.length && index == -1;i++){
        if (pairedSemIds[i].Sem_Id == semId)
            index = i;
    }

    if (index >= 0)
        pairedSemIds.splice(index,1);

    transmitter.value = JSON.stringify(pairedSemIds);

    if (pairedSemIds.length == 0){
        var newLine = document.createElement("tr");
        newLine.innerHTML = "<td colspan=\"3\" class=\"text-center\">No semesters paired</td>";
        tableContainer.appendChild(newLine);
    }
    optionElement.disabled = false;
}

function EditHiddenTaskClicker(semId){
    var transmitter = document.getElementById("pairedSemIds");//transmitter is the hidden input area that transmits the paired semester to the backend
    var selecter = document.getElementById("hiddenTaskSelecter");
    var table = document.getElementById("tableHiddenTasks").firstElementChild;
    var pairedSemIds = JSON.parse(transmitter.value);
    var pairedSemTG = JSON.parse(document.getElementById("pairedSemTG").value);
    var availableTGs = null;
    var pairedTGs = null;
    var pairedTGsWithName = [];
    //Search for available TGs
    for(i=0; i < pairedSemTG.length && availableTGs==null;i++){
        if (pairedSemTG[i].sem_id == semId)
            availableTGs = pairedSemTG[i].tgs;
    }
    //Search for paired TGs
    for(i=0; i < pairedSemIds.length && pairedTGs==null;i++){
        if (pairedSemIds[i].Sem_Id == semId)
            pairedTGs = pairedSemIds[i].tskg_Ids;
    }
    //Add all possible to the selecter
    selecter.innerHTML = "";
    for(i=0;i < availableTGs.length;i++){
        var el = document.createElement("option");
        el.value=availableTGs[i].tg_id+";"+availableTGs[i].tg_name;
        el.id="hiddenTaskOption"+availableTGs[i].tg_id;
        el.innerHTML=availableTGs[i].tg_id+":"+availableTGs[i].tg_name;
        if(pairedTGs.includes(Number(availableTGs[i].tg_id))){
            el.disabled = true;
            pairedTGsWithName.push([availableTGs[i].tg_id,availableTGs[i].tg_name]);
        }

        selecter.appendChild(el);
    }
    //Add all already hidden tasks
    while(table.childNodes.length > 1)
        table.removeChild(table.childNodes[table.childNodes.length-1]);
    if (pairedTGs.length == 1 && pairedTGs[0] == -1){
        var el = document.createElement("tr");
        el.innerHTML = "<td colspan='3' id='blankTaskHidden'>No Tasks Hidden - Questionaire/Selfcheck is voluntarily</td>";
        table.appendChild(el);
    }
    else{
        for(i=0;i<pairedTGsWithName.length;i++){
            var el = document.createElement("tr");
            el.id = "hiddenTaskRow"+pairedTGsWithName[i][0];
            el.innerHTML = "<td colspan='2'>"+pairedTGsWithName[i][1]+
                "</td><td><button onclick='deleteHiddenTask("+pairedTGsWithName[i][0]+")' class='btn btn-danger'>" +
                "<span class=\"glyphicon glyphicon-trash\"></span></button></td>";
            table.appendChild(el);
        }
    }
    window.openSemester = semId;//Define open Semester globally to access it inside the hiddenTaskWindow
    document.getElementById("overOverlay").style = "display: block;";
}

function CloseHiddenTaskWindow(){
    document.getElementById("overOverlay").style = "display: none;";
}

function addHiddenTask(){
    var selecter = document.getElementById("hiddenTaskSelecter");
    var taskParts = selecter.value.split(';');
    var option = document.getElementById("hiddenTaskOption"+taskParts[0]);
    var table = document.getElementById("tableHiddenTasks").firstElementChild;
    var transmitter = document.getElementById("pairedSemIds");//transmitter is the hidden input area that transmits the paired semester to the backend
    var pairedSemIds = JSON.parse(transmitter.value);

    option.disabled = true;

    if (document.getElementById("blankTaskHidden") != null){
        document.getElementById("blankTaskHidden").parentNode.removeChild(document.getElementById("blankTaskHidden"));
    }
    var el = document.createElement("tr");
    el.id = "hiddenTaskRow"+taskParts[0];
    el.innerHTML = "<td colspan='2'>"+taskParts[1]+
        "</td><td><button onclick='deleteHiddenTask("+taskParts[0]+")' class='btn btn-danger'>" +
        "<span class=\"glyphicon glyphicon-trash\"></span></button></td>";
    table.appendChild(el);

    //Add it to the input area
    var stopLoop =false;
    for(i=0;i<pairedSemIds.length && !stopLoop;i++){
        if (pairedSemIds[i].Sem_Id == window.openSemester){
            if (pairedSemIds[i].tskg_Ids.length==1 && pairedSemIds[i].tskg_Ids[0] == -1)
                pairedSemIds[i].tskg_Ids.length = 0;
            pairedSemIds[i].tskg_Ids.push(taskParts[0]);
            stopLoop = true;
        }
    }
    transmitter.value = JSON.stringify(pairedSemIds);
}

function deleteHiddenTask(taskGId){
    var selecter = document.getElementById("hiddenTaskSelecter");
    var option = document.getElementById("hiddenTaskOption"+taskGId);
    var table = document.getElementById("tableHiddenTasks").firstElementChild;
    var transmitter = document.getElementById("pairedSemIds");//transmitter is the hidden input area that transmits the paired semester to the backend
    var pairedSemIds = JSON.parse(transmitter.value);
    var rowDeleter = document.getElementById("hiddenTaskRow"+taskGId);

    option.disabled = false;

    rowDeleter.parentNode.removeChild(rowDeleter);

    //Remove it from the input area
    var stopLoop =false;
    for(i=0;i<pairedSemIds.length && !stopLoop;i++){
        if (pairedSemIds[i].Sem_Id == window.openSemester){
            if (pairedSemIds[i].tskg_Ids.length==1 && pairedSemIds[i].tskg_Ids[0] == -1)
                pairedSemIds[i].tskg_Ids.length = 0;
            var tskg_IdsConverted = pairedSemIds[i].tskg_Ids.map(function(x){return String(x)});
            var index = tskg_IdsConverted.indexOf(String(taskGId));
            if (index >= 0)
                pairedSemIds[i].tskg_Ids.splice(index,1);
            stopLoop = true;
            if (pairedSemIds[i].tskg_Ids.length==0) {
                pairedSemIds[i].tskg_Ids.push(-1);

                var el = document.createElement("tr");
                el.innerHTML = "<td colspan='3' id='blankTaskHidden'>No Tasks Hidden - Questionaire/Selfcheck is voluntarily</td>";
                table.appendChild(el);
            }
        }
    }
    transmitter.value = JSON.stringify(pairedSemIds);
}

/**
 * add the task items to the demo
 * @param taskNumber
 */
function addItem(taskNumber){
    var tableContainer = document.getElementById("itemContainertable"+taskNumber).firstElementChild;
    var childCount = tableContainer.childElementCount;

    //Get TaskType directly from the select field
    var taskTypeSelect = document.getElementsByName("newType"+taskNumber)[0];
    var tasktype = Number(taskTypeSelect.options[taskTypeSelect.selectedIndex].value);

    var newRow = document.createElement("tr");
    newRow.id = "itemRow"+taskNumber+"."+childCount;
    switch (tasktype) {
        case 1://Freetext
            newRow.innerHTML = "<td>" +
                "<input type='hidden' name='taskItemOrder"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"' value='"+taskNumber+"."+childCount+"' >"+
                "<div class=\"row\">\n" +
                "<div class=\"col-2\" id=\"taskItemOrderDisplay"+taskNumber+"."+childCount+"\">"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"</div>\n" +
                "<div class=\"col\">\n" +
                "<input type=\"text\" class=\"form-control\" name=\"itemTitle"+taskNumber+"."+childCount+"\">\n" +
                "</div>\n" +
                "<!-- Delete Task Item Button -->\n" +
                "<div class=\"col-1\">\n" +
                "<button type=\"button\" class=\"btn btn-danger text-center taskItemDeleter\" onclick=\"deleteItem("+taskNumber+"."+childCount+")\">\n" +
                "<span class=\"glyphicon glyphicon-trash\"></span>\n" +
                "</button>\n" +
                "</div>\n" +
                "</div>"+
                "<div class=\"row mt-3\">\n" +
                "<div class=\"col-2 text-right\">max input length</div>\n" +
                "<div class=\"col\"><input type=\"text\" class=\"form-control\" name=\"maxInputLength"+taskNumber+"."+childCount+"\"></div>\n" +
                "<div class=\"col-2 text-right\">input type</div>\n" +
                "<div class=\"col\"><select name=\"inputType"+taskNumber+"."+childCount+"\" class=\"form-control\">\n" +
                "<option value=\"text\">text</option>\n" +
                "<option value=\"int\">number</option>\n" +
                "</select></div>\n" +
                "<!-- Blank cell to even the adder button out -->\n" +
                "<div class=\"col-1\"></div>\n" +
                "</div>" +
                "</td>";
            break;
        case 2://Choices
            newRow.innerHTML = "<td>" +
                "<input type='hidden' name='taskItemOrder"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"' value='"+taskNumber+"."+childCount+"' >"+
                "<div class=\"row\">\n" +
                "<div class=\"col-1\" id=\"taskItemOrderDisplay"+taskNumber+"."+childCount+"\">"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"</div>\n" +
                "<div class=\"col\">\n" +
                "<input type=\"text\" class=\"form-control\" name=\"itemTitle"+taskNumber+"."+childCount+"\">\n" +
                "</div>\n" +
                "<!-- Delete Task Item Button -->\n" +
                "<div class=\"col-1\">\n" +
                "<button type=\"button\" class=\"btn btn-danger text-center taskItemDeleter\" onclick=\"deleteItem("+taskNumber+"."+childCount+")\">\n" +
                "<span class=\"glyphicon glyphicon-trash\"></span>\n" +
                "</button>\n" +
                "</div>\n" +
                "</div>" +
                "<div class=\"row mt-3\"><div class=\"col\" id=\"optionsContainer"+taskNumber+"."+childCount+"\" >"+
                "<div class=\"row mt-2\">\n" +
                "<div class=\"col-1 text-right\"> <span class=\"glyphicon glyphicon-triangle-right\"></span> </div>\n" +
                "<div class=\"col\">\n" +
                "<input type=\"text\" class=\"form-control\" name=\"listItem"+taskNumber+"."+childCount+".1\">\n" +
                "</div>\n" +
                "<div class=\"col-1\">\n" +
                "<button type=\"button\" class=\"btn btn-danger text-center\" id='listItemDeleter"+taskNumber+"."+childCount+"' onclick=\"deleteOption(this)\" ><span class=\"glyphicon glyphicon-trash\"></span></button>\n" +
                "</div>\n" +
                "</div>"+
                "  <div class=\"row mt-2\"><div class=\"col\"></div> <div class=\"col-1\">\n" +
                "    <button type=\"button\" class=\"btn btn-default text-center\" onclick=\"addOption("+taskNumber+"."+childCount+" ,2)\" ><span class=\"glyphicon glyphicon-plus\"></span></button>\n" +
                "  </div></div>"+
                "</div></div>"+
                "</td>";
            break;
        case 3://Likerscale
            newRow.innerHTML = "<td>" +
                "<input type='hidden' name='taskItemOrder"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"' value='"+taskNumber+"."+childCount+"' >"+
                "<div class=\"row\">\n" +
                "<div class=\"col-1\" id=\"taskItemOrderDisplay"+taskNumber+"."+childCount+"\">"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"</div>\n" +
                "   <div class=\"col\">\n" +
                "     <input type=\"text\" name='itemTitle"+taskNumber+"."+childCount+"' class=\"form-control\" value=\"\">\n" +
                "   </div>\n" +
                "   <!-- Delete Task Item Button -->\n" +
                "   <div class=\"col-1\">\n" +
                "     <button type=\"button\" class=\"btn btn-danger text-center taskItemDeleter\" onclick=\"deleteItem("+taskNumber+"."+childCount+")\">\n" +
                "       <span class=\"glyphicon glyphicon-trash\"></span>\n" +
                "     </button>\n" +
                "   </div>\n" +
                "</div>" +
                "</td>";
            break;
        case 4://Multiple Choice
            newRow.innerHTML = "<td><div class=\"row\">\n" +
                "<input type='hidden' name='taskItemOrder"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"' value='"+taskNumber+"."+childCount+"' >"+
                "<div class=\"col-1\" id=\"taskItemOrderDisplay"+taskNumber+"."+childCount+"\">"+getOrderIDFromReffererID(taskNumber)+"."+childCount+"</div>\n" +
                "<div class=\"col\">\n" +
                "<input type=\"text\" class=\"form-control\" name=\"itemTitle"+taskNumber+"."+childCount+"\">\n" +
                "</div>\n" +
                "<!-- Delete Task Item Button -->\n" +
                "<div class=\"col-1\">\n" +
                "<button type=\"button\" class=\"btn btn-danger text-center taskItemDeleter\" onclick=\"deleteItem("+taskNumber+"."+childCount+")\">\n" +
                "<span class=\"glyphicon glyphicon-trash\"></span>\n" +
                "</button>\n" +
                "</div>\n" +
                "</div>" +
                "<div class=\"row mt-3\"><div class=\"col\" id=\"optionsContainer"+taskNumber+"."+childCount+"\">"+
                "<div class=\"row mt-2\">\n" +
                "<div class=\"col-1 text-right\"> <input type='checkbox' name=\"listItemCB"+taskNumber+"."+childCount+"\" class='checkbox'></div>\n" +
                "<div class=\"col\">\n" +
                "<input type=\"text\" class=\"form-control\" name=\"listItem"+taskNumber+"."+childCount+".1\">\n" +
                "</div>\n" +
                "<div class=\"col-1\">\n" +
                "<button type=\"button\" class=\"btn btn-danger text-center\" id='listItemDeleter"+taskNumber+"."+childCount+"' onclick=\"deleteOption(this)\"><span class=\"glyphicon glyphicon-trash\"></span></button>\n" +
                "</div>\n" +
                "</div>"+
                "  <div class=\"row mt-2\"><div class=\"col\"></div> <div class=\"col-1\">\n" +
                "    <button type=\"button\" class=\"btn btn-default text-center\" onclick=\"addOption("+taskNumber+"."+childCount+" ,4)\"><span class=\"glyphicon glyphicon-plus\"></span></button>\n" +
                "  </div></div>"+
                "</div></div>"+
                "</td>";
            break;
    }

    tableContainer.insertBefore(newRow,tableContainer.lastElementChild);

}

function deleteItem(taskNum){
    var rowToDelete = document.getElementById("itemRow"+taskNum);

    if (rowToDelete != null)
        rowToDelete.parentNode.removeChild(rowToDelete);
    var taskNumber = Number(String(taskNum).split('.')[0]);
    var itemNumber = Number(String(taskNum).split('.')[1]);
    var tableContainer = document.getElementById("itemContainertable"+taskNumber).firstElementChild;
    var childCount = tableContainer.childElementCount;

    //Add adaptation of number if the element is removed in the middle
    for(i=itemNumber+1;i <= childCount;i++) {
        var reffererItemNumber = getReferrerIDFromOrder(taskNumber+"."+i,true);
        //Update display
        var display = document.getElementById("taskItemOrderDisplay"+reffererItemNumber);
        if (display != null)
            display.innerHTML = taskNumber+"."+(i-1);
        //Update name
        var orderInput = document.getElementsByName("taskItemOrder"+taskNumber+"."+i)[0];
        orderInput.setAttribute("name","taskItemOrder"+taskNumber+"."+(i-1));
    }

}

function addOption(itemNum,taskType) {
    var optionContainer = document.getElementById("optionsContainer"+itemNum);
    var childNodes = optionContainer.childElementCount;

    var newOption = document.createElement("div");
    newOption.classList.add("row");
    newOption.classList.add("mt-2");
    if (taskType == 2){
        newOption.innerHTML =  "<div class=\"col-1 text-right\"> <span class=\"glyphicon glyphicon-triangle-right\"></span> </div>\n" +
            "<div class=\"col\">\n" +
            "  <input type=\"text\" class=\"form-control\" name=\"listItem"+itemNum+"."+childNodes+"\">\n" +
            "</div>\n" +
            "  <div class=\"col-1\">\n" +
            "    <button type=\"button\" class=\"btn btn-danger text-center\" id='listItemDeleter"+itemNum+"."+childNodes+"' onclick=\"deleteOption(this)\">\n" +
            "       <span class=\"glyphicon glyphicon-trash\"></span>\n" +
            "    </button>\n" +
            "  </div>";

    }else if (taskType == 4){
        newOption.innerHTML = "<div class=\"col-1 text-right\"> <input type=\"checkbox\" class=\"checkbox\" name=\"listItemCB"+itemNum+"."+childNodes+"\"> </div>\n" +
            "<div class=\"col\">\n" +
            "  <input type=\"text\" class=\"form-control\"  name=\"listItem"+itemNum+"."+childNodes+"\">\n" +
            "</div>\n" +
            "<div class=\"col-1\">\n" +
            "  <button type=\"button\" class=\"btn btn-danger text-center\" id='listItemDeleter"+itemNum+"."+childNodes+"' onclick=\"deleteOption(this)\"><span class=\"glyphicon glyphicon-trash\"></span></button>\n" +
            "</div>";
    }

    optionContainer.insertBefore(newOption,optionContainer.lastElementChild);
}

function deleteOption(element) {
    if (element != null) {
        var id = element.id.split('.');
        var optionNum = Number(id[id.length-1]);
        var taskNum = id[0].substr(15)+"."+id[1];

        //console.log(optionNum+" op <-- --> task "+taskNum);
        var optionContainer = document.getElementById("optionsContainer"+taskNum);
        var childNodes = optionContainer.childElementCount;

        var deleterRow = element.parentNode.parentNode;
        deleterRow.parentNode.removeChild(deleterRow);

        for(i = optionNum+1; i < childNodes;i++){
            var input = document.getElementsByName("listItem"+taskNum+"."+i)[0];
            input.setAttribute('name',"listItem"+taskNum+"."+(i-1));

            var button = document.getElementById("listItemDeleter"+taskNum+"."+i);
            button.id = "listItemDeleter"+taskNum+"."+(i-1);
        }
    }
}

function switchTaskType(taskNumber){
    //Delete all items currently present in the item
    var itemNum = 1;
    while((deleterRow = document.getElementById("itemRow"+taskNumber+"."+itemNum)) != null){
        deleterRow.parentNode.removeChild(deleterRow);
        itemNum++;
    }
    //Update Task Header
    //Get TaskType directly from the select field
    var taskTypeSelect = document.getElementsByName("newType"+taskNumber)[0];
    var tasktype = Number(taskTypeSelect.options[taskTypeSelect.selectedIndex].value);
    var taskHeaderRow = taskTypeSelect.parentNode.parentNode;
    //First remove everything except the type chooser
    removeAllNextSiblings(taskTypeSelect.parentNode);//Remove parallel items from likerscale for example
    removeAllNextSiblings(taskTypeSelect.parentNode.parentNode,"DIV");//Remove horizontal rows added by likerscale till you reach the table
    switch (tasktype){
        case 1://Freetext - remove everything except the type chooser
        case 2://Choices - same as Freetext
            break;
        case 3://Likerscale - add scalesize and extrema
            appendLikerscaleTaskHeader(taskHeaderRow,taskNumber);
            break;
        case 4://MC-Task
            appendMCTaskHeader(taskHeaderRow,taskNumber);
            break;
        case 5://SQLTask
            appendSQLTaskHeader(taskHeaderRow,taskNumber);
            break;
    }

}

function removeAllNextSiblings(element, fromType=null){
    while(element.nextElementSibling != null && (fromType == null || element.nextElementSibling.nodeName == fromType)) {
        element.parentNode.removeChild(element.nextElementSibling);
    }
}

function changeExtremaInputs(taskNumber){
    var scaleSizeInputArea = document.getElementsByName("scaleScope"+taskNumber)[0];
    if (scaleSizeInputArea != null){
        var scaleSize = Number(scaleSizeInputArea.value);
        if (scaleSize >= 2) {
            var mainRow = scaleSizeInputArea.parentNode.parentNode;
            //Remove previous middle extrema
            /*while (mainRow.nextElementSibling != null && mainRow.nextElementSibling.nodeName == "DIV"){
                mainRow.parentNode.removeChild(mainRow.nextElementSibling);
            }*/
            removeAllNextSiblings(mainRow,"DIV");

            //Add new middle extrema nodes
            for (var i = scaleSize-2; i > 0 ; i -= 3)
                appendExtremaRow(mainRow,i+1-scaleSize%3,i >= scaleSize-2 && i%3 != 0 ? i%3 : 3  , taskNumber);
        }
    }
}

function addNewTaskGroup(){
    var latestIDHiddenInput = document.getElementById("latestFrontTaskID");//provides the latest ID given in the front of the questionnaire edit
    var numberOfElementsHiddenInput = document.getElementById("numberOfTasks");//Shortcut to get the number of tasks in the front
    var panelAccordion = document.getElementById("accordion");//parent Element to add new tasks

    var latestID = Number(latestIDHiddenInput.value);
    var numberOfElements = Number(numberOfElementsHiddenInput.value);//the actual number of elements; tasks are ordered starting with 1

    var lastButtonOrderDown =document.getElementById("buttonOrderDown"+getReferrerIDFromOrder(numberOfElements));
    if (lastButtonOrderDown != null)
        lastButtonOrderDown.disabled = false;

    appendTaskGroup(panelAccordion,latestID+1,numberOfElements+1);

    latestIDHiddenInput.value = latestID+1;
    numberOfElementsHiddenInput.value = numberOfElements+1;

}

function deleteTaskGroup(reffererTaskId){
    var panel = document.getElementById("panel"+reffererTaskId);
    var orderID = getOrderIDFromReffererID(reffererTaskId);
    var numberOfElementsHiddenInput = document.getElementById("numberOfTasks");//Shortcut to get the number of tasks in the front
    var numberOfElements = Number(numberOfElementsHiddenInput.value);//the actual number of elements; tasks are ordered starting with 1

    numberOfElementsHiddenInput.value = numberOfElements-1;

    panel.parentNode.removeChild(panel);
    for(i=orderID+1;i <= numberOfElements;i++){
        var taskID = getReferrerIDFromOrder(i);
        var display = document.getElementById("taskGroupOrderDisplay"+taskID);
        display.innerHTML = (i-1)+":";

        var orderInput = document.getElementsByName("taskOrder"+i)[0];
        orderInput.setAttribute("name","taskOrder"+(i-1));

        counter = 1;
        while(document.getElementsByName("taskItemOrder"+i+"."+counter).length > 0){
            UpdateTaskOrder(i+"."+counter,false,(i-1)+"."+counter,true);
            counter++;
        }
    }

    var buttonUp = document.getElementById("buttonOrderUp"+getReferrerIDFromOrder(1))
    if (buttonUp != null)
        buttonUp.disabled = true;

}

function getReferrerIDFromOrder(taskOrderNum,item=false){
    if (item)
        var elem = document.getElementsByName("taskItemOrder"+taskOrderNum)[0];
    else
        var elem = document.getElementsByName("taskOrder"+taskOrderNum)[0];
    return elem != null ? Number(elem.value) : -1;
}

function getOrderIDFromReffererID(reffererTaskId,item=false){
    if (item)
        var elems = document.getElementsByClassName("taskItemOrder");
    else
        var elems = document.getElementsByClassName("taskOrder");

    for(i=0;i < elems.length;i++){
        if (Number(elems[i].value) == Number(reffererTaskId)){
            return Number(elems[i].name.substr(9));
        }
    }
    return -1;
}

function MoveTaskGroup(direction,refferID){
    var orderNum = getOrderIDFromReffererID(refferID);
    var numberOfElementsHiddenInput = document.getElementById("numberOfTasks");//Shortcut to get the number of tasks in the front
    var numberOfElements = Number(numberOfElementsHiddenInput.value);//the actual number of elements; tasks are ordered starting with 1

    if (orderNum == 1 && direction=="U" || orderNum == numberOfElements && direction=="D")
        return false;

    //Adapt the order Number
    if (direction == "D") {
        var panel = document.getElementById("panel"+refferID);
        var panelNext = document.getElementById("panel"+getReferrerIDFromOrder(orderNum+1));
        UpdateTaskOrder(orderNum,false,numberOfElements+1);
        UpdateTaskOrder(orderNum+1,false);
        UpdateTaskOrder(numberOfElements+1,false,orderNum+1);
        panelNext.after(panel);
        //Update TaskItems of this tasks - Swap all of the in the numbers
        var counter = 1;
        while(document.getElementsByName("taskItemOrder"+orderNum+"."+counter).length > 0){
            UpdateTaskOrder(orderNum+"."+counter,false,"X"+orderNum+"."+counter,true);
            counter++;
        }
        counter = 1;
        while(document.getElementsByName("taskItemOrder"+(orderNum+1)+"."+counter).length > 0){
            UpdateTaskOrder((orderNum+1)+"."+counter,false,orderNum+"."+counter,true);
            counter++;
        }
        counter = 1;
        while(document.getElementsByName("taskItemOrderX"+orderNum+"."+counter).length > 0){
            UpdateTaskOrder("X"+orderNum+"."+counter,false,(orderNum+1)+"."+counter,true);
            counter++;
        }
        //Adapt buttons
        if (orderNum == 1){
            var button = document.getElementById("buttonOrderUp"+getReferrerIDFromOrder(1));
            button.disabled = true;
            button = document.getElementById("buttonOrderUp" + getReferrerIDFromOrder(orderNum+1));
            button.disabled = false;
        }
        if (orderNum == numberOfElements-1){
            var button = document.getElementById("buttonOrderDown"+getReferrerIDFromOrder(orderNum+1));
            button.disabled = true;
            button = document.getElementById("buttonOrderDown" + getReferrerIDFromOrder(orderNum));
            button.disabled = false;
        }

    } else if (direction == "U"){
        var panel = document.getElementById( "panel"+refferID);
        var panelPrevious= document.getElementById("panel"+getReferrerIDFromOrder(orderNum-1));
        UpdateTaskOrder(orderNum,false,numberOfElements+1);
        UpdateTaskOrder(orderNum-1,false,orderNum);
        UpdateTaskOrder(numberOfElements+1,false,orderNum-1);
        panelPrevious.before(panel);
        //Update TaskItems of this tasks
        var counter = 1;
        while(document.getElementsByName("taskItemOrder"+orderNum+"."+counter).length > 0){
            UpdateTaskOrder(orderNum+"."+counter,false,"X"+orderNum+"."+counter,true);
            counter++;
        }
        counter = 1;
        while(document.getElementsByName("taskItemOrder"+(orderNum-1)+"."+counter).length > 0){
            UpdateTaskOrder((orderNum-1)+"."+counter,false,orderNum+"."+counter,true);
            counter++;
        }
        counter = 1;
        while(document.getElementsByName("taskItemOrderX"+orderNum+"."+counter).length > 0){
            UpdateTaskOrder("X"+orderNum+"."+counter,false,(orderNum-1)+"."+counter,true);
            counter++;
        }
        //Adapt buttons
        if (orderNum == 2){
            var button = document.getElementById("buttonOrderUp"+getReferrerIDFromOrder(1));
            button.disabled = true;
            button = document.getElementById("buttonOrderUp" + getReferrerIDFromOrder(orderNum));
            button.disabled = false;
        }
        if (orderNum == numberOfElements){
            var button = document.getElementById("buttonOrderDown"+getReferrerIDFromOrder(orderNum));
            button.disabled = true;
            button = document.getElementById("buttonOrderDown" + getReferrerIDFromOrder(orderNum-1));
            button.disabled = false;
        }
    }
}

function UpdateTaskOrder(number,isRef = true,newOrderNum = -1,isItem = false){
    var refNumber = isRef ? number : getReferrerIDFromOrder(number,isItem);
    var ordNumber = !isRef ? number : getOrderIDFromReffererID(number,isItem);
    var change = (String(newOrderNum).substr(0,1) == "+" || String(newOrderNum).substr(0,1) == "-") ? ordNumber+newOrderNum : newOrderNum;
    //Update display
    //console.log("isItem"+isItem+"refNum"+refNumber+"ordNum"+ordNumber);
    if (isItem) {
        var display = document.getElementById("taskItemOrderDisplay" + refNumber);
        if (display != null)
            display.innerHTML = (change);
        //Update name
        var orderInput = document.getElementsByName("taskItemOrder"+ordNumber)[0];
        orderInput.setAttribute("name","taskItemOrder"+(change));
    }
    else {
        var display = document.getElementById("taskGroupOrderDisplay" + refNumber);
        if (display != null)
            display.innerHTML = (change) + ":";
        //Update name
        var orderInput = document.getElementsByName("taskOrder"+ordNumber)[0];
        orderInput.setAttribute("name","taskOrder"+(change));
    }

}

function deleteQuestFunction(){
    var inputHidden = document.getElementById("deleteQuest");
    var form = document.getElementById("formula");
    if (inputHidden.value == 0){
        inputHidden.value = 1;
        var elements = form.elements;
        for (var i = 0, len = elements.length; i < len; ++i) {
            if (elements[i].type != "submit" && elements[i].type != "hidden")
                elements[i].disabled = true;
        }
        document.getElementById("deleterButton").disabled = false;
    }else {
        inputHidden.value = 0;
        var elements = form.elements;
        for (var i = 0, len = elements.length; i < len; ++i) {
            elements[i].disabled = false;
        }
    }
}

/**-Appending of new Headers and Items -- HTML items -**/
function appendExtremaRow(anchor,firstNum, count, taskNum){
    var newEl = document.createElement('div');
    newEl.classList.add("row");
    newEl.classList.add("mt-2");
    newEl.innerHTML = "<div class=\"col-4\"></div>" +
        "<div class=\"col-1\">middle extrema</div>";
    for(var i = firstNum; i < firstNum+count;i++)
        newEl.innerHTML = newEl.innerHTML+"<div class=\"col\"><input type=\"text\" class=\"form-control\" name=\"extrema"+(i-1)+";"+taskNum+"\" placeholder='"+i+"'></div>";
    anchor.parentNode.insertBefore(newEl,anchor.nextElementSibling);
}

function appendLikerscaleTaskHeader(element,taskNumber){
    var first = document.createElement('div');
    first.classList.add("col-1");
    first.innerHTML = "scalesize";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "<input type=\"text\" class=\"form-control\" maxlength=\"2\" name=\"scaleScope"+taskNumber+"\" onchange=\"changeExtremaInputs("+taskNumber+")\">";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "extrema left";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.classList = ("col");
    first.innerHTML = "<input type=\"text\" class=\" form-control \" name=\"extremaLeft"+taskNumber+"\" >";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "extrema right";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.classList = ("col");
    first.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"extremaRight"+taskNumber+"\" >";
    element.appendChild(first);
}

function appendMCTaskHeader(element,taskNumber) {
    var first = document.createElement('div');
    first.classList.add("col");
    first.innerHTML = "Test type";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = '<select name="testType' + taskNumber + '" class="form-control"><option value="mc"> Multiple choice </option> ' +
        '<option value="sc">Single choice</option>' +
        '</select>';
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "tasks drawn";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "<input type=\"text\" class=\"form-control\" maxlength=\"2\" name=\"tasksDrawn" + taskNumber + "\" >";
    element.appendChild(first);

}

function appendSQLTaskHeader(element,taskNumber) {
    var first = document.createElement('div');
    first.classList.add("col");
    first.innerHTML = "task pool";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = '<input type="text" class="form-control" maxlength="2" name="sqlTaskPool'+taskNumber+'" value="">';
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "tasks drawn";
    element.appendChild(first);
    first = first.cloneNode(true);
    first.innerHTML = "<input type=\"text\" class=\"form-control\" maxlength=\"2\" name=\"tasksDrawn" + taskNumber + "\">";
    element.appendChild(first);

}

function appendTaskGroup(element,taskNumber,taskOrderPosition){
    var newEl = document.createElement('div');
    newEl.classList.add("panel", "panel-default");
    newEl.id = "panel"+taskNumber;
    var disabled = taskOrderPosition == 1 ? "disabled" : "";
    newEl.innerHTML =
        "<div class=\"panel-heading\">" +
        "<div class=\"row text-center\">" +
        "<input type=\"hidden\" class=\"taskOrder\" name=\"taskOrder"+taskOrderPosition+"\" value=\""+taskNumber+"\">" +
        "<div class=\"col-1 text-left\" id=\"taskGroupOrderDisplay"+taskNumber+"\">"+taskOrderPosition+":</div>" +
        "<div class=\"col\"><input type=\"text\" class=\"form-control\" name=\"groupName"+taskNumber+"\" ></div>" +
        "<div class=\"col-1\">" +
        "<button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteTaskGroup("+taskNumber+")\"><span style=\"color:#000000;\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>" +
        "</div>" +
        "<div class=\"col-2\">" +
        "<button type=\"button\" class=\"btn btn-default\" id=\"buttonOrderDown"+taskNumber+"\" onclick=\"MoveTaskGroup('D',"+taskNumber+")\" disabled=\"\">" +
        "<span style=\"color:#000000;\" class=\"glyphicon glyphicon-triangle-bottom\" aria-hidden=\"true\"></span>" +
        "</button>" +
        "<button type=\"button\" class=\"btn btn-default\" id=\"buttonOrderUp"+taskNumber+"\" onclick=\"MoveTaskGroup('U',"+taskNumber+")\" "+disabled+">" +
        "<span style=\"color:#000000;\" class=\"glyphicon glyphicon-triangle-top\" aria-hidden=\"true\"></span>" +
        "</button>" +
        "</div>" +
        "<div class=\"col-1\"><a class=\"collapsed\" data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse"+taskNumber+"\">" +
        "<span style=\"color:#000000;\" class=\"glyphicon glyphicon-triangle-bottom\" aria-hidden=\"true\"></span>" +
        "</a>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "<div id=\"collapse"+taskNumber+"\" class=\"panel-collapse collapse\">" +
        "<div class=\"panel-body\">" +
        "<input type=\"hidden\" value=\"-1\" name=\"originalType"+taskNumber+"\">" +
        "                                                        <!-- Headerinformation task-->" +
        "<div class=\"row\">" +
        "<div class=\"col-1 text-right\">type</div>" +
        "<div class=\"col-3\"><select class=\"form-control\" name=\"newType"+taskNumber+"\" onchange=\"switchTaskType("+taskNumber+")\">" +
        "                                                                                            <option value=\"1\" selected>Freetext</option>" +
        "                                                                                            <option value=\"2\">Choices</option>" +
        "                                                                                            <option value=\"3\">Likerscale</option>" +
        "                                                                                            <option value=\"4\">MultipleChoice</option>" +
        "                                                                                            <option value=\"5\">SQLTask</option>" +
        "                                                                                    </select>" +
        "                                    </div>" +
        "                                </div>" +
        "                                                                                            <!-- task content - task items-->" +
        "<table class=\"table mt-3\" id=\"itemContainertable"+taskNumber+"\">" +
        "                                                                                <!-- Item Adder Button -->\n" +
        "  <tr><td class=\"text-right\">\n" +
        "  <button type=\"button\" class=\"btn btn-default itemAdder\" onclick=\"addItem("+taskNumber+")\" id=\"itemAdderTask"+taskNumber+"\">\n" +
        "     <span class=\"glyphicon glyphicon-plus\"></span> Add Item" +
        "  </button>" +
        "  </td></tr>" +
        "</table>" +
        "</div>" +
        "</div>";
    element.appendChild(newEl);
}