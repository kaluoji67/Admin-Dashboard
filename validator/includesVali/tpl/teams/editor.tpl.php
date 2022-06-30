<?php

require __DIR__ . "/database.php";
$DB = Database::getInstance();

create_db($DB);

function create_db($DB)
{

    //create another database
    $sql = "create database if not exists teams01";
    $DB->run($sql);

    //make sure tables exixt
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS editor (
        id BIGINT PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT DEFAULT NULL,
        group_id BIGINT DEFAULT NULL,
        query text,
        date DATETIME,
        INDEX user_id (user_id),
        INDEX group_id (group_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS queries (
        id BIGINT PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT DEFAULT NULL,
        group_id BIGINT DEFAULT NULL,
        failed TINYINT(1) DEFAULT 0,
        query text,
        result text,
        date DATETIME,
        INDEX user_id (user_id),
        INDEX failed (failed),
        INDEX group_id (group_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);

    

}


    //read all saved queries
    $user_id = $user->getId();
    $sql = "select * from editor where user_id = '$user_id' ";
    $myqueries = $DB->run($sql);

    if($myqueries)
    {
        $myqueries = array_column($myqueries, "query");
        $myqueries = json_encode(array_values($myqueries));
    }

    //check if user belongs to a group
    $sql = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $mygroup = $DB->run($sql);

    $mygroupid = null;
    if($mygroup)
    {
        $mygroupid = $mygroup[0]['group_id'];
    }

    

?>

<html lang="">
<head>
    <title> Editor </title>
</head>

<style type="text/css">


    .button{

        border-radius: 5px;
        font-size: 18px;
        border:none;
        padding:4px;
        padding-right:10px;
        padding-left:10px;
        color: white;
        margin: 6px;
    }

    .icon{
        float: left;
        width: 20px;
        margin: 2px;
    }

    .green:hover{
        background-color: #0c9f8a;
    }

    .green{
        background-color: #06c3a7;
    }

    .blue{
        background-color: #3273dc;
    }

    .blue:hover{
        background-color: #2b60b5;
    }

    .purple{
        background-color: #ac22dd;
    }

    .purple:hover{
        background-color: #9106c3;
    }
    
    .red{
        background-color: #f75151;
    }

    .red:hover{
        background-color: #d73e3e;
    }

    

    .textarea{

        flex: 10;
        background-color: white;
        padding-left:4px;
        padding-right:4px;
        min-height: 300px;
        font-size: 16px;
        color: #236e24;
        font-family: verdana;
    }

    .line_number{
        background-color: #dcdcdc;
        color: #444;
        text-align: right;
        padding-right: 10px;
        font-size:16px;
        font-family: verdana;
    }

    #result{

        width:100%;
        height: 100%;
        background-color: #363636;
        flex: 1;
        min-height: 300px;
        color: white;
        font-size: 13px;
        padding: 10px;
    }

    #saved_queries{

        width:100%;
        height: 100%;
        background-color: #363636;
        flex: 1;
        min-height: 300px;
        color: white;
        font-size: 13px;
        padding: 10px;
    }


    pre{
        background-color: #00000000;
        color: white;
    }

    td, th{

        padding:6px;
        border-left: solid thin grey;
    }

    th{
        border-bottom: solid thin grey;
    }
    hr{
        opacity: 0.1;
        color: blue;
        margin: 6px;
    }

    .hide{
        display: none;
    }

    #loader{
        position: relative;
        flex: 1;
    }

    #loader img{

        position: absolute;
        left:50%;
        top:50%;
        transform: translate(-50%, -50%);
        width: 150px;
        height: 150px;
    }

    .saved_query{
        border:solid thin #aaa;
        padding: 10px;
        position: relative;
        margin-bottom: 6px;
        background-color: #555;
    }

    #ToDo{
        height: 50px; background-color: #d5e5ea;color: #ae4949;
    }
    #search_box{
        width: 400px; height: 29px; border-radius: 5px; border: none; padding: 4px;
        background-image: ;
    }
    #cover_Area{
        width: 800px; margin: auto; background-color: whitesmoke; min-height: 500px;
    }

    #post_area{
        background-color: ghostwhite; flex: 0 0 50px; padding: 20px;
    }
    #white_board{
        border: solid thin #aaa; padding: 10px;
    }

