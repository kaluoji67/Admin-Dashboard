function pageChangeSemester(select)
{
    window.location.href = "index.php?action=dashboard/dash_index&currentSemester="+ select.value;
}
//Referesh date
function getLastFreshed()
{
    const currentdate = new Date();
    const datetime = currentdate.getDate() + "/"
        + (currentdate.getMonth()+1)  + "/"
        + currentdate.getFullYear() + " - "
        + currentdate.getHours() + ":"
        + currentdate.getMinutes() ;
        //+ ":"
        //+ currentdate.getSeconds();
    return datetime
}

const lastRefreshed = document.querySelector('.last_refreshed');
lastRefreshed.innerHTML = getLastFreshed();

//control tabs
const tabs = document.querySelectorAll('.tab[data-tab-target]')
const tabContents = document.querySelectorAll('.content[data-tab-content]')

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.tabTarget)
        tabContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })
        tabs.forEach(tab => {
            tab.classList.remove('active')
        })
        tab.classList.add('active')
        target.classList.add('active')
    })
})

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

