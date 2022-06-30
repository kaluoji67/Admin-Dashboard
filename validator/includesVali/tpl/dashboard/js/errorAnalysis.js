// Error and Retrial tab toggle
const errorTabs = document.querySelectorAll('.error_tab[data-tab-target]')
const errorTabContents = document.querySelectorAll('.error_content[data-tab-content]')

errorTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.tabTarget)
        errorTabContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        errorTabs.forEach(tab => {
            tab.classList.remove('active')
        })
        tab.classList.add('active')
        target.classList.add('active')
    })
})


//tabs in error tab

//check and uncheck errors
//chart 1
let semOneAllErrorChecks = document.querySelector('.sem_one_all_error_check');
let semOneErrorChecks = "";
//chart2
let semTwoAllErrorChecks = document.querySelector('.sem_two_all_error_check');
let semTwoErrorChecks = "";

//important
initialiseCheckListeners();

semOneAllErrorChecks.addEventListener('change',()=>{
    toggleCheck(semOneErrorChecks,semOneAllErrorChecks,errorChart1);
});
semTwoAllErrorChecks.addEventListener('change',()=>{
    toggleCheck(semTwoErrorChecks,semTwoAllErrorChecks,errorChart2);
});

function initialiseCheckListeners(){
    semOneAllErrorChecks.checked=false;
    semTwoAllErrorChecks.checked=false;

    semOneErrorChecks = document.querySelectorAll('.sem_one_error_check');
    semOneErrorChecks.forEach(check=>{
        check.addEventListener('change',()=>{
            updateChart(errorChart1,check);
        });
    });

    semTwoErrorChecks = document.querySelectorAll('.sem_two_error_check');
    semTwoErrorChecks.forEach(check=>{
        check.addEventListener('change',()=>{
            updateChart(errorChart2,check);
        });
    });
}


function toggleCheck(errorChecks, all,chart) {
    if(all.checked == true)
    {
        errorChecks.forEach(check=>{
            check.checked=true;
            updateChart(chart,check);
        });

    }else{
        errorChecks.forEach(check=>{
            check.checked=false;
            emptyChart(chart);
        });
    }
}

function updateChart(chart, check) {
    let label= chart.data.labels;
    let freq = chart.data.datasets[0].data;
    let bg = chart.data.datasets[0].backgroundColor;
    let index = label.indexOf(check.name);
    if(check.checked==true){
        if(index!=-1)
            return;
        chart.data.labels.push(check.name);
        chart.data.datasets[0].data.push(check.value);
        chart.data.datasets[0].backgroundColor.push(colors[parseInt(check.name)]);

        chart.update();

    }else{
        if(index==-1)
            return;
        label.splice(index,1);
        freq.splice(index,1);
        bg.splice(index,1);
        chart.data.labels = label;
        chart.data.datasets[0].data = freq;
        chart.data.datasets[0].backgroundColor = bg;
        chart.update();
    }
}


function emptyChart(chart)
{
    chart.data.labels = [];
    chart.data.datasets[0].data = [];
    chart.data.datasets[0].backgroundColor=[];
    chart.update();
}

//refresh tasks and errors when semester is changed
const divSemOneErrorChecks = document.querySelector('.semester_one_error_checks');
const semOneErrorTaskSelect = document.querySelector('.semester_one_error_task_select');
const semOneErrorSelect = document.querySelector('.semester_one_error_sel');
semOneErrorSelect.addEventListener('change',()=>{ changeErrorSemester(semOneErrorSelect,semOneErrorTaskSelect,divSemOneErrorChecks,errorChart1,'sem_one_error_check')});

semOneErrorTaskSelect.addEventListener('change',()=>{ changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,divSemOneErrorChecks,errorChart1,"sem_one_error_check")});


const divSemTwoErrorChecks = document.querySelector('.semester_two_error_checks');
const semTwoErrorTaskSelect = document.querySelector('.semester_two_error_task_select');
const semTwoErrorSelect = document.querySelector('.semester_two_error_sel');
semTwoErrorSelect.addEventListener('change',()=>{ changeErrorSemester(semTwoErrorSelect,semTwoErrorTaskSelect,divSemTwoErrorChecks,errorChart2,'sem_two_error_check')});

semTwoErrorTaskSelect.addEventListener('change',()=>{ changeErrorTask(semTwoErrorSelect,semTwoErrorTaskSelect,divSemTwoErrorChecks,errorChart2,"sem_two_error_check")});