</style>
<br>
<body style="font-family: verdana, 'Arial Unicode MS',serif">
    <div id="">
        <div style="background-color:#ccc;margin: auto; width: 100%; font-size: 14px;display: flex;">
            <div style="flex: 1;padding:10px;background-color: #444;color: white;">Editor</div>  <div style="padding:10px;text-align:right;flex:10;"><?php include("header.inc.php");?></div> 
        </div>
    </div>
 <div style="display: flex;">
 <div style="flex: 1">
        <!--saved queries top area-->
            <div class="panel panel-default" style="marsgin-top: -30px;margin-bottom: 5px;">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse20" class="collapsed" aria-expanded="false">
                        Saved Queries</a>
                    </h4>
                </div>
                <div id="collapse20" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                    <div class="js-top-saved-queries panel-body">
                            
                      </div>
                </div>
            </div>
    <!--end saved queries top area-->

    <div id="ToDo">
        <div style="margin: auto; width: 800px; font-size: 30px;">
 
            <!--
            <button class="button blue" style="float: right;">
                <svg fill="white" class="icon" width="24" height="24" viewBox="0 0 24 24"><path d="M15 2v5h5v15h-16v-20h11zm1-2h-14v24h20v-18l-6-6z"/></svg>
                Save
            </button>-->
            <button id="run_button" class="button green" style="float: right;" onclick="run_query(event)">
                <svg fill="white" class="icon" width="24" height="24" viewBox="0 0 24 24"><path d="M3 22v-20l18 10-18 10z"/></svg>
                Run
            </button>
            <button class="button purple" style="float: right;" onclick="show_code(event)">
                <svg fill="white" class="icon" width="24" height="24" viewBox="0 0 24 24"><<path d="M24 10.935v2.131l-8 3.947v-2.23l5.64-2.783-5.64-2.79v-2.223l8 3.948zm-16 3.848l-5.64-2.783 5.64-2.79v-2.223l-8 3.948v2.131l8 3.947v-2.23zm7.047-10.783h-2.078l-4.011 16h2.073l4.016-16z"/></svg>
                Query
            </button>

        </div>
    </div>

    <!--Cover Area-->
    <div id="cover_Area">

        <!--Below Cover Area-->
        <div id="Noti_Work" style="display: flex; padding: 8px;background-color: #fafafa;min-height: 300px;">

            <!--Post_Area-->
            <div id="line_numbers" class="" style="background-color: #ebebeb;flex: 0 0 50px;">
                <div class="line_number">1</div>
            </div>
            <div id="text_input" class="textarea" onkeyup="line_number(event)" onclick="clicked(event)" contenteditable="true" ></div>
            <div id="result" class="hide"></div>
            <div id="saved_queries" class="hide"></div>
            <div id="loader" class="hide"><img src="../../includesVali/tpl/teams/loader.gif"></div>

        </div>

        <button id="clear_query_button" class="button red" onclick="clear_query(event)">
            <svg width="24" height="24"  fill="white" class="icon" fill-rule="evenodd" clip-rule="evenodd"><path d="M5.662 23l-5.369-5.365c-.195-.195-.293-.45-.293-.707 0-.256.098-.512.293-.707l14.929-14.928c.195-.194.451-.293.707-.293.255 0 .512.099.707.293l7.071 7.073c.196.195.293.451.293.708 0 .256-.097.511-.293.707l-11.216 11.219h5.514v2h-12.343zm3.657-2l-5.486-5.486-1.419 1.414 4.076 4.072h2.829zm.456-11.429l-4.528 4.528 5.658 5.659 4.527-4.53-5.657-5.657z"/></svg>
            &nbsp Clear query
        </button>
        <button id="show_list_button" class="button purple" onclick="show_saved(event)">
            <svg fill="white" class="icon" width="24" height="24" viewBox="0 0 24 24"><path d="M15.003 3h2.997v5h-2.997v-5zm8.997 1v20h-24v-24h20l4 4zm-19 5h14v-7h-14v7zm16 4h-18v9h18v-9z"/></svg>
            &nbsp Show saved list
        </button>
        <button id="add_to_list_button" class="button blue hide" onclick="add_to_list(event)">
            <svg fill="white" class="icon"  width="24" height="24" viewBox="0 0 24 24"><path d="M19 11h-14v-2h14v2zm0 2h-14v2h14v-2zm0 4h-14v2h14v-2zm3-11v16h-20v-16h20zm2-6h-24v24h24v-24z"/></svg>
            &nbsp Add query to list
        </button>


    </div>
    </div>
    <div style="flex: 1;">
        <?php require "chat.tpl.php" ?>
    </div>
