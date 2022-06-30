const skillTaskSelect = document.querySelector('.skill_task_select');
const skillProficiencySelect = document.querySelector('.skill_proficiency_select');
const skillSemSelect = document.querySelector('.skill_semester_sel');

skillSemSelect.addEventListener('change',()=>{
    changeSkillSemester(skillSemSelect,skillTaskSelect,skillProficiencySelect);
});

skillTaskSelect.addEventListener('change',()=>{
    changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,-1,divSemOneErrorChecks,errorChart1,"sem_one_error_check");
    semOneErrorProficiencySelect.selectedIndex=0;
});

skillProficiencySelect.addEventListener('change',(e)=>{
    changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,e.target.value,divSemOneErrorChecks,errorChart1,"sem_one_error_check")
        .then(()=>{
            semOneAllErrorChecks.checked=true;
            semOneAllErrorChecks.dispatchEvent(new CustomEvent("change"));
        });

});
skillTaskSelect.dispatchEvent(new CustomEvent("change"));