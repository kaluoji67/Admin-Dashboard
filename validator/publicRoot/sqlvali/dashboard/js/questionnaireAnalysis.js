//Questionnaire dropdown control
/*
const questTabs = document.querySelectorAll('.quest[data-tab-target]')
const questContents = document.querySelectorAll('.quest_content[data-tab-content]')

questTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.tabTarget)
        let active = target.classList.contains('active');
        questContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })

        questTabs.forEach(tab => {
            tab.classList.remove('active')
        })
        tab.classList.add('active')

        active?target.classList.remove('active'):target.classList.add('active');
    })
})

const taskNumTabs = document.querySelectorAll('.taskNum[data-tab-target]')
const taskNumContents = document.querySelectorAll('.taskNum_content[data-tab-content]')

taskNumTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.tabTarget)
        let active = target.classList.contains('active');

        let activeItem =target.querySelector('.active');
        taskNumContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        questItems.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        taskNumTabs.forEach(tab => {
            tab.classList.remove('active')
        })
        tab.classList.add('active')
        active?target.classList.remove('active'):target.classList.add('active');
        (activeItem == null)?activeItem.classList.add(''):activeItem.classList.add('active');
    })
})

const questItems = document.querySelectorAll('.item');

questItems.forEach(item => {
    item.addEventListener('click', (e) => {
        const id = item.getAttribute('itemId');
        const idArray = id.split(",");
        const q_id = idArray[0];
        const taskNum = idArray[1];
        const itemId = idArray[2];

        const semOne = semOneQuestSelect.value;
        const semTwo = semTwoQuestSelect.value;
        updateQuestChart(item,semOne,questChart1);
        updateQuestChart(item,semTwo,questChart2);

        questItems.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        taskNumContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        taskNumTabs.forEach(tab => {
            tab.classList.remove('active')
        })

        item.classList.add('active');
        item.parentElement.classList.add('active');
        item.parentElement.parentElement.classList.add('active');

        e.stopPropagation();
    })
});
*/
const semOneQuestSelect = document.querySelector('.quest_sem_one_sel');
const semOneQuestTaskSelect = document.querySelector('.quest_sem_one_task_sel');

semOneQuestTaskSelect.addEventListener('change',()=>{
    const id = semOneQuestTaskSelect.value;
    const idArray = id.split(",");
    const q_id = idArray[0];
    const taskNum = idArray[1];
    const itemId = idArray[2];


    const semester= semOneQuestSelect.value;
    const lang= semOneQuestSelect.lang;

    updateQuestChart(q_id,taskNum,itemId,semester,lang,questChart1,questChartCanvas1,questChartDataset1);
});
semOneQuestSelect.addEventListener('change', ()=>{
    changeQuestionnaireSem(semOneQuestSelect,semOneQuestTaskSelect);
});
// trigger event to lead questionnaire one chart
semOneQuestTaskSelect.dispatchEvent(new CustomEvent("change"));

const semTwoQuestSelect = document.querySelector('.quest_sem_two_sel');
const semTwoQuestTaskSelect = document.querySelector('.quest_sem_two_task_sel');

semTwoQuestSelect.addEventListener('change',async ()=>{
    await changeQuestionnaireSem(semTwoQuestSelect,semTwoQuestTaskSelect);
});

semTwoQuestTaskSelect.addEventListener('change',(e)=>{
    const id = semTwoQuestTaskSelect.value;
    const idArray = id.split(",");
    const q_id = idArray[0];
    const taskNum = idArray[1];
    const itemId = idArray[2];

    const semester= semTwoQuestSelect.value;
    const lang= semTwoQuestSelect.lang;

    updateQuestChart(q_id,taskNum,itemId,semester,lang,questChart2,questChartCanvas2,questChartDataset2);
});
// Dispatch/Trigger/Fire the event
semTwoQuestTaskSelect.dispatchEvent(new CustomEvent("change"));

const chartSwapButtons = document.querySelectorAll('.swap_chart')
chartSwapButtons.forEach(btt=>{
    const type = btt.getAttribute('type');
    const order = btt.getAttribute('chart');
    const chart = order=="1"?questChart1:questChart2;
    const chartCanvas = order=="1"?questChartCanvas1:questChartCanvas2;
    const chartDataset = order=="1"?questChartDataset1:questChartDataset2;
    btt.addEventListener('click',()=>{
        updateChartFromType(type,chart,chartCanvas,chartDataset)
    })
});

function updateChartFromType(type,chart,chartCanvas,chartDataset)
{
    switch(type) {
        case "bar":
            changeToBar(chart,chartCanvas,chartDataset);
            break;
        case "boxplot":
            changeToBoxPlot(chart,chartCanvas,chartDataset);
            break;
        case "radar":
            changeToRadar(chart,chartCanvas,chartDataset);
            break;
        case "line":
            changeToLine(chart,chartCanvas,chartDataset);
            break;
    }
}