</div>
</body>
</html>

<script type="text/javascript">
    
    var queries_from_db = `<?=$myqueries?>`;
    var group_id = `<?=$mygroupid?>`;

    var TOTAL_LINES = 1;
    var SAVED_QUERIES = [];
    var MODE = "QUERY";

    if(queries_from_db != "")
    {
        SAVED_QUERIES = JSON.parse(queries_from_db);
        display_saved();
        show_saved();
    }

    var text_input = document.getElementById("text_input");
    text_input.focus();

    function line_number(e)
    {

        var text_input = document.getElementById("text_input"); //get the text input div
        var text = text_input.innerHTML;                        //collect its contents

        //check how many divs it has
        var text_divs = text_input.querySelectorAll("DIV");
        var number_of_text_divs = text_divs.length || 1;

        var line_numbers_container = document.getElementById("line_numbers");
        var line_number_divs = line_numbers_container.querySelectorAll("DIV");

        //count the break tags in the text
        var count_br = (text.match(/<br/g) || []).length;
        var number_of_line_divs = line_number_divs.length;

        if(count_br > 0)
            count_br -= 1;

        number_of_text_divs += count_br;

        if(number_of_text_divs < number_of_line_divs)
        {
            var difference = number_of_line_divs - number_of_text_divs;
            //remove a line number
            var count = 0;
            for (var i = number_of_line_divs - 1; i >= 0; i--) {

                count++;
                if(count <= difference){

                    TOTAL_LINES--;
                    line_number_divs[i].remove();
                }
            }

        }else
        if(number_of_text_divs > number_of_line_divs)
        {
            //add a line numbers
            var difference = number_of_text_divs - number_of_line_divs;
            var count = 0;
            for (var i = number_of_text_divs - 1; i >= 0; i--) {

                count++;
                if(count <= difference){

                    TOTAL_LINES++;
                    line_numbers_container.innerHTML += "<div class='line_number'>" + (number_of_line_divs + count) + "</div>";
                }
            }

        }

        //hide or show the add to list button
        var add_to_list_button = document.querySelector("#add_to_list_button");
        text = clean_string(text);

        if(br2nl(removeDivs(text.trim())) == ""){
            add_to_list_button.classList.add("hide");
        }else{
            add_to_list_button.classList.remove("hide");
        }
    }

    function clicked(e)
    {
        if(e.target.id != "text_input"){

            //remove all bg colors first
            var text_input = document.getElementById("text_input"); //get the text input div

            //check how many divs it has
            var text_divs = text_input.querySelectorAll("DIV");
            for (var i = text_divs.length - 1; i >= 0; i--) {
                text_divs[i].style.backgroundColor = "#00000000";
            }

            //set new bg color to selected line
            e.target.style.backgroundColor = "#ededed";
        }
    }

    function clean_string(text){

        text = text.replaceAll("<br>","");
        text = text.replaceAll("<div>","");
        text = text.replaceAll("</div>","");
        text = text.replaceAll("<span>","");
        text = text.replaceAll("</span>","");
        text = text.replaceAll("&nbsp;","");
        text = text.replace(/^\s+|\s+$/g,'');
        return text;
    }

    function run_query(e)
    {

        if(MODE == "SAVED"){

            //select only the selected queries
            var SELECTED_QUERIES = [];

            var saved_queries = document.getElementById("saved_queries");
            var checkboxes = saved_queries.querySelectorAll("INPUT");

            for (var i = 0; i < checkboxes.length; i++) {
                if(checkboxes[i].checked)
                {
                    SELECTED_QUERIES.push(SAVED_QUERIES[checkboxes[i].id]);
                }
            }


            if(SAVED_QUERIES.length == 0)
            {
                alert("Please save at least one query to run");
                return;
            }

            if(SELECTED_QUERIES.length == 0)
            {
                alert("Please selected at least one query to run");
                return;
            }
            
             var text = SELECTED_QUERIES.join(";");
            //remove duplicate semicolons
            text = text.replace(/;{2,}/mg,";");
        }else
        {
            var text_input = document.getElementById("text_input"); //get the text input div
            var text = text_input.innerHTML;

            if(text.trim() == "")
            {
                alert("Please type a query to run");
                text_input.focus();
                return;
            }
        }

        send_data(br2nl(removeDivs(text)));
    }

    function send_data(data)
    {
        show_loader();
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('query',data);
        form.append('user_id',<?=$user->getId()?>);
        form.append('group_id',group_id);

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                var result_container = document.getElementById("result"); //get the results div
                result_container.innerHTML = ajax.responseText;
                show_result();
            }
        });
        
        ajax.open("POST",get_root() + "editor_ajax.php",true)
        ajax.send(form);
    }

    function get_root()
    {
        var a = window.location.href;
        var b = a.split("index.php");
        return b[0] + "../../includesVali/tpl/teams/";

    }

    function br2nl(str) {
        return str.replace(/<br\s*\/?>/mg,"\n\r");
    }

    function removeDivs(str){

        var a = str.replace(/<div\s*\/?>/mg,"");
        return a.replace(/<\/div\s*\/?>/mg,"");
    }

    function add_to_list(e){

        var text_input = document.querySelector("#text_input");
        var text = clean_string(text_input.innerHTML);

        if(br2nl(removeDivs(text.trim())) == ""){
            alert("please type a query first!");
            text_input.focus();
            return;
        }

        SAVED_QUERIES.push(text_input.innerHTML);

        display_saved();
        show_saved(true);

        save_all_queries();
    }

    function save_all_queries()
    {
        //save queries to database
        var ajax = new XMLHttpRequest();
        var form = new FormData();
        var data = JSON.stringify(SAVED_QUERIES);
        form.append('queries',data);
        form.append('user_id',<?=$user->getId()?>);
        form.append('group_id',group_id);

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                //alert(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "editor_ajax.php",true)
        ajax.send(form);
    }

    function remove_from_list(index){

        if(confirm("Are you sure you want to remove this query?"))
            SAVED_QUERIES.splice(index,1);

        display_saved();
        save_all_queries();
    }

    function clear_query(e){

        if(!confirm("Are you sure you want to clear the query??")){
            return;
        }

        var text_input = document.querySelector("#text_input");
        text_input.innerHTML = "";
        line_number(true);
        text_input.focus();
    }

    function show_code(){

        MODE = "QUERY";

        var result_container = document.getElementById("result"); //get the results div
        result_container.classList.add("hide");

        var text_input = document.getElementById("text_input"); //get the text input div
        text_input.classList.remove("hide");

        var line_numbers_container = document.getElementById("line_numbers");
        line_numbers_container.classList.remove("hide");

        var loader = document.getElementById("loader");
        loader.classList.add("hide");

        var add_to_list_button = document.getElementById("add_to_list_button");
        add_to_list_button.classList.remove("hide");

        var saved_queries = document.getElementById("saved_queries");
        saved_queries.classList.add("hide");

        //change run all button to run
        var run_button = document.getElementById("run_button");
        run_button.innerHTML = run_button.innerHTML.replace("Run All","Run");

        text_input.focus();
    }

    function show_result(){

        MODE = "RESULTS";

        var result_container = document.getElementById("result"); //get the results div
        result_container.classList.remove("hide");

        var text_input = document.getElementById("text_input"); //get the text input div
        text_input.classList.add("hide");

        var line_numbers_container = document.getElementById("line_numbers");
        line_numbers_container.classList.add("hide");

        var loader = document.getElementById("loader");
        loader.classList.add("hide");

        var add_to_list_button = document.getElementById("add_to_list_button");
        add_to_list_button.classList.add("hide");

        var saved_queries = document.getElementById("saved_queries");
        saved_queries.classList.add("hide");

    }

    function show_saved(){

        MODE = "SAVED";

        var saved_queries = document.getElementById("saved_queries");
        saved_queries.classList.remove("hide");

        var result_container = document.getElementById("result"); //get the results div
        result_container.classList.add("hide");

        var text_input = document.getElementById("text_input"); //get the text input div
        text_input.classList.add("hide");

        var line_numbers_container = document.getElementById("line_numbers");
        line_numbers_container.classList.add("hide");

        var loader = document.getElementById("loader");
        loader.classList.add("hide");

        var add_to_list_button = document.getElementById("add_to_list_button");
        add_to_list_button.classList.add("hide");

        //change run button to run all
        var run_button = document.getElementById("run_button");
        if(!run_button.innerHTML.includes("Run All"))
            run_button.innerHTML = run_button.innerHTML.replace("Run","Run All");
    }


    function show_loader(){

        MODE = "LOADING";

        var result_container = document.getElementById("result"); //get the results div
        result_container.classList.add("hide");

        var text_input = document.getElementById("text_input"); //get the text input div
        text_input.classList.add("hide");

        var line_numbers_container = document.getElementById("line_numbers");
        line_numbers_container.classList.add("hide");

        var loader = document.getElementById("loader");
        loader.classList.remove("hide");

        var add_to_list_button = document.getElementById("add_to_list_button");
        add_to_list_button.classList.add("hide");

        var saved_queries = document.getElementById("saved_queries");
        saved_queries.classList.add("hide");

    }

    function display_saved(){

        var saved_queries = document.getElementById("saved_queries");
        saved_queries.innerHTML = "<span style='font-size:18px;'>Run Saved Queries: </span>(only selected will run)<br>";

        var top_saved_queries = document.querySelector(".js-top-saved-queries");
        top_saved_queries.innerHTML = "";

        for (var i = 0; i < SAVED_QUERIES.length; i++) {
            saved_queries.innerHTML += `<div class="saved_query">
            <input type="checkbox" class="js-active-query" id="${i}" checked style="transform: scale(1.3);margin-right:6px;cursor:pointer;"/>
            ${SAVED_QUERIES[i]}
            <svg fill="white" style="position:absolute;right:2px;top:2px;margin:4px;cursor:pointer" onclick="remove_from_list(${i})" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>
            </div>`;

            top_saved_queries.innerHTML += `<div class="list-group-item">
            ${i+1}. 
            <span id="saved_${i}">${SAVED_QUERIES[i]}</span>
            <svg fill="orange" style="position:absolute;right:35px;top:2px;margin:4px;cursor:pointer" onclick="CopyToClipboard('saved_${i}')" width="24" height="24" viewBox="0 0 24 24"><path d="M21 2h-19v19h-2v-21h21v2zm3 2v20h-20v-20h20zm-2 2h-1.93c-.669 0-1.293.334-1.664.891l-1.406 2.109h-6l-1.406-2.109c-.371-.557-.995-.891-1.664-.891h-1.93v16h16v-16zm-3 6h-10v1h10v-1zm0 3h-10v1h10v-1zm0 3h-10v1h10v-1z"/></svg>
            <svg fill="orange" style="position:absolute;right:2px;top:2px;margin:4px;cursor:pointer" onclick="remove_from_list(${i})" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>
            </div>`;
 
        }

    }

    function CopyToClipboard(containerid){

      if (document.selection){

        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("copy");
        alert("Query copied to clipboard")
      }else 
      if(window.getSelection){

        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand("copy");
        alert("Query copied to clipboard!")
      }
    }

</script>
<!--
todo
1. to creat an enviroment like google doc where they can code or write together
   the wiki need to have a similar feature.
-->