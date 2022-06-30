<!DOCTYPE html>
<?php
    require_once("includes/helpers/fetch_functions.tpl.php");

    //get semesters list from DB
    $semesterList = getSemesters();

    //load semester from selection
    $currentSemester="";
    $currentSemesterDescr="";
    $previousSemester ="";
    if(isset($_GET["currentSemester"])){
        $currentSemester= $_GET["currentSemester"];
        foreach($semesterList as $key=>$semester)
        {

            if($semester["sem_id"]==$currentSemester)
            {
                $currentSemesterDescr=$semester["sem_descr"];
                break;
            }
            $previousSemester=$semester["sem_id"];
        }

    }else{
        $lastSemester = count($semesterList) - 1;
        foreach($semesterList as $key=>$semester)
        {
            if($lastSemester==$key)
            {
                $currentSemester=$semester["sem_id"];
                $currentSemesterDescr=$semester["sem_descr"];
                break;
            }
            $previousSemester=$semester["sem_id"];
        }
    }
    //get number of students
    $students = getNumberOfStudents($currentSemester);
    $countStudents = count($students);

    //get sex/gender
    $genderData = getDemographic($currentSemester);

    //get number of males and females
    $countSpecifiedGender =0;
    foreach($genderData as $gender=>$count)
    {
        $countSpecifiedGender += intval($count);
    }
    if($countStudents>$countSpecifiedGender)
    {
        $genderData["Unspecified"] = $countStudents - $countSpecifiedGender;
    }
    //process gender into two arrays for the chart, one array for label the other for value
    $gendersLabel=Array();
    $gendersCount=Array();
    foreach($genderData as $gender=>$count)
    {
    $gendersLabel[] = $gender;
    $gendersCount[] = $count;
    }

    //get department/programmes
    $programmesList = getProgrammes($currentSemester);
    $countSpecifiedProgrammes =0;
    foreach($programmesList as $programme=>$count)
    {
        $countSpecifiedProgrammes += intval($count);
    }
    if($countStudents>$countSpecifiedProgrammes)
        $programmesList["Unspecified"]= $countStudents - $countSpecifiedProgrammes;

    //process programmesList into two arrays for the chart, one array for label the other for value
    $programmesLabel=Array();
    $programmesCount=Array();
    foreach($programmesList as $programme=>$count)
    {
        $programmesLabel[] = $programme;
        $programmesCount[] = $count;
    }

    //GET ACTIVITY
    $labelActivity = array("week1", "week2");
    $errorActivity = array("1"=> array(20,40), "2"=> array(10,10));
    $activity = array("labels"=> $labelActivity, "errors"=> $errorActivity);

    $semesterActivity = getErrorActivity($currentSemester);

    //get study semesters
    $studySemesterList = getStudySemesters($currentSemester);

    $countSpecifiedSem =0;
    foreach($studySemesterList as $semester=>$count)
    {
        $countSpecifiedSem += intval($count);
    }
    if($countStudents>$countSpecifiedSem)
    {
        $studySemesterList["Unspecified"] = $countStudents - $countSpecifiedSem;
    }
    //process study semesters into two arrays for the chart, one array for label the other for value
    $studySemestersLabel=Array();
    $studySemestersCount=Array();
    foreach($studySemesterList as $semester=>$count)
    {
        $studySemestersLabel[] = $semester;
        $studySemestersCount[] = $count;
    }

    //ERROR ANALYSIS
    $semester_one = $currentSemester;
    $semester_two = $previousSemester;

    //load tasks initially , further changes would be done via ajax
    $semOneTaskList = getTaskList($semester_one,$lang);
    $semOneTask = key(reset($semOneTaskList)["tasks"]);

    $semTwoTaskList = getTaskList($semester_two,$lang);

    //LOAD ERROR LIST
    $semOneErrorList = getErrorList($semOneTask);
    $semTwoErrorList = getErrorList($semester_two);
    //process error lists into two arrays for the chart, one array for label the other for value
    $semOneErrorLabel=Array();
    $semOneErrorFreq=Array();
    foreach($semOneErrorList as $error=>$freq)
    {
        $semOneErrorLabel[]= $error;
        $semOneErrorFreq[]=$freq;
    }

    //RETRIAL LIST
    $semOneGroupId = key($semOneTaskList);
    $semOneRetrialList= getRetrialTaskList($semOneGroupId,$semester_one,$lang);
    //process error lists into two arrays for the chart, one array for label the other for value
    $semOneRetrialLabel=Array();
    $semOneRetrialFreq=Array();
    foreach($semOneRetrialList as $taskId=>$taskDetails)
    {
        $title = key($taskDetails);
        $semOneRetrialLabel[]= $title;
        $semOneRetrialFreq[]= $taskDetails[$title];
    }

    $semTwoGroupId = key($semTwoTaskList);
    $semTwoRetrialList= getRetrialTaskList($semTwoGroupId,$semester_two,$lang);
    //process error lists into two arrays for the chart, one array for label the other for value
    $semTwoRetrialLabel=Array();
    $semTwoRetrialFreq=Array();
    foreach($semTwoRetrialList as $taskId=>$taskDetails)
    {
        $title = key($taskDetails);
        $semTwoRetrialLabel[]= $title;
        $semTwoRetrialFreq[]= $taskDetails[$title];
    }

    // QUESTIONNAIRE ANALYSIS
    $sem_one_available_quest = getQuest($currentSemester,$lang);
    $sem_two_available_quest = getQuest($previousSemester,$lang);

    $answers = getQuestAnswers($currentSemester,$lang,7,1);