function changeToBar(chart,chartCanvas,chartDataset) {
     //build legend
    let legend = new Array(chartDataset["answers"].length);
    for(let i=0; i<legend.length;i++){
        legend[i] = i;
    }

    if(chartDataset.extremes.length >= chartDataset["answers"].length){
        for(let i=0;i<chartDataset["extremes"].length;i++){
            chartDataset["extremes"][i]= chartDataset["extremes"][i] +" : " + i;
        }
        legend =chartDataset["extremes"];
    }else{
        legend[0] = chartDataset["extremes"][0] +" : " + 0;
        legend[legend.length-1] = chartDataset["extremes"][chartDataset["extremes"].length-1]+" : " + (legend.length-1);
    }

    //populate chart
    let titles = new Array();
    for(const INum in chartDataset["titles"]){
        titles.push(chartDataset["titles"][INum].split(" ")[0])
    }

    let datasets= Array();
    for(const ans in chartDataset["answers"])
    {
        let it = {
            label: legend[parseInt(ans)],
            backgroundColor : colors[parseInt(ans)],
            data : chartDataset["answers"][ans]
        }
        datasets.push(it);
    }
    /////////////////////////////////
    if (chart) {
        chart.destroy();
    }
    const data = {
        labels: titles,
        datasets: datasets
    };
    const config = {
        type: 'bar',
        data: data,
        options: {
            indexAxis : 'y',
            legend: { display: true },
            plugins:{
                title: {
                    display: true,
                    text: 'Questionnaire Response'
                }
            },
            maintainAspectRatio: false,
            scales: {
                yAxes: {
                    stacked: true,
                    ticks: {
                        beginAtZero: true
                    }
                },
                xAxes: {
                    stacked: true,
                    scaleFontSize: 10,
                    display: true,
                    title: {
                        display: true,
                        text: 'Responses'
                    }
                }
            }
        }
    };
    let myChart= new Chart(chartCanvas,config);

    chart.length=0;
    Object.assign(chart, myChart);
}
function changeToBoxPlot(chart,chartCanvas,chartDataset) {
    const label = new Array();
    const boxplotDataset = new Array();
    //Object.assign(boxplotDataset, chartDataset);
    let index =0;
    for(const item in chartDataset["titles"]){
        label.push(chartDataset["titles"][item].split(" ")[0]);
        let answerData = new Array();
        for(const ans in chartDataset["answers"]){
            answerData.push(chartDataset["answers"][ans][index]);
        }
        //answerData array has been filled with the amount of the different responses
        boxplotDataset.push(answerData);
        index++;
    }
    /////////////////////////////////
    if (chart) {
        chart.destroy();
    }
    const data = {
        labels: label,
        datasets: [{
            label: 'Responses to different options',
            backgroundColor: colors,
            borderColor: colors,
            data: boxplotDataset,
        }]
    };
    const config = {
        type: 'boxplot',
        data: data,
        options: {maintainAspectRatio: false}
    };

    const myChart = new Chart(chartCanvas,
        config
    );
    chart.length=0;
    Object.assign(chart, myChart);
}

function changeToRadar(chart,chartCanvas,chartDataset) {
    let legend = new Array(chartDataset["answers"].length);
    for(let i=0; i<legend.length;i++){
        legend[i] = i;
    }

    if(chartDataset.extremes.length >= chartDataset["answers"].length){
        for(let i=0;i<chartDataset["extremes"].length;i++){
            chartDataset["extremes"][i]= chartDataset["extremes"][i] +" : " + i;
        }
        legend =chartDataset["extremes"];
    }else{
        legend[0] = chartDataset["extremes"][0] +" : " + 0;
        legend[legend.length-1] = chartDataset["extremes"][chartDataset["extremes"].length-1]+" : " + (legend.length-1);
    }

    const radarDataset = new Array();
    let index =0;
    for(const item in chartDataset["titles"]){
        const label = chartDataset["titles"][item].split(" ")[0];
        let answerData = new Array();
        for(const ans in chartDataset["answers"]){
            answerData.push(chartDataset["answers"][ans][index]);
        }
        //answerData array has been filled with the amount of the different responses
        const datasets= {
            label: label,
            borderColor: colors,
            data: answerData,
            fill: true,
            backgroundColor: transparentColors[index],
            borderColor: colors[index],
            pointBackgroundColor: 'rgb(255, 99, 132)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(255, 99, 132)'
        }
        radarDataset.push(datasets);
        index++;
    }

    /////////////////////////////////
    if (chart) {
        chart.destroy();
    }
    const data = {
        labels: legend,
        datasets: radarDataset
    };
    const config = {
        type: 'radar',
        data: data,
        options: {
            maintainAspectRatio: false,
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            plugins: {
                legend: { display: true, position:'right',
                    fontsize:3
                }
            },
        }
    };
    const myChart = new Chart(chartCanvas,
        config
    );
    chart.length=0;
    Object.assign(chart, myChart);
}

function changeToLine(chart,chartCanvas,chartDataset) {

}


