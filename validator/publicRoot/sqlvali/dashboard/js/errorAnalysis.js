const addNewErrorChart = document.querySelector(".add_new_error_chart");
addNewErrorChart.addEventListener('click',()=>{
    duplicateNode("error_chart_div");
});
function duplicateNode(className){
    const elem = document.querySelector('.'+className);
    const parent = elem.parentNode;
    var clone = elem.cloneNode(true);

// Update the ID and add a class
    clone.id = 'elem2';
    clone.classList.add('text-large');

// Inject it into the DOM
    const brk = document.createElement("br");
    parent.appendChild(brk);
    parent.appendChild(clone);

    let errorChart= new Chart(clone.querySelector("#error_bar_chart1"), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                {
                    label: "",
                    backgroundColor: [],
                    data: []
                }
            ]
        },
        options: {
            plugins:{
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Error Frequency of Task'
                }
            },
            maintainAspectRatio: false,
            scales: {
                yAxes: {
                    title: {
                        display: true,
                        text: 'Error Frequency'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });
    createErrorListeners(clone,errorChart);
}
//duplicateNode("semester_one")

const errorDiv = document.querySelector('.error_chart_div');
let errorChart= new Chart(errorDiv.querySelector("#error_bar_chart1"), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            {
                label: "",
                backgroundColor: [],
                data: []
            }
        ]
    },
    options: {
        plugins:{
            legend: { display: false },
            title: {
                display: true,
                text: 'Error Frequency of Task'
            }
        },
        maintainAspectRatio: false,
        scales: {
            yAxes: {
                title: {
                    display: true,
                    text: 'Error Frequency'
                },
                ticks: {
                    beginAtZero: true
                }
            }
        }
    }
});
createErrorListeners(errorDiv,errorChart)
function createErrorListeners(div,errorChart)
{
    let semOneAllErrorChecks = div.querySelector('.sem_one_all_error_check');
    let semOneErrorChecks = div.querySelectorAll('.sem_one_error_check');

    initialiseCheckListeners(semOneAllErrorChecks,semOneErrorChecks,errorChart);

    //refresh tasks and errors when semester is changed
    const divSemOneErrorChecks = div.querySelector('.semester_one_error_checks');
    const semOneErrorTaskSelect = div.querySelector('.semester_one_error_task_select');
    const semOneErrorProficiencySelect = div.querySelector('.semester_one_error_proficiency_select');
    const semOneErrorSelect = div.querySelector('.semester_one_error_sel');

    semOneErrorSelect.addEventListener('change',()=>{ changeErrorSemester(semOneErrorSelect,semOneErrorTaskSelect,divSemOneErrorChecks,errorChart,'sem_one_error_check')});

    semOneErrorTaskSelect.addEventListener('change',()=>{
        changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,-1,divSemOneErrorChecks,errorChart,"sem_one_error_check");
        semOneErrorProficiencySelect.selectedIndex=0;
    });

    semOneErrorProficiencySelect.addEventListener('change',(e)=>{
        changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,e.target.value,divSemOneErrorChecks,errorChart,"sem_one_error_check")
            .then(()=>{
                allErrorChecks = div.querySelector('.sem_one_all_error_check');
                allErrorChecks.checked=true;
                allErrorChecks.dispatchEvent(new CustomEvent("change"));
            });

    });
    semOneErrorTaskSelect.dispatchEvent(new CustomEvent("change"));
}
function initialiseCheckListeners(semOneAllErrorChecks,semOneErrorChecks,errorChart){
    semOneAllErrorChecks.checked=false;

    semOneAllErrorChecks.addEventListener('change',()=>{
        toggleCheck(semOneErrorChecks,semOneAllErrorChecks,errorChart);
    });

    semOneErrorChecks.forEach(check=>{
        check.addEventListener('change',()=>{
            updateChart(errorChart,check);
        });
    });
}

/*
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

//refresh tasks and errors when semester is changed
const divSemOneErrorChecks = document.querySelector('.semester_one_error_checks');
const semOneErrorTaskSelect = document.querySelector('.semester_one_error_task_select');
const semOneErrorProficiencySelect = document.querySelector('.semester_one_error_proficiency_select');
const semOneErrorSelect = document.querySelector('.semester_one_error_sel');

semOneErrorSelect.addEventListener('change',()=>{ changeErrorSemester(semOneErrorSelect,semOneErrorTaskSelect,divSemOneErrorChecks,errorChart1,'sem_one_error_check')});

semOneErrorTaskSelect.addEventListener('change',()=>{
    changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,-1,divSemOneErrorChecks,errorChart1,"sem_one_error_check");
    semOneErrorProficiencySelect.selectedIndex=0;
});

semOneErrorProficiencySelect.addEventListener('change',(e)=>{
    changeErrorTask(semOneErrorSelect,semOneErrorTaskSelect,e.target.value,divSemOneErrorChecks,errorChart1,"sem_one_error_check")
        .then(()=>{
            semOneAllErrorChecks.checked=true;
            semOneAllErrorChecks.dispatchEvent(new CustomEvent("change"));
        });

});
semOneErrorTaskSelect.dispatchEvent(new CustomEvent("change"));

const divSemTwoErrorChecks = document.querySelector('.semester_two_error_checks');
const semTwoErrorTaskSelect = document.querySelector('.semester_two_error_task_select');
const semTwoErrorSelect = document.querySelector('.semester_two_error_sel');
const semTwoErrorProficiencySelect = document.querySelector('.semester_two_error_proficiency_select');

semTwoErrorSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeErrorSemester(semTwoErrorSelect,semTwoErrorTaskSelect,divSemTwoErrorChecks,errorChart2,'sem_two_error_check')
});

semTwoErrorTaskSelect.addEventListener('change',()=>{
    //Ajax Request.js
    changeErrorTask(semTwoErrorSelect,semTwoErrorTaskSelect,-1,divSemTwoErrorChecks,errorChart2,"sem_two_error_check")
    semOneErrorProficiencySelect.selectedIndex=0;
    //-1 represents all proficiencies
});
semTwoErrorProficiencySelect.addEventListener('change',(e)=>{
    changeErrorTask(semTwoErrorSelect,semTwoErrorTaskSelect,e.target.value,divSemTwoErrorChecks,errorChart2,"sem_two_error_check")
        .then(()=>{
            semTwoAllErrorChecks.checked=true;
            semTwoAllErrorChecks.dispatchEvent(new CustomEvent("change"));
        });

});

semTwoErrorTaskSelect.dispatchEvent(new CustomEvent("change"));
*/
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

