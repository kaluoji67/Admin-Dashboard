function changeErrorSemester(semester,tasks,errors,chart,className)
{
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_task_error.php',
        data: {
            semester33: semester.value,
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

            //reset variables due to wiping
            initialiseCheckListeners();
            emptyChart(chart);
        }
    });
}
function changeErrorTask(semester,task,errors,chart,className)
{
    $.ajax({
        type: 'POST',
        url: 'dashboard/includes/helpers/get_task_error.php',
        data: {
            semester33: semester.value,
            task: task.value,
            lang: semester.lang
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

            //reset variables due to wiping
            initialiseCheckListeners();
            emptyChart(chart);
        }
    });
}