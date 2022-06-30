
//GermanSteps - changed language flag order
let firstLoginTourStepsGerman = [
    {
        element: '#flag_en',
        title: 'Spracheinstellungen',
        content: 'Du kannst die Sprache zu Englisch wechseln',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'
    },
    {
        element: '#flag_de',
        title: 'Spracheinstellungen',
        content: 'Du kannst die Sprache auf Deutsch wechseln',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'
        //
    },
    {
        element: '.alert-warning',
        title: 'Eine neue Umfrage',
        content: 'Es gibt eine neue Umfrage',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'
    },
    {
        element: '#account_navBar',
        title: 'Accountübersicht',
        content: 'Dies führt zur Accountübersicht.<br> Hier können die Accountdaten sowie Statistiken eingesehen werden.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'

    },

    {
        element: '#account_information_table',
        title: 'Account Information',
        content: 'Sie können ihre Accountinformationen hier editieren. Beispielsweise Passwort ändern, E-Mail ändern, Gruppe ändern oder den Account löschen.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },
    {
        element: '#account_statistics_table',
        title: 'Account Statistiken',
        content: 'Hier können Sie einige Statistiken einsehen. Die Anzahl der Einreichungen, richtige Einreichungen, fehlerhafte Einreichungen und den prozentualen Anteil an korrekten Einreichungen.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },
    {
        element: '#accordion',
        title: 'Meine Einreichungen',
        content: 'Hier können Sie ihre Einreichungen ansehen.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },

    {
        element: '#tutorial_navBar',
        title: 'Tutorial',
        content: 'Es gibt eine Einführung für den SQLValidator',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_introduction'

    },
    {
        element: '#starttutorial' ,
        title: 'Starte Einführung',
        content: 'Starten sie die Einführung für den SQLValidator',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_introduction'
    }

];

//english steps
let firstLoginTourSteps = [
    {
        element: '#flag_de',
        title: 'Language Settings',
        content: 'You can change the language to german.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'
    },
    {
        element: '#flag_en',
        title: 'Language Settings',
        content: 'You can change the language to english.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'

    },
    {
        element: '.alert-warning',
        title: 'A new Questionnaire',
        content: 'There is a new questionnaire.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php'
    },
    {
        element: '#account_navBar',
        title: 'Account Overview',
        content: 'This leads to the account overview.<br> You can edit your account data and look for some statistics..',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'

    },
    {
      element: '#account_information_table',
      title: 'Account information',
      content: 'You can edit your account data. E.g. change password, change e-mail, change group, delete account.',
      placement: "bottom",
      path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },
    {
        element: '#account_statistics_table',
        title: 'Account statistics',
        content: 'You can see a few statistics here. Your amount of submissions,correct submissions, wrong submissions and your percentage value of correct submissions.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },
    {
        element: '#accordion',
        title: 'Your Submissions',
        content: 'You can see your submissions here.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=account'
    },
    {
        element: '#tutorial_navBar',
        title: 'Tutorial',
        content: 'Here is a tutorial for the SQLValidator.',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_introduction'

    },
    {
        element: '#starttutorial' ,
        title: 'Start Tutorial',
        content: 'Start the tutorial for the SQLValidator',
        placement: "bottom",
        path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_introduction'
    }

];

//Start Tour
function startTour(language) {
    let my_template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« previous</button>
        <span data-role='separator'>|</span>
        <button class='btn btn-default' data-role='next'> next »</button>
        <span data-role="separator">|</span>
    <button class='btn btn-default' data-role='end'> Done</button>
    </div>
  </div>`;
    if (language === 'de'){
        //german tutorial
        my_template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« zurück</button>
        <span data-role='separator'>|</span>
        <button class='btn btn-default' data-role='next'> weiter »</button>
        <span data-role="separator">|</span>
    <button class='btn btn-default' data-role='end'> Fertig</button>
    </div>
  </div>`;
        firstLoginTourSteps = firstLoginTourStepsGerman;
    }
    let firstLoginTour = new Tour({
        name: 'FirstLogin',
        showProgressBar: false,
        framework: "bootstrap3",
        backdrop: true,
        template: my_template,
        steps: firstLoginTourSteps,
    });

//make sure tutorial gets only started coming from register
    if (!firstLoginTour.ended() && (JSON.parse(sessionStorage.getItem("start_initial_tutorial")) === true)){
        firstLoginTour.start();
    }
}

//delete our "flag" when the tour has been ended
if (localStorage.getItem("FirstLogin_end") === "yes"){
    sessionStorage.removeItem("start_initial_tutorial");
}
