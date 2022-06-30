
    <h1><?php echo $l->getString('about_heading'); ?></h1>

    <ul class="list-group">
        <li class="list-group-item"><?php echo $l->getString('about_project_nameII'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_teamII'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_responsibleII'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_advisorII'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_timeII'); ?></li>
    </ul>
    
    <ul class="list-group">
        <li class="list-group-item"><?php echo $l->getString('about_project_name'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_team'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_responsible'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_advisor'); ?></li>
        <li class="list-group-item"><?php echo $l->getString('about_project_time'); ?></li>
    </ul>
    <img src="img/logo.png">
    <?php if(isset($user) && $user->getFlagAdmin() == 'Y'): ?>
    <h1>Version 2.1.1 - 30.04.2020/06.05.2020</h1>
        <ol>
            <li>Check for DEFAULT and NULL is optional at CT Statements. In default it is turned off and can be switched on under edit solution.</li>
            <li>The user now receives all errors that occurred originating from a single query instead of only the first one per statement.</li>
            <li>The user EvalID is now independently stored from the personal data via a hash function.</li>
            <li>The user queries are now stored for later evaluation processes.</li>
            <li>Foreign Key Statements are now checked whether the right table and column is referenced</li>
            <li>Minor language fixes to adapt to both languages</li>
            <li>06.05. Added Table content error and deactivated column order error.</li>
        </ol>
    <h1>Version 2.1.2 - 05.06.2020/16.06.2020</h1>
        <ol>
            <li>Bugfixing of view submission for user - It is now again possible to view the submission of a single user with edit users->View submission</li>
            <li>Security is now enhanced - closed some backholes</li>
            <li>New Admin Overview site - It is now possible to view and export current questionnaires as well as task submissions</li>
            <li>Added Selfcheck as separate questionnaire type</li>
            <li>Changes to questionnaire - they can be voluntarily, have a predecessor which is started after the first one is completed, likerscale can now have different steps</li>
            <li>16.06.: Added possibility to have multiple trial in a selfcheck</li>
            <li>16.06.: Empty selfchecks won't be saved in the database</li>
            <li>16.06.: Check for table content is now independent of spaces and case</li>
        </ol>
    <h1>Version 2.1.3 - 10.07.2020</h1>
    <ol>
        <li>Export of SQL-Queries removes now linebreaks</li>
        <li>Edit of Selfchecks Frontend is ready to be viewed</li>
    </ol>
    <h1>Version 2.1.4 - 29.09.2020/03.10.</h1>
    <ol>
        <li>Edit of Selfchecks and Questionnaires is now entirely possible</li>
        <li>TTS - Time Tracking System now provides data to track user movement within the site</li>
        <li>Case can be important within submissions of the sqlvalidator </li>
        <li>Bugfixing of selfcheck to deal with missing or Null values</li>
        <li>Changed View of EvalAdminOverview window</li>
        <li>Language fixes and spelling correction</li>
        <li>03.10. - Admins can change their semester they start with</li>
        <li>03.10. - Statement title is now a select </li>
        <li>03.10. - ViewTask now has a quick navigation to get to the next task and back to overview</li>
    </ol>
    <?php endif;?>

</body>
</html>