?>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src = "dashboard/js/chart_3_8_0/package/dist/Chart.min.js"></script>
        <script type="text/javascript" src = "https://unpkg.com/@sgratzl/chartjs-chart-boxplot@3.6.0/build/index.umd.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!--<link rel="stylesheet" href="css/bootstrap.css.css"> -->
        <link rel="stylesheet" href="dashboard/css/dashboard_styles.css">

        <title>Admin DashBoard</title>
    </head>
    <body>
        <div class="top_panel" >
            <h3 class="title"> Admin Dashboard</h3>
            <div class="top_option">
                <div class="select_semester cell">
                    <label for="semesters"> Semester:</label>
                    <div class="dropdown" name="semesters">

                        <select class="dropdown-content" onchange="pageChangeSemester(this)">
                            <?php foreach($semesterList as $key=>$semester):?>
                                <option  value=<?php echo $semester['sem_id']?> <?php if($currentSemester == $semester['sem_id']) echo'selected="selected"';?>>
                                    <?php echo $semester['sem_descr']?>
                                    </option>
                                <?php endforeach;?>
                        </select>
                    </div>

                </div>
                <div class="top_date cell">
                    <label for="last_refreshed" style="display: inline-block"> Last Refreshed:</label>
                    <div name="last_refreshed" class="last_refreshed" >Date</div>
                </div>
            </div>
        </div>

        <div class="dashboard_container">

            <ul class="tabs">
                <li data-tab-target="#home" class="active tab">Home</li>
                <li data-tab-target="#error_analysis" class="tab">Error Analysis</li>
                <li data-tab-target="#skill_analysis" class="tab">Skill Analysis</li>
                <li data-tab-target="#questionnaire_analysis" class="tab">Questionnaire Analysis</li>
            </ul>

            <div class="tab_content">
                <!-- HOME CONTENT -->
                <div id="home" data-tab-content class="active content">
                <div class="top_metrics " >
                    <div class="cell">Total  Students : <span class="metric_figure"> <?php echo " ".$countStudents?> <span> </div>
                    <!--
                    <div class="cell">Total  Questions</div>
                    <div class="cell" >Submision Rate</div>
                    -->
                </div>
                    <br>
                <div class="chart_container">
                    <div class="pie_chart_container">
                        <div  class="pie1 chart" >
                            <canvas id="home_pie_chart1"></canvas>
                        </div>

                        <div  class="pie2 chart" >
                            <canvas id="home_pie_chart2"></canvas>
                        </div>
                    </div>
                    <br>
                    <div  class="bar chart">
                        <canvas id="home_bar_chart"></canvas>
                    </div>

                </div>

                </div>

                <!-- ERROR CONTENT -->
                <div id="error_analysis" data-tab-content class="content">
                    <div >
                        <div class="cell error_tab active" data-tab-target="#error_frequency"> Error Frequency</div>
                        <div class="cell error_tab" data-tab-target="#retrial_rate">Retrial Rate </div>
                    </div>
                    <!-- ERROR FREQUENCY -->
                    <div class="active error_content" data-tab-content id="error_frequency">
                        <div>
                            <div class="add_new_error_chart cell">
                                Add New Chart
                            </div>
                            <br>
                        </div>
                        <!--chart for semester one error analysis -->
                        <div class="semester_one     error_chart_div">
                            <!-- options -->
                            <div class="options">
                                <label for="semester_one_sel"> Semester</label>
                                <select name="semester_one_sel" lang="<?php echo$lang ?>" class="semester_one_error_sel">
                                    <?php foreach($semesterList as $key=>$semester):?>
                                        <option  value=<?php echo $semester['sem_id']?> <?php if($semester_one == $semester['sem_id']) echo'selected="selected"';?>>
                                            <?php echo $semester['sem_descr']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_one_task"> Task</label>
                                <select name="semester_one_task" class="semester_one_error_task_select">
                                    <?php foreach($semOneTaskList as $key=> $taskGroup):?>
                                    <optgroup label="<?php echo $taskGroup['group_title']?>">
                                        <?php foreach($taskGroup['tasks'] as $key=>$task):?>
                                        <option  value=<?php echo $key?> >
                                            <?php echo $task?>
                                        </option>
                                        <?php endforeach;?>
                                    </optgroup>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_one_proficiency"> Proficiency</label>
                                <select name="semester_one_proficiency" class="semester_one_error_proficiency_select">
                                    <option value="-1" selected="selected">All</option>
                                    <option value="0" >Minor : 0</option>
                                    <option value="1" >1</option>
                                    <option value="2" >2</option>
                                    <option value="3" >3</option>
                                    <option value="4" >Extensive : 4</option>
                                </select>
                            </div>
                            <!-- chart -->
                            <div class="sem_analysis">
                                <div class="chart error_bar">
                                <canvas id="error_bar_chart1"></canvas>
                                </div>
                                <div class="error_list cell">
                                    <span> Errors in task: </span>
                                    <br>
                                    <input  type = "checkbox"  class="sem_one_all_error_check" name = "semester_one_all"  value = "all"  id = "semester_one_all"  >
                                    <label  for = "semester_one_all" > All </label>
                                    <br>
                                    <div class="semester_one_error_checks">
                                    <?php foreach($semOneErrorList as $error=> $frequency):?>
                                        <input  type = "checkbox"  class="sem_one_error_check" name = "<?php echo $error ?>"  value = "<?php echo $frequency ?>" >
                                        <label  for = "<?php echo $error ?>"  > <?php echo $error ?> </label>
                                        <br>
                                    <?php endforeach;?>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- ERROR RETRIAL FREQUENCY -->
                    <div class="error_content" id="retrial_rate" data-tab-content >

                        <!--chart for semester one retrial analysis -->
                        <div class="semester_one">
                            <!-- options -->
                            <div class="options">
                                <label for="semester_one_sel"> Semester</label>
                                <select name="semester_one_sel" lang="<?php echo$lang ?>" class="semester_one_retrial_sel">
                                    <?php foreach($semesterList as $key=>$semester):?>
                                        <option  value=<?php echo $semester['sem_id']?> <?php if($semester_one == $semester['sem_id']) echo'selected="selected"';?>>
                                            <?php echo $semester['sem_descr']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_one_task"> Task</label>
                                <select name="semester_one_task" class="semester_one_retrial_task_select">
                                    <?php foreach($semOneTaskList as $key=> $taskGroup):?>
                                        <option  value=<?php echo $key?>  <?php if($semOneGroupId == $key) echo'selected="selected"';?>>
                                            <?php echo $taskGroup['group_title']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                            </div>
                            <!-- chart -->
                            <div class="sem_analysis">
                                <div class="chart retrial_bar">
                                    <canvas id="retrial_bar_chart1"></canvas>
                                </div>
                            </div>

                        </div>

                        <!--chart for semester one retrial analysis -->
                        <div class="semester_two">
                            <!-- options -->
                            <div class="options">
                                <label for="semester_two_sel"> Semester</label>
                                <select name="semester_two_sel" lang="<?php echo$lang ?>" class="semester_two_retrial_sel">
                                    <?php foreach($semesterList as $key=>$semester):?>
                                        <option  value=<?php echo $semester['sem_id']?> <?php if($semester_two == $semester['sem_id']) echo'selected="selected"';?>>
                                            <?php echo $semester['sem_descr']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_two_task"> Task</label>
                                <select name="semester_two_task" class="semester_two_retrial_task_select">
                                    <?php foreach($semTwoTaskList as $key=> $taskGroup):?>
                                        <option  value=<?php echo $key?>  >
                                            <?php echo $taskGroup['group_title']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <!-- chart -->
                            <div class="sem_analysis">
                                <div class="chart retrial_bar">
                                    <canvas id="retrial_bar_chart2"></canvas>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- QUESTIONNAIRE CONTENT -->
                <div id="questionnaire_analysis" data-tab-content class="content">
                    <div class="quest_chart">

                        <!-- SEMESTER 1 QUESTIONNAIRE-->
                        <div class="quest_chart_sem_one " >
                            <div class="options">
                                <label for="semester_one_sel"> Semester</label>
                                <select name="semester_one_sel" lang="<?php echo$lang ?>" class="quest_sem_one_sel">
                                    <?php foreach($semesterList as $key=>$semester):?>
                                        <option  value=<?php echo $semester['sem_id']?> <?php if($semester_one == $semester['sem_id']) echo'selected="selected"';?>>
                                            <?php echo $semester['sem_descr']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_one_task"> Question</label>
                                <!-- unload the questionnaire titles -->
                                <select name="semester_one_task" lang="<?php echo$lang ?>" class="quest_sem_one_task_sel">
                                <?php foreach($sem_one_available_quest as $key=>$quest):?>
                                    <optgroup label="<?php echo $quest['title']?>">
                                        <?php foreach($quest['taskNum'] as $tkey=>$taskNum):?>
                                            <option  value='<?php echo $key.",".$tkey?>' >
                                                <?php echo $tkey.".  ".$taskNum['title']?>
                                            </option>
                                        <?php endforeach;?>
                                    </optgroup>
                                <?php endforeach;?>
                                </select>

                            </div><br>
                            <!--SEM 1 STATISTICAL INFO -->
                            <div class="sem_one_quest_stats" >
                                <div class="cell">Population Size</div>
                                <div class="cell swap_chart" type="bar" chart="1">Stacked Bar</div>
                                <div class="cell swap_chart" type="boxplot" chart="1">Box Plot</div>
                                <div class="cell swap_chart" type="radar" chart="1">Radar</div>
                                <div class="cell swap_chart" type="line" chart="1">Line</div>
                            </div><br>
                            <!--SEM 1 QUESTIONNAIRE CHART -->
                            <div class="chart quest_bar ">
                                <canvas id="quest_bar_chart1"></canvas>
                            </div>
                        </div><hr>

                        <!-- SEMESTER 2 QUESTIONNAIRE-->
                        <div class="quest_chart_sem_two">
                            <div class="options">
                                <label for="semester_two_sel"> Semester</label>
                                <select name="semester_two_sel" lang="<?php echo$lang ?>" class="quest_sem_two_sel">
                                    <?php foreach($semesterList as $key=>$semester):?>
                                        <option  value=<?php echo $semester['sem_id']?> <?php if($semester_two == $semester['sem_id']) echo'selected="selected"';?>>
                                            <?php echo $semester['sem_descr']?>
                                        </option>
                                    <?php endforeach;?>
                                </select>

                                <label for="semester_one_task"> Question</label>
                                <!-- unload the questionnaire titles -->
                                <select name="semester_one_task" lang="<?php echo$lang ?>" class="quest_sem_two_task_sel">
                                    <?php foreach($sem_two_available_quest as $key=>$quest):?>
                                        <optgroup label="<?php echo $quest['title']?>">
                                            <?php foreach($quest['taskNum'] as $tkey=>$taskNum):?>
                                                <<option  value='<?php echo $key.",".$tkey?>' >
                                                    <?php echo $tkey.".  ".$taskNum['title']?>
                                                </option>
                                            <?php endforeach;?>
                                        </optgroup>
                                    <?php endforeach;?>
                                </select>

                            </div><br>
                            <!--SEM 2 STATISTICAL INFO -->
                            <div class="sem_one_quest_stats" >
                                <div class="cell">Population Size</div>
                                <div class="cell swap_chart" type="bar" chart="2">Stacked Bar</div>
                                <div class="cell swap_chart" type="boxplot" chart="2">Box Plot</div>
                                <div class="cell swap_chart" type="radar" chart="2">Radar</div>
                                <div class="cell swap_chart" type="line" chart="2">Line</div>
                            </div><br>
                            <!--SEM 2 QUESTIONNAIRE CHART -->
                            <div class="chart quest_bar quest_chart_parent_2">
                                <canvas id="quest_bar_chart2"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="quest_list">
                        <div class="quest_list_sem_one">
                        <div class="quest_list_header">
                            Semester one Available Questionnaire
                        </div><br>
                        <div class="quest_list_content">
                            <!-- unload the questionnaire titles -->
                            <?php foreach($sem_one_available_quest as $key=>$quest):?>
                            <div  data-tab-target='<?php echo "#quest_content".$key?>' class="quest" q_id=<?php echo $key?> >
                                <?php echo $quest['title']?>
                            </div>
                            <div data-tab-content id ='<?php echo "quest_content".$key?>' class="quest_content">
                                <!-- unload the taskitems -->
                                <?php foreach($quest['taskNum'] as $tkey=>$taskNum):?>

                                    <!-- check if taskNum has only one item -->
                                    <?php if(count($taskNum['items']) <= 1):?>
                                        <div  class="item" itemId='<?php echo $key.",".$tkey.","."1"?>' >
                                            <?php echo $tkey.".".$taskNum['title']?>
                                        </div>
                                        <!-- else multiple task items -->
                                    <?php else:?>
                                        <div  data-tab-target='<?php echo "#taskNum_content".$tkey?>'class="taskNum" itemId='<?php echo $key.",".$tkey.","."1"?>' >
                                            <p><?php echo $tkey.".".$taskNum['title']?></p>

                                            <div data-tab-content id='<?php echo "taskNum_content".$tkey?>' class="taskNum_content">
                                                <?php foreach($taskNum['items'] as $ikey=>$item):?>
                                                    <div  class="item" itemId='<?php echo $key.",".$tkey.",".$ikey?>' >
                                                        <?php echo $tkey.".".$ikey.".".$item?>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>

                                    <?php endif;?>
                                <?php endforeach;?>
                                <div>

                                    <?php endforeach;?>
                        </div><br><br>

                    </div>
                </div>
                        </div>
                        <br><br><br>
                        <div class="quest_list_sem_two">
                            <div class="quest_list_header">
                                Semester Two Available Questionnaire
                            </div><br>
                            <div class="quest_list_content">
                                <!-- unload the questionnaire titles -->
                                <?php foreach($sem_two_available_quest as $key=>$quest):?>
                                <div  data-tab-target='<?php echo "#quest_content".$key?>' class="quest" q_id=<?php echo $key?> >
                                    <?php echo $quest['title']?>
                                </div>
                                <div data-tab-content id ='<?php echo "quest_content".$key?>' class="quest_content">
                                    <!-- unload the taskitems -->
                                    <?php foreach($quest['taskNum'] as $tkey=>$taskNum):?>

                                        <!-- check if taskNum has only one item -->
                                        <?php if(count($taskNum['items']) <= 1):?>
                                            <div  class="item" itemId='<?php echo $key.",".$tkey.","."1"?>' >
                                                <?php echo $tkey.".".$taskNum['title']?>
                                            </div>
                                            <!-- else multiple task items -->
                                        <?php else:?>
                                            <div  data-tab-target='<?php echo "#taskNum_content".$tkey?>'class="taskNum" itemId='<?php echo $key.",".$tkey.","."1"?>' >
                                                <p><?php echo $tkey.".".$taskNum['title']?></p>

                                                <div data-tab-content id='<?php echo "taskNum_content".$tkey?>' class="taskNum_content">
                                                    <?php foreach($taskNum['items'] as $ikey=>$item):?>
                                                        <div  class="item" itemId='<?php echo $key.",".$tkey.",".$ikey?>' >
                                                            <?php echo $tkey.".".$ikey.".".$item?>
                                                        </div>
                                                    <?php endforeach;?>
                                                </div>
                                            </div>

                                        <?php endif;?>
                                    <?php endforeach;?>
                                    <div>

                                        <?php endforeach;?>
                                    </div><br><br>

                                </div>
                            </div>
                        </div>

            </div>

                </div>

                <!-- STUDENT ANALYSIS CONTENT
                <div id="skill_analysis" data-tab-content class="content skill_container">

                    <div class="options">
                        <label for="semester_one_sel"> Semester</label>
                        <select name="semester_one_sel" lang="<?php echo$lang ?>" class="skill_semester_sel">
                            <?php foreach($semesterList as $key=>$semester):?>
                                <option  value=<?php echo $semester['sem_id']?> <?php if($semester_one == $semester['sem_id']) echo'selected="selected"';?>>
                                    <?php echo $semester['sem_descr']?>
                                </option>
                            <?php endforeach;?>
                        </select>

                        <label for="semester_one_task"> Task</label>
                        <select name="semester_one_task" class="skill_task_select">
                            <?php foreach($semOneTaskList as $key=> $taskGroup):?>
                                <optgroup label="<?php echo $taskGroup['group_title']?>">
                                    <?php foreach($taskGroup['tasks'] as $key=>$task):?>
                                        <option  value=<?php echo $key?> >
                                            <?php echo $task?>
                                        </option>
                                    <?php endforeach;?>
                                </optgroup>
                            <?php endforeach;?>
                        </select>

                        <label for="semester_one_proficiency"> Proficiency</label>
                        <select name="semester_one_proficiency" class="skill_proficiency_select">
                            <option value="-1" selected="selected">All</option>
                            <option value="0" >Minor : 0</option>
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >Extensive : 4</option>
                        </select>
                    </div>
                    <div class="skill_content">
                        <div class="students">
                        </div>
                    </div>
                </div >
                -->

        <script>
            const lang = "<?php echo$lang ?>";
            const semesterList = JSON.parse(`<?= json_encode($semesterList) ?>`);


            const colors = ["#3e95cd", "#8e5ea2","#3cba9f", "#FF5733","#A6D516", "#16ACD5","#4e6755","#8e8e12","#EDC5FF","#ADDEC8","#ff9600",
            "#969600","#9696a4","#6d96a4","#ca96a4","#ca96ff","#5796ff","#57efff","#57ef8a","#f2ef8a","#f2a18a","#8f3570"];
            const transparentColors = ["#3e95cd80", "#8e5ea280","#3cba9f80", "#FF573380","#A6D51680", "#16ACD580","#4e675580","#8e8e1280","#EDC5FF80","#ADDEC880","#ff960080",
                "#96960080","#9696a480","#6d96a480","#ca96a480","#ca96ff80","#5796ff80","#57efff80","#57ef8a80","#f2ef8a80","#f2a18a80","#8f357080"];
            //HOME
            let gendersLabel = [<?php echo '"'.implode('","', $gendersLabel).'"' ?>];
            let gendersCount = [<?php echo '"'.implode('","', $gendersCount).'"' ?>];

            let programmesLabel = [<?php echo '"'.implode('","', $programmesLabel).'"' ?>];
            let programmesCount = [<?php echo '"'.implode('","', $programmesCount).'"' ?>];

            let studySemestersLabel = [<?php echo '"'.implode('","', $studySemestersLabel).'"' ?>];
            let studySemestersCount = [<?php echo '"'.implode('","', $studySemestersCount).'"' ?>];
            // chart 1
            let homeChart1 = new Chart(document.getElementById("home_pie_chart1"), {
                type: 'pie',
                data: {
                    labels: gendersLabel,
                    datasets: [
                        {
                            label: "Students By Gender",
                            backgroundColor: colors,
                            data: gendersCount,
                            fontsize:5
                        }
                    ]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: "Students By Gender"
                        },
                        legend: { display: true, position:'right',
                            fontsize:3
                        }
                    },
                    maintainAspectRatio: false,
                }
            });

            // chart 2
            let homeChart2 = new Chart(document.getElementById("home_pie_chart2"), {
                type: 'doughnut',
                data: {
                    labels: programmesLabel,
                    datasets: [
                        {
                            label: "Students By Programme",
                            backgroundColor: colors,
                            data: programmesCount,
                            fontsize:5
                        }
                    ]
                },
                options: {
                    plugins:{
                        title: {
                            display: true,
                            text: "Students By Programme"
                        },
                        legend: { display: true, position:'right',
                            fontsize:3
                        },
                    },
                    maintainAspectRatio: false,
                }
            });

            // chart 3
            /*
            let homeChart3 = new Chart(document.getElementById("home_bar_chart"), {
                type: 'bar',
                data: {
                    labels: studySemestersLabel,
                    datasets: [
                        {
                            label: "Study Semesters",
                            backgroundColor: colors,
                            data: studySemestersCount
                        }
                    ]
                },
                options: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'students by Study Semester'
                    },
                    maintainAspectRatio: false,
                }
            });
            */
            const semesterActivity = JSON.parse(`<?= json_encode($semesterActivity) ?>`);
            let semesterActivityDatasets= Array();
            for(const error in semesterActivity.errors)
            {
                let er = {
                    label: error,
                    backgroundColor : colors[parseInt(error)],
                    data : semesterActivity.errors[error]
                }
                semesterActivityDatasets.push(er);
            }

            let homeChart3 = new Chart(document.getElementById("home_bar_chart"), {
                type: 'bar',
                data: {
                    labels: semesterActivity.labels,
                    datasets: semesterActivityDatasets,
                },
                options: {
                    plugins:{
                        title: {
                            display: true,
                            text: "Students Activities"
                        },
                        legend: { display: true, position:'right',
                            fontsize:3
                        },
                    },
                    tooltips: {
                        displayColors: true,
                        callbacks:{
                            mode: 'x',
                        },
                    },
                    scales: {
                        xAxis: {
                            stacked: true,
                            gridLines: {
                                display: false,
                            }
                        },
                        yAxis: {
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                            },
                            type: 'linear',
                            title: {
                                display: true,
                                text: 'Submissions'
                            },
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom' },
                }
            });
            //HOME - END

            //ERROR ANALYSIS
            // semester one bar
            /*
            let errorChart1= new Chart(document.getElementById("error_bar_chart1"), {
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
            */
            // semester two bar

            let errorChart2= new Chart(document.getElementById("error_bar_chart2"), {
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
            //Retrial Rate
            let retryLabel = [<?php echo '"'.implode('","', $semOneRetrialLabel).'"' ?>];
            let retryFreq = [<?php echo '"'.implode('","', $semOneRetrialFreq).'"' ?>];
            let retryChart1= new Chart(document.getElementById("retrial_bar_chart1"), {
                type: 'bar',
                data: {
                    labels: retryLabel,
                    datasets: [
                        {
                            label: "",
                            backgroundColor: colors,
                            data: retryFreq
                        }
                    ]
                },
                options: {
                    plugins:{
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Retrial Rate of Tasks'
                        }
                    },
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: {
                            title: {
                                display: true,
                                text: 'Number of Retries'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        },
                        xAxes: {
                            scaleFontSize: 10,
                            display: true,
                        }
                    }
                }
            });

            let retryLabel2 = [<?php echo '"'.implode('","', $semTwoRetrialLabel).'"' ?>];
            let retryFreq2 = [<?php echo '"'.implode('","', $semTwoRetrialFreq).'"' ?>];
            let retryChart2= new Chart(document.getElementById("retrial_bar_chart2"), {
                type: 'bar',
                data: {
                    labels: retryLabel2,
                    datasets: [
                        {
                            label: "",
                            backgroundColor: colors,
                            data: retryFreq2
                        }
                    ]
                },
                options: {
                    plugins:{
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Retrial Rate of Tasks'
                        }
                    },
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: {
                            title: {
                                display: true,
                                text: 'Number of Retries'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        },
                        xAxes: {
                            scaleFontSize: 10,
                            display: true,
                        }
                    }
                }
            });

            //Questionnaire analysis
            let questChartDataset1= new Array();
            const questChartCanvas1 =document.getElementById("quest_bar_chart1");
            const questChartParent1 =questChartCanvas1.parentElement;
            let questChart1= new Chart(questChartCanvas1, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "",
                            backgroundColor: colors,
                            data: []
                        }
                    ]
                },
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
            });

            let questChartDataset2= new Array();
            const questChartCanvas2 =document.getElementById("quest_bar_chart2");
            const questChartParent2 =questChartCanvas2.parentElement;
            let questChart2= new Chart(questChartCanvas2, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "",
                            backgroundColor: colors,
                            data: []
                        }
                    ]
                },
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
            });

            //ERROR - END
        </script>
        <script src="dashboard/js/index.js"></script>
        <script src="dashboard/js/ajaxRequests.js"></script>
        <script src="dashboard/js/errorAnalysis.js"></script>
        <script src="dashboard/js/retrialAnalysis.js"></script>
        <script src="dashboard/js/questionnaireAnalysis.js"></script>

    </body>
</html>
