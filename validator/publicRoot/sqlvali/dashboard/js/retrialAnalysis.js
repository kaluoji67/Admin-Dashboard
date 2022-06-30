const semOneRetrialTaskSelect = document.querySelector('.semester_one_retrial_task_select');
const semOneRetrialSelect = document.querySelector('.semester_one_retrial_sel');

semOneRetrialSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeRetrialSemester(semOneRetrialSelect,semOneRetrialTaskSelect,retryChart1)
});

semOneRetrialTaskSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeRetrialTask(semOneRetrialSelect,semOneRetrialTaskSelect,retryChart1)
});
semOneRetrialTaskSelect.dispatchEvent(new CustomEvent('change'));

//SEMESTER 2
const semTwoRetrialTaskSelect = document.querySelector('.semester_two_retrial_task_select');
const semTwoRetrialSelect = document.querySelector('.semester_two_retrial_sel');

semTwoRetrialSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeRetrialSemester(semTwoRetrialSelect,semTwoRetrialTaskSelect,retryChart2)
});

semTwoRetrialTaskSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeRetrialTask(semTwoRetrialSelect,semTwoRetrialTaskSelect,retryChart2)
});
semTwoRetrialTaskSelect.dispatchEvent(new CustomEvent("change"));
