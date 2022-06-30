//require include include_once
//our expected Queries
let expectedQuerySyntaxError = "CREATE TABLE LECTURES (ID date PRIMARY KEY, name varchar(255))";
let expectedQuerySyntaxFixed = "CREATE TABLE LECTURES (ID int PRIMARY KEY, name varchar(255));";
let expectedQuerySyntaxError2 = "selet * from employee";
let expectedQuerySchemaError = "select * from employee;";
let expectedQuerySchemaFixed = "select * from employee where age >= '35';";
let expectedQuerySchemaError2 = "select * from employee where age >= '35';";
let expectedQuerySchemaFixed2 = "select name,age from employee where age >= '35';";

//Error Message for alert-warning box!
const SchemaErrorToken = "";
//Flag if we already did the Error
let syntaxError = false;
let schemaError = false;

//IntroductionTutorial Texts
let tutorialInEnglish = {
    'title':
        ['Welcome to the SQL-Validator Tutorial', 'Example Exercise Sheet', 'First Exercise Sheet', 'First Task', 'Doing your first Task'],
    'content': ['Here you see an overview of all available exercises.', 'This is an example exercise sheet.', 'This is the first exercise sheet and a preview of the tasks.', "This is the first task of this exercise sheet.", "Click this link to try and solve the exercise."]
}
let tutorialInGerman = {
    'title': ['Willkommen zum SQL-Validator Tutorial', 'Beispielaufgabenblatt', 'Erstes Aufgabenblatt', 'Erste Aufgabe', 'Bearbeite die erste Aufgabe'],
    'content': ['Hier sehen sie eine Übersicht über alle verfügbaren Aufgaben.', 'Dies ist ein Beispielaufgabenblatt.', 'Dies ist das erste Aufgabenblatt und eine Vorschau der Aufgaben.', 'Dies ist die erste Aufgabe dieses Aufgabenblattes.', 'Klicken Sie auf diesen Link und versuchen Sie die Aufgabe zu lösen.']
}
//Syntax Tour Texts
let mySyntaxTourInEnglish = {
    'title': ['The first Task', 'Exercise Text', 'Your Result', 'SQL-Query', 'Send your SQL Query', 'Response', 'Fix the syntax error', 'Example Query', 'SQL-Query', 'Send your SQL Query', 'Response', 'Go to previous Task', 'Go to next Task'],
    'content': ["This is the task.", "That is the exercise text. We will encounter a syntax error in this task and fix it later on.", 'Put your SQL-Query in the editor below.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here: <br><b>${expectedQuerySyntaxError}</b><br> or use the autofill button.</div>`, "<b>Send your query (click this button or just next). </b>", 'This is the response if you made syntax errors. Syntax Errors can have the following reasons: <ul><li>Missing semicolon ;</li><li>Missing paranthesis ()</li><li>Typo in SQL-Keyword</li><li>Wrong DataType</li><li>Copying of control characters </li></ul>', 'Lets fix the syntax error.', 'Example Query has changed.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here:<br><b>${expectedQuerySyntaxFixed}</b><br>  or use the autofill button.</div>`, "<b>Send your query (click next).</b>", 'This is the response if you made no errors.', 'You can select the previous task with this button.', '<b>Or you can select the next task with this button (click this button). </b>']
};
let mySyntaxTourInGerman = {
    'title': ['Die erste Aufgabe', 'Aufgabentext', 'Dein Ergebnis', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', 'Syntaxfehler korrigieren', 'Beispielquery', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', "Zur vorherigen Aufgabe", "Zur nächsten Aufgabe"],
    'content': ["Das ist die Aufgabe.", "Dies ist der Aufgabentext. In dieser Aufgabe werden wir einen Syntaxfehler erzeugen und diesen später korrigieren.", 'Schreiben Sie ihre SQL-Abfrage in den Editor unterhalb.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySyntaxError}</b><br> oder nutzen Sie den Autofill-Button</div>`, "<b>Senden Sie ihre SQL-Abfrage (klicken Sie auf weiter). </b>", 'Dies ist die Antwort des SQL-Validators wenn Sie einen Syntaxfehler gemacht haben. Ein Syntaxfehler kann folgende Ursachen haben: <ul></ul><li>Fehlendes Semikolon ;</li><li>Fehlende Klammer ()</li><li>Tippfehler eines Schlüsselwortes</li><li>Falscher Datentyp</li><li>Kopieren von Steuerungszeichen</li></ul>', 'Lassen Sie uns den Syntaxfehler korrigieren.', 'Die Beispielquery hat sich geändert.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein:<br><b>${expectedQuerySyntaxFixed}</b><br> oder nutzen Sie den Autofill-Button.</div>`, "<b>Senden Sie ihre SQL-Abfrage (klicken Sie auf weiter). </b>", 'Das ist die Antwort, wenn sie keinen Fehler gemacht haben.', 'Mit diesem Button können Sie zur vorherigen Aufgabe wechseln.', '<b>Oder Sie können die nächste Aufgabe mit diesem Button auswählen (klicken sie auf den Button). </b>']
};

//Syntax+SchemaTourTexts
let mySchemaTourInEnglish = {
    'title': ['The second Task', 'Exercise Text', 'Your Result', 'SQL-Query', 'Send your SQL Query', 'Response', 'Fix Syntax Error', 'Example Query', 'SQL-Query', 'Send your SQL Query', 'Response', 'Solution', 'Your solution', 'Disparity', 'Row', 'Row', 'Fix Error', 'Example query', 'SQL-Query', 'Send your SQL query', 'Response', 'Your Solution', 'Similar', 'Go to previous Task', 'Go to next Task'],
    'content': ['This is the second task. Here we will encounter another possible error type.', 'That is the exercise text.', 'Put your SQL Query in the editor below.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here: <br><b>${expectedQuerySyntaxError2}</b><br> or use the autofill button.</div>`, `<b>Send your Query (click next).</b>`, 'This alertbox shows you, that you made a syntax error in your query. In this case we made a typo in the Word <i>SELECT</i> and missed semicolon.', 'Lets fix the Syntax Error.', 'Example Query has changed.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here: <br><b>${expectedQuerySchemaError}</b><br> or use the autofill button.</div>`, `<b>Send your Query (click this button or just next).</b>`, 'This alertbox shows you a hint what kind of schema related error you have done. In this case our result has more rows than it should. Lets have a look.', 'What the solution should look like', 'What your solution looks like', 'Look at the Disparity of these two tables.', 'This row should not be shown in your result.', 'This row should not be shown in your result.', 'Lets fix the schema related error now', 'Example Query has changed', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here: <b>${expectedQuerySchemaFixed}</b> or use the autofill button.</div>`, `<b>Send your query(click this button or just next) </b>`, 'This is the response if you made no errors.', 'What your solution looks like', 'Look at the similarity of these two tables', 'You can select the previous task with this button', '<b>Or you can select the next task with this button (click this button) </b>']
};
let mySchemaTourInGerman = {
    'title': ['Die zweite Aufgabe', 'Aufgabentext', 'Dein Ergebnis', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', 'Syntaxfehler korrigieren', 'Beispielquery', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', 'Lösung', 'Ihre Lösung', 'Unterschiede', 'Zeile', 'Zeile', 'Fehler korrigieren', 'Beispielquery', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', 'Ihre Lösung', 'Identisch', 'Zur vorherigen Aufgabe', 'Zur nächsten Aufgabe'],
    'content': ['Dies ist die zweite Aufgabe. Hier werden wir einem weiteren Fehlertyp begegnen.', 'Dies ist der Aufgabentext.', 'Schreiben Sie ihre SQL-Abfrage in den Editor unterhalb.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySyntaxError2}</b><br> oder nutzen Sie den Autofill-Button.</div>`, '<b> Senden Sie ihre Abfrage (klicken Sie auf weiter).</b>', 'Diese Warnbox zeigt ihnen, dass Sie einen Syntaxfehler in Ihrer Abfrage gemacht haben. <br> In diesem Falle haben wir einen Tippfehler im Schlüsselwort <i>SELECT</i> gemacht und ein Semikolon vergessen.', 'Lassen Sie uns den Syntaxfehler korrigieren.', 'Die Beispielquery hat sich geändert.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySchemaError}</b><br> oder nutzen Sie den Autofill-Button.</div>`, '<b> Senden Sie ihre Abfrage (klicken Sie auf weiter).</b>', 'Diese Hinweisbox zeigt Ihnen einen Hinweis welchen schemabezogenen Fehler Sie gemacht haben. In diesem Falle hat unser Ergebnis mehr Zeilen als gefordert. Lassen Sie uns einen Blick darauf werfen.', 'Wie die Lösung aussehen sollte.', 'Wie Ihre Lösung aussieht.', 'Sehen Sie sich den Unterschied der beiden Tabellen an.', 'Diese Zeile sollte nicht im Ergebnis enthalten sein.', 'Diese Zeile sollte nicht im Ergebnis enthalten sein.', 'Lassen Sie uns den schemabezogenen Fehler jetzt korrigieren.', 'Die Beispielquery hat sich geändert.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySchemaFixed}</b><br> oder nutzen Sie den Autofill-Button.</div>`, '<b> Senden Sie ihre Abfrage (klicken Sie auf weiter).</b>', 'Das ist die Antwort wenn Sie keinen Fehler gemacht haben.', 'Wie Ihre Lösung aussieht.', 'Schauen Sie sich die Gleichartigkeit beider Tabellen an.', 'Mit diesem Button können Sie zur vorherigen Aufgabe wechseln.', '<b>Oder Sie können die nächste Aufgabe mit diesem Button auswählen (klicken Sie auf den Button). </b>']
};

//Task #3 SchemaTourText
let mySchemaTourTwoInEnglish = {'title': ['The third Task','Exercise Text','Your Result','SQL-Query','Send your SQL Query','Response','Solution', 'Your solution', 'Disparity', 'Column', 'Column', 'Fix Error','Example query', 'SQL-Query', 'Send your SQL query', 'Response', 'Your solution', 'Similar' ],
    'content': ['This is the task. You will encounter another common schema related error.', 'That is the exercise text.', 'Put your SQL query in the editor below.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here: <br><b>${expectedQuerySchemaError2}</b><br> or use the autofill button.</div>`, "<b>Send your query (click this button or just next). </b>", 'This alertbox shows you a hint what kind of schema related error you have done. In this case your solution has more columns than it should. Lets have a look. ', 'What the solution should look like.', 'What your solution looks like.', 'Look at the disparity of these two tables.', 'This column should not be shown in your result.', 'This column should not be shown in your result.', 'Lets fix the schema related error now.', 'Example query has changed.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Put your solution in here:<br><b>${expectedQuerySchemaFixed2}</b><br> or use the autofill button.</div>`, '<b>Send your query (click this button or just next)</b>', 'This is the response if you made no errors.', 'What your solution looks like.', 'Look at the similarity of these two tables. You finished the tutorial successfully, congratulation!']};
let mySchemaTourTwoInGerman = {'title': ['Die dritte Aufgabe','Aufgabentext', 'Dein Ergebnis','SQL-Abfrage','Senden Sie die SQL-Abfrage','Antwort','Lösung', 'Ihre Lösung','Unterschiede', 'Spalte', 'Spalte','Fehler korrigieren', 'Beispielquery', 'SQL-Abfrage', 'Senden Sie die SQL-Abfrage', 'Antwort', 'Ihre Lösung', 'Identisch'],
    'content': ['Dies ist die Aufgabe. Hier werden Sie einen weiteren häufigen schemabezogenen Fehler sehen.', 'Dies ist der Aufgabentext.','Schreiben Sie ihre SQL-Abfrage in den Editor unterhalb.', `<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySchemaError2}</b><br> oder nutzen Sie den Autofill-Button.</div>`, '<b>Senden Sie ihre SQL-Abfrage (klicken sie auf weiter).</b>','Diese Warnbox zeigt ihnen, dass Sie einen schemabezogenen Fehler gemacht haben. In diesem Falle hat ihr Ergebnis mehr Spalten als gefordert.','Wie die Lösung aussehen sollte.', 'Wie Ihre Lösung aussieht.', 'Sehen Sie sich den Unterschied der beiden Tabellen an.', 'Diese Spalte sollte nicht im Ergebnis enthalten sein.', 'Diese Spalte sollte nicht im Ergebnis enthalten sein.', 'Lassen Sie uns den schemabezogenen Fehler jetzt korrigieren.', 'Die Beispielquery hat sich geändert.',`<div class="img-div"><img src="/testViktor/test_tutor/includesVali/tpl/introduction/warning-sign.png" width="50", height="50">Fügen Sie ihre Lösung hier ein: <br><b>${expectedQuerySchemaFixed2}</b><br> oder nutzen Sie den Autofill-Button.</div>`, '<b> Senden Sie ihre Abfrage (klicken Sie auf weiter)</b>', 'Das ist die Antwort wenn Sie keinen Fehler gemacht haben.', 'Wie Ihre Lösung aussieht.', 'Schauen Sie sich die Gleichartigkeit beider Tabellen an. Sie haben das Tutorial erfolgreich abgeschlossen, gratulation.']};



//IntroductionTutorial
function startIntroductionTutorial(language = en) {
    let template = template_buttons();

    //if German language translate tour
    if (language === 'de') {
        template = template_buttons('de');
        tutorialInEnglish = tutorialInGerman;
    }

    //TourSteps as JSON
    let tourExerciseSteps = [
        {
            element: '.main-container',
            title: tutorialInEnglish['title'][0],
            content: tutorialInEnglish['content'][0],
            placement: "bottom",

        },
        {
            element: '#bootstraptourexercise1',
            title: tutorialInEnglish['title'][1],
            content: tutorialInEnglish['content'][1],
            placement: "bottom",
            onShown: _ => {
                //simulating a click
                if (!(document.getElementById('clickanchor') == null)) {
                    document.getElementById('clickanchor').click();
                }
            }
        },
        {
            element: '#ulid',
            title: tutorialInEnglish['title'][2],
            content: tutorialInEnglish['content'][2],
            placement: "bottom",
        },
        {
            element: '#exercise1a',
            title: tutorialInEnglish['title'][3],
            content: tutorialInEnglish['content'][3],
            placement: "bottom",
        },
        {
            element: '#exercise1aanchor',
            title: tutorialInEnglish['title'][4],
            content: tutorialInEnglish['content'][4],
            onNext: _ => {
                //simulating a click
                if (!(document.getElementById('exercise1aanchor') == null)) {
                    document.getElementById('exercise1aanchor').click();
                }
            }
        },

    ];


    //build the IntroductionTour
    let tourExercise = new Tour({
        name: 'ExerciseTutorial',
        showProgressBar: false,
        framework: "bootstrap3",
        backdrop: true,
        keyboard: false,
        template: template,
        steps: tourExerciseSteps,
    })
    tourExercise.start();
    tourExercise.restart();
}

//tutorial starting logic for all 3 exercises
function startTutorial(id, language = en) {
    //define our custom template
    let template = template_buttons();
    //translate template buttons if language is german
    if (language === 'de'){
        template = template_buttons('de');
    }
    //syntax exercise
    if (id === 1) {
        //autofillfunction
        let autofill_template = template_autofill('en', 1);
        //translate Tour Texts + Autofill Template
        if (language === 'de'){
            autofill_template = template_autofill('de', 1);
            mySyntaxTourInEnglish = mySyntaxTourInGerman;
        }
        //define our Steps for the syntaxtour
        let tourSyntaxSteps = [
            {
                element: '#solvingsheet',
                title: mySyntaxTourInEnglish['title'][0],
                content: mySyntaxTourInEnglish['content'][0],
                placement: 'bottom',
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },
            // see task exercise text
            {
                element: '.exercise',
                title: mySyntaxTourInEnglish['title'][1],
                content: mySyntaxTourInEnglish['content'][1],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
                onShow: function () {
                    fill_example_query(1)
                }
            },
            // see editor frame
            {
                element: '#result',
                title: mySyntaxTourInEnglish['title'][2],
                content: mySyntaxTourInEnglish['content'][2],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',

            },
            // input query in editor
            {
                element: '#myform',
                title: mySyntaxTourInEnglish['title'][3],
                content: mySyntaxTourInEnglish['content'][3],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },

            // send solution
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySyntaxTourInEnglish['title'][4],
                content: mySyntaxTourInEnglish['content'][4],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (!document.getElementById('danger').classList.contains('hide')) {
                        tourSyntax.goTo(4);
                    }
                    if (document.getElementById('danger').classList.contains('hide')) {
                        tourSyntax.goTo(2);
                    }
                }
            },

            // show danger
            {
                element: '.alert-danger',
                title: mySyntaxTourInEnglish['title'][5],
                content: mySyntaxTourInEnglish['content'][5],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
                onNext: function () {
                    syntaxError = true;
                }

            },
            {
                element: '.alert-danger',
                title: mySyntaxTourInEnglish['title'][6],
                content: mySyntaxTourInEnglish['content'][6],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },
            // show example query has changed
            {
                element: '#predefinedQuery',
                title: mySyntaxTourInEnglish['title'][7],
                content: mySyntaxTourInEnglish['content'][7],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
                onShow: function () {
                    console.log('fill example query syntax error fixed');
                    fill_example_query(2);
                },
            },


            // show editor
            {
                element: '#myform',
                title: mySyntaxTourInEnglish['title'][8],
                content: mySyntaxTourInEnglish['content'][8],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',

            },

            // solutionbutton die 2.
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySyntaxTourInEnglish['title'][9],
                content: mySyntaxTourInEnglish['content'][9],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
                //testViktor/test_tutor/
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (document.getElementById('success').classList.contains('hide')) {
                        tourSyntax.goTo(7);
                    }
                }
            },

            // alert succes syntax error fixed
            {
                element: '.alert-success',
                title: mySyntaxTourInEnglish['title'][10],
                content: mySyntaxTourInEnglish['content'][10],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },


            //predecessor task
            {
                element: '#predecessorbutton',
                title: mySyntaxTourInEnglish['title'][11],
                content: mySyntaxTourInEnglish['content'][11],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },
            // successor task
            {
                element: '#successorbutton',
                title: mySyntaxTourInEnglish['title'][12],
                content: mySyntaxTourInEnglish['content'][12],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=1',
            },
        ];
        //Building our Syntax tour finally
        let tourSyntax = new Tour({
            name: 'Syntax_Tutorial',
            showProgressBar: false,
            //framework: "bootstrap3",
            backdrop: true,
            keyboard: false,
            template: template,
            steps: tourSyntaxSteps
        });
        tourSyntax.start();
        //restart questionable
        tourSyntax.restart();
    }
    //syntax+schema exercise
    if (id === 2){
        let autofill_template = template_autofill('en', 2);
        if (language === 'de') {
            autofill_template = template_autofill('de', 2);
            mySchemaTourInEnglish = mySchemaTourInGerman;

        }
        //define our steps for syntax+schemaTour
        let tourSchemaErrorSteps = [

            {
                element: '#solvingsheet',
                title: mySchemaTourInEnglish['title'][0],
                content: mySchemaTourInEnglish['content'][0],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onNext: function () {
                    fill_example_query(3)
                }
            },
            // see task exercise text
            {
                element: '.exercise',
                title: mySchemaTourInEnglish['title'][1],
                content: mySchemaTourInEnglish['content'][1],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',

            },
            // see editor frame
            {
                element: '#result',
                title: mySchemaTourInEnglish['title'][2],
                content: mySchemaTourInEnglish['content'][2],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },

            // input query in editor
            {
                element: '#myform',
                title: mySchemaTourInEnglish['title'][3],
                content: mySchemaTourInEnglish['content'][3],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',


            },
            // send solution
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySchemaTourInEnglish['title'][4],
                content: mySchemaTourInEnglish['content'][4],
                placement: 'bottom',
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (!(document.getElementById('danger').classList.contains('hide'))) {
                        tourSchema.goTo(4);
                    } else {
                        tourSchema.goTo(2);
                    }
                },
            },

            // show syntax error box and tables
            {
                element: '.alert-danger',
                title: mySchemaTourInEnglish['title'][5],
                content: mySchemaTourInEnglish['content'][5],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onShow: function () {
                    syntaxError = true;
                },
            },

            {
                element: '.alert-danger',
                title: mySchemaTourInEnglish['title'][6],
                content: mySchemaTourInEnglish['content'][6],
                placement: 'bottom',
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },

            //show example query has changed
            {
                element: '#predefinedQuery',
                title: mySchemaTourInEnglish['title'][7],
                content: mySchemaTourInEnglish['content'][7],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onShow: function () {
                    fill_example_query(4)
                },
            },

            // input query in editor
            {
                element: '#myform',
                title: mySchemaTourInEnglish['title'][8],
                content: mySchemaTourInEnglish['content'][8],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',

            },

            // send solution
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySchemaTourInEnglish['title'][9],
                content: mySchemaTourInEnglish['content'][9],
                placement: 'bottom',
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (!(document.getElementById('warning').classList.contains('hide'))) {
                        tourSchema.goTo(9);
                    } else {
                        tourSchema.goTo(7);
                    }
                },
            },

            // show schema error box and tables
            {
                element: '#warning',
                title: mySchemaTourInEnglish['title'][10],
                content: mySchemaTourInEnglish['content'][10],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onShow: function () {
                    document.getElementById('resultdiv').classList.remove('hide');
                    document.getElementById('yourresultdiv').classList.remove('hide');
                    schemaError = true;
                },
            },
            // show table 1
            {
                element: '#resultdiv',
                title: mySchemaTourInEnglish['title'][11],
                content: mySchemaTourInEnglish['content'][11],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },
            // show table 2
            {
                element: '#yourresultdiv',
                title: mySchemaTourInEnglish['title'][12],
                content: mySchemaTourInEnglish['content'][12],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },
            // show both tables and disparity
            {
                element: '#wrapresultdiv',
                title: mySchemaTourInEnglish['title'][13],
                content: mySchemaTourInEnglish['content'][13],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },
            {
                element: '#age33',
                title: mySchemaTourInEnglish['title'][14],
                content: mySchemaTourInEnglish['content'][14],
                placement: "top",

            },
            {
                element: '#age23',
                title: mySchemaTourInEnglish['title'][15],
                content: mySchemaTourInEnglish['content'][15],
                placement: "top",
            },
            {
                element: '.alert-warning',
                title: mySchemaTourInEnglish['title'][16],
                content: mySchemaTourInEnglish['content'][16],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },

        ];
        let tourSchemaFixedSteps = [//show example query has changed
            {
                element: '#predefinedQuery',
                title: mySchemaTourInEnglish['title'][17],
                content: mySchemaTourInEnglish['content'][17],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onShow: function () {
                    fill_example_query(6)
                },

            },


            // input query in editor
            {
                element: '#myform',
                title: mySchemaTourInEnglish['title'][18],
                content: mySchemaTourInEnglish['content'][18],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',

            },
            // send solution
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySchemaTourInEnglish['title'][19],
                content: mySchemaTourInEnglish['content'][19],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (!(document.getElementById('success').classList.contains('hide'))) {
                        tourSchema.goTo(19);
                    } else {
                        tourSchema.goTo(17);
                    }
                },
            },

            // show resultmessage
            {
                element: '.alert-success',
                title: mySchemaTourInEnglish['title'][20],
                content: mySchemaTourInEnglish['content'][20],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
                onNext: function () {
                    document.getElementById('resultdiv').classList.remove('hide');
                    document.getElementById('yourresultdiv').classList.remove('hide');
                    document.getElementById('age23').classList.add('hide');
                    document.getElementById('age33').classList.add('hide');
                    document.getElementById('resultspan').classList.remove('glyphicon-remove');
                    document.getElementById('resultspan').classList.add('glyphicon-ok');
                },
            },
            //show your result table
            {
                element: '#yourresultdiv',
                title: mySchemaTourInEnglish['title'][21],
                content: mySchemaTourInEnglish['content'][21],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',

            },
            // show both tables
            {
                element: '#wrapresultdiv',
                title: mySchemaTourInEnglish['title'][22],
                content: mySchemaTourInEnglish['content'][22],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            },
            //predecessor task
            {
                element: '#predecessorbutton',
                title: mySchemaTourInEnglish['title'][23],
                content: mySchemaTourInEnglish['content'][23],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',


            },
            // successor task
            {
                element: '#successorbutton',
                title: mySchemaTourInEnglish['title'][24],
                content: mySchemaTourInEnglish['content'][24],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=2',
            }
        ];
        // concat our stepsbuilding our SchemaTour
        let mysteps2 = tourSchemaErrorSteps
        mysteps2 = mysteps2.concat(tourSchemaFixedSteps);
        //building all together for syntax+schema task
        let tourSchema = new Tour({
            name: 'Schema_Tutorial',
            showProgressBar: false,
            framework: "bootstrap3",
            backdrop: true,
            keyboard: false,
            debug: true,
            template: template,
            steps: mysteps2,
        });
        tourSchema.start();
        //restart questionable
        tourSchema.restart();
    }

    // 3rd task
    if (id === 3)
    {
        let autofill_template = template_autofill('en', 3);
        if (language === 'de'){
            autofill_template = template_autofill('de', 3);
            mySchemaTourTwoInEnglish = mySchemaTourTwoInGerman;
        }
        //define our steps for schemaTour
        let tourSchema2Steps = [
            {
                element: '#solvingsheet',
                title: mySchemaTourTwoInEnglish['title'][0],
                content: mySchemaTourTwoInEnglish['content'][0],
                placement: 'bottom',
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },
            // see task exercise text
            {
                element: '.exercise',
                title: mySchemaTourTwoInEnglish['title'][1],
                content: mySchemaTourTwoInEnglish['content'][1],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onShow: function () {
                    fill_example_query(7);
                }
            },
            // see editor frame
            {
                element: '#result',
                title: mySchemaTourTwoInEnglish['title'][2],
                content: mySchemaTourTwoInEnglish['content'][2],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',

            },
            // input query in editor
            {
                element: '#myform',
                title: mySchemaTourTwoInEnglish['title'][3],
                content: mySchemaTourTwoInEnglish['content'][3],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

            // send solution
            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySchemaTourTwoInEnglish['title'][4],
                content: mySchemaTourTwoInEnglish['content'][4],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (!document.getElementById('warningSchema2').classList.contains('hide')) {
                        document.getElementById('resultdiv2').classList.remove('hide');
                        document.getElementById('yourresultdiv2').classList.remove('hide');
                        tour2Schema.goTo(4);
                    }
                    if (document.getElementById('warningSchema2').classList.contains('hide')) {
                        tour2Schema.goTo(2);
                    }
                }
            },

            // show warning
            {
                element: '#warningSchema2',
                title: mySchemaTourTwoInEnglish['title'][5],
                content: mySchemaTourTwoInEnglish['content'][5],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onNext: function () {
                   schemaError = true;
                }

            },
            //show correct solution
            {
                element: '#resultdiv2',
                title: mySchemaTourTwoInEnglish['title'][6],
                content: mySchemaTourTwoInEnglish['content'][6],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },
            //show your solution
            {
                element: '#yourresultdiv2',
                title: mySchemaTourTwoInEnglish['title'][7],
                content: mySchemaTourTwoInEnglish['content'][7],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

            {
                element: '#wrapresultdiv2',
                title: mySchemaTourTwoInEnglish['title'][8],
                content: mySchemaTourTwoInEnglish['content'][8],
                placement: "top",
                path:'/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

            {
                element: '#wrongcolumn',
                title: mySchemaTourTwoInEnglish['title'][9],
                content: mySchemaTourTwoInEnglish['content'][9],
                placement: "top",
                path:'/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

            {
                element: '#wrongcolumn2',
                title: mySchemaTourTwoInEnglish['title'][10],
                content: mySchemaTourTwoInEnglish['content'][10],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3'
            },



            //following steps
            {
                element: '#warningSchema2',
                title: mySchemaTourTwoInEnglish['title'][11],
                content: mySchemaTourTwoInEnglish['content'][11],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },
            // show example query has changed
            {
                element: '#predefinedQuery',
                title: mySchemaTourTwoInEnglish['title'][12],
                content: mySchemaTourTwoInEnglish['content'][12],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onShow: function () {
                    fill_example_query(8);
                }
            },


            // show editor
            {
                element: '#myform',
                title: mySchemaTourTwoInEnglish['title'][13],
                content: mySchemaTourTwoInEnglish['content'][13],
                placement: "bottom",
                template: autofill_template,
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },


            {
                element: '#solutionbutton',
                reflexOnly: true,
                title: mySchemaTourTwoInEnglish['title'][14],
                content: mySchemaTourTwoInEnglish['content'][14],
                placement: "bottom",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onNext: _ => {
                    //simulating a click
                    if (!(document.getElementById('solutionbutton') == null)) {
                        document.getElementById('solutionbutton').click();
                    }
                    if (document.getElementById('success').classList.contains('hide')) {
                        tour2Schema.goTo(12);
                    }
                }
            },

            // alert success
            {
                element: '.alert-success',
                title: mySchemaTourTwoInEnglish['title'][15],
                content: mySchemaTourTwoInEnglish['content'][15],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
                onNext: function () {
                    document.getElementById('resultdiv2').classList.remove('hide');
                    document.getElementById('yourresultdiv2').classList.remove('hide');
                    for (let i = 0; i < document.querySelectorAll('.wrongcolumn-hide').length; i++){
                        document.querySelectorAll('.wrongcolumn-hide')[i].classList.add('hide');
                    }
                    document.getElementById('resultspan').classList.remove('glyphicon-remove');
                    document.getElementById('resultspan').classList.add('glyphicon-ok');
                },
            },

            {
                element: '#yourresultdiv2',
                title: mySchemaTourTwoInEnglish['title'][16],
                content: mySchemaTourTwoInEnglish['content'][16],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

            {
                element: '#wrapresultdiv2',
                title: mySchemaTourTwoInEnglish['title'][17],
                content: mySchemaTourTwoInEnglish['content'][17],
                placement: "top",
                path: '/testViktor/test_tutor/publicRoot/sqlvali/index.php?action=introduction/tutorial_exercise_page&id=3',
            },

        ];
        //build our Schema2Tour
        let tour2Schema = new Tour({
            name: 'Schema_Tutorial2',
            showProgressBar: false,
            framework: "bootstrap3",
            backdrop: true,
            keyboard: false,
            debug: true,
            template: template,
            steps: tourSchema2Steps,
        });
        tour2Schema.start();
        //restart questionable
        tour2Schema.restart();

    }

}


// fill our Example Query
function fill_example_query(exercise_id_step) {
    let examplequery = document.getElementById('predefinedQuery');
    switch (exercise_id_step) {
        case 1:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySyntaxError} </p>`;
            break;
        case 2:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySyntaxFixed}</p>`;
            break;
        case 3:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySyntaxError2}</p>`;
            break;
        case 4:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySchemaError}</p>`;
            break;
        case 5:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySchemaError}</p>`;
            break;
        case 6:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySchemaFixed}</p>`;
            break;
        case 7:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySchemaError2}</p>`;
            break;
        case 8:
            examplequery.innerHTML = `<p>Example Query: ${expectedQuerySchemaFixed2}</p>`;
    }

}

