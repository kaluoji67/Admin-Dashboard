function changeErrorSemester(semester,tasks,errors,chart,className)
{
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_task_error.php',
        data: {
            semester: semester.value,
            lang: semester.lang
        },
        success: function (data){
            data = JSON.parse(data);

            // repopulate the task select drop down
            tasks.innerHTML="";
            const taskList = data['taskList'];
            for(const group in taskList)
            {
                const optgroup = document.createElement("optgroup");
                optgroup.setAttribute('label',taskList[group]['group_title']);
                const groupTasks = taskList[group]['tasks'];
                for(const tasks in groupTasks)
                {
                    const task = document.createElement("option");
                    task.setAttribute('value',tasks);
                    task.text = groupTasks[tasks];

                    optgroup.appendChild(task);
                }
                tasks.append(optgroup);
            }

            //repopulate the error List
            errors.innerHTML="";
            const errorList = data['errorList'];
            for(const error in errorList)
            {
                const check = document.createElement("input");
                check.setAttribute('type','checkbox');
                check.setAttribute('class',className);
                check.setAttribute('name',error);
                check.setAttribute('value',errorList[error]);

                const label = document.createElement("label");
                label.setAttribute('for',error);
                label.innerHTML= "    "+error;

                const lineBreak = document.createElement("br");

                errors.appendChild(check)
                errors.appendChild(label);
                errors.appendChild(lineBreak);
            }
            const parentDiv = semester.parentNode.parentNode;
            const allErrorChecks = parentDiv.querySelector('.sem_one_all_error_check');

            const allErrorChecksClone = allErrorChecks.cloneNode(true);
            allErrorChecks.parentNode.replaceChild(allErrorChecksClone, allErrorChecks);

            let errorChecks = parentDiv.querySelectorAll('.sem_one_error_check');

            //reset variables due to wiping
            initialiseCheckListeners(allErrorChecksClone,errorChecks,chart);
            emptyChart(chart);
        }
    });
}
function changeErrorTask(semester,task,proficiency,errors,chart,className)
{
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            url: 'dashboard/includes/helpers/get_task_error.php',
            data: {
                semester: semester.value,
                task: task.value,
                lang: semester.lang,
                proficiency: proficiency
            },
            success: function (data){
                data = JSON.parse(data);

                //repopulate the error List
                errors.innerHTML="";
                const errorList = data['errorList'];
                for(const error in errorList)
                {
                    const check = document.createElement("input");
                    check.setAttribute('type','checkbox');
                    check.setAttribute('class',className);
                    check.setAttribute('name',error);
                    check.setAttribute('value',errorList[error]);

                    const label = document.createElement("label");
                    label.setAttribute('for',error);
                    label.innerHTML= "    "+error;

                    const lineBreak = document.createElement("br");

                    errors.appendChild(check)
                    errors.appendChild(label);
                    errors.appendChild(lineBreak);
                }

                const parentDiv = semester.parentNode.parentNode;
                const allErrorChecks = parentDiv.querySelector('.sem_one_all_error_check');

                const allErrorChecksClone = allErrorChecks.cloneNode(true);
                allErrorChecks.parentNode.replaceChild(allErrorChecksClone, allErrorChecks);

                let errorChecks = parentDiv.querySelectorAll('.sem_one_error_check');

                //reset variables due to wiping
                initialiseCheckListeners(allErrorChecksClone,errorChecks,chart);
                emptyChart(chart);

                resolve();
            }
        });
    });

}

function changeRetrialSemester(semester, groupSelect, chart) {
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_retry_rate.php',
        data: {
            semester: semester.value,
            lang: semester.lang
        },
        success: function (data){
            data = JSON.parse(data);

            const groupList = data['groupList'];
            const retrialList = data['retrialList'];
            console.log(retrialList);
            // repopulate the group select drop down
            groupSelect.innerHTML="";
            for(const group in groupList)
            {
                const option = document.createElement("option");
                option.setAttribute('label',groupList[group]['group_title']);
                option.setAttribute('value',group);
                groupSelect.appendChild(option);
            }

            //get the data to repopulate chart

            let taskTitle = Array();
            let retries = Array();
            for(const task in retrialList)
            {
                let obj = retrialList[task]
                for(const title in obj){
                    taskTitle.push(title);
                    retries.push(obj[title]);
                }
            }
            //populate chart
            chart.data.labels = taskTitle;
            chart.data.datasets[0].data = retries;
            chart.update();

        }
    });
}

function changeRetrialTask(semester, groupSelect,chart){
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_retry_rate.php',
        data: {
            semester: semester.value,
            groupId: groupSelect.value,
            lang: semester.lang
        },
        success: function (data){
            data = JSON.parse(data);
            const retrialList = data['retrialList'];
            //get the data to repopulate chart

            let taskTitle = Array();
            let retries = Array();
            for(const task in retrialList)
            {
                let obj = retrialList[task]
                for(const title in obj){
                    let getNum = title.split(" ");
                    taskTitle.push(getNum[0]);
                    retries.push(obj[title]);
                }
            }
            //populate chart
            chart.data.labels = taskTitle;
            chart.data.datasets[0].data = retries;
            chart.update();
        }
    });
}

function updateQuestChart(qId,taskNum,itemId,semester,lang,chart,chartCanvas,chartDataset){
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_quest_answers.php',
        data: {
            qId: qId,
            taskNum: taskNum,
            INum : itemId,
            semester:semester,
            lang: lang
        },
        success: function (data){
            data = JSON.parse(data);
            //save data to enable chart type swap
            chartDataset.length=0;
            Object.assign(chartDataset, data);

            //adapt data to type of chart
            const type = chart.config.type;
            updateChartFromType(type,chart,chartCanvas,chartDataset)

        }
    });
}
function changeQuestionnaireSem(semester,taskSelect){
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_quest_answers.php',
        data: {
            semester: semester.value,
            lang: semester.lang
        },
        success: function (data){
            data = JSON.parse(data);

            // repopulate the task select drop down
            taskSelect.innerHTML=data;
            taskSelect.dispatchEvent(new CustomEvent("change"));
        }
    });
}

function changeSkillSemester(semester,tasks,proficiency)
{
    proficiency.selectedIndex=0;
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_task_error.php',
        data: {
            semester: semester.value,
            lang: semester.lang
        },
        success: function (data){
            data = JSON.parse(data);

            // repopulate the task select drop down
            tasks.innerHTML="";
            const taskList = data['taskList'];
            for(const group in taskList)
            {
                const optgroup = document.createElement("optgroup");
                optgroup.setAttribute('label',taskList[group]['group_title']);
                const groupTasks = taskList[group]['tasks'];
                for(const tasks in groupTasks)
                {
                    const task = document.createElement("option");
                    task.setAttribute('value',tasks);
                    task.text = groupTasks[tasks];

                    optgroup.appendChild(task);
                }
                tasks.append(optgroup);
            }


        }
    });
}