//validate the sql query for the tutorial
function validate_sql_query(id) {
    //get CodeMirror Instance and its value
    let query = $('.CodeMirror')[0].CodeMirror.getValue();
    query = query.trim();
    // trim all types of newline
    query = query.replace(/(\r\n|\n|\r)/gm, " ");
    // trim extra whitespaces/double whitespaces
    query = query.replace(/\s+/g, " ");
    query = query.replace(/[",',`,´]/gm, "\'");
    switch (id) {
        case 1:
            //show error box
            if (query.toLowerCase() === expectedQuerySyntaxError.toLowerCase() && !syntaxError) {
                document.querySelector('.alert-danger').classList.remove('hide');
                break;
            }
            //show success box
            if (query.toLowerCase() === expectedQuerySyntaxFixed.toLowerCase() && syntaxError) {
                document.querySelector('.alert-danger').classList.add('hide');
                document.querySelector('.alert-success').classList.remove('hide');
                break;
            }
            break;
        case 2:
            if (query.toLowerCase() === expectedQuerySyntaxError2.toLowerCase() && !syntaxError) {
                document.querySelector('.alert-danger').classList.remove('hide');
                break;
            }
            if (query.toLowerCase() === expectedQuerySchemaError.toLowerCase() && syntaxError) {
                //remove syntax error box and show schema error box
                document.querySelector('.alert-danger').classList.add('hide');
                document.getElementById('warning').classList.remove('hide');
                break;
            }

            //show success box
            if (query.toLowerCase() === expectedQuerySchemaFixed.toLowerCase() && schemaError) {
                document.getElementById('warning').classList.add('hide');
                document.querySelector('.alert-success').classList.remove('hide');
                break;
            }
            break;
        case 3:
            if (query.toLowerCase() === expectedQuerySchemaError2.toLowerCase() && !schemaError) {
                //show warning box
                document.getElementById('warningSchema2').classList.remove('hide');
                break;
            }
            if (query.toLowerCase() === expectedQuerySchemaFixed2.toLowerCase() && schemaError) {
                //show success box
                document.getElementById('warningSchema2').classList.add('hide');
                document.querySelector('.alert-success').classList.remove('hide');
                break;
            }
            break;
    }
}


//autofill button for tutorial query
function autofill_query(id) {

    let codemirrorAutoFill = $('.CodeMirror')[0].CodeMirror;
    //console.log(codemirrorAutoFill);
    switch (id) {
        case 1:
            if (!syntaxError) {
                codemirrorAutoFill.setValue(expectedQuerySyntaxError);
                break;
            } else if (syntaxError) {
                codemirrorAutoFill.setValue(expectedQuerySyntaxFixed);
                break;
            }
            break;
        case 2:
            if (!syntaxError) {
                codemirrorAutoFill.setValue(expectedQuerySyntaxError2);
                break;
            } else if (syntaxError && !schemaError) {
                codemirrorAutoFill.setValue(expectedQuerySchemaError);
                break;
            }
            if (!schemaError) {
                codemirrorAutoFill.setValue(expectedQuerySchemaError);
                break;
            } else if (schemaError) {
                codemirrorAutoFill.setValue(expectedQuerySchemaFixed);
                break;
            }
            break;
        case 3: if (!schemaError){
            codemirrorAutoFill.setValue(expectedQuerySchemaError2);
            break;
        }
            else if (schemaError){
                codemirrorAutoFill.setValue(expectedQuerySchemaFixed2);
                break;
            }

    }
}


//translate Autofill-template
function template_autofill(lang = 'en',autofill_query_id){
    let template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« previous</button>
        <span data-role='separator'>|</span>
        <button class='btn btn-default' data-role='next'> next »</button>
        <span data-role="separator">|</span>
    <button class='btn btn-default' data-role='end'> Done</button>
    <button id="autofillbutton" class="btn btn-primary"
                            onclick="autofill_query(${autofill_query_id})">Autofill-Button</button>
    </div>
  </div>`;

    if (lang === 'de'){
    template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« zurück</button>
        <span data-role='separator'>|</span>
        <button class='btn btn-default' data-role='next'> weiter »</button>
        <span data-role="separator">|</span>
    <button class='btn btn-default' data-role='end'> Fertig</button>
    <button id="autofillbutton" class="btn btn-primary"
                            onclick="autofill_query(${autofill_query_id})">Autofill-Button</button>
    </div>
  </div>`;}

    return template;
}

//translate Template Buttons
function template_buttons(lang) {
    let template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« prev</button>
        <button class='btn btn-default' data-role='next'> next »</button>
        <button class='btn btn-default' data-role='end'> Done</button>
</div></div>`;

    if (lang === 'de') {
        template = `<div class='popover tour'>
    <div class='arrow'></div>
    <h3 class='popover-title'></h3>
    <div class='popover-content'></div>
    <div class='popover-navigation'>
        <button class='btn btn-default' data-role='prev'>« zurück</button>
        <span data-role='separator'>|</span>
        <button class='btn btn-default' data-role='next'> weiter »</button>
        <span data-role="separator">|</span>
    <button class='btn btn-default' data-role='end'> Fertig</button></div></div>`;
    }

    /*//AutofillTemplate
    if (mode === 'autofill') {
        //Insert autofillbutton into our template
        let template_temp = template.slice(0,474) + `<button id ="autofillbutton" class="btn btn-primary" onclick="autofill_query(${autofill_number})">Autofill-Button</button>` + template.slice(474,template.length);
        template = template_temp;
    }
    */
    return template;
}
