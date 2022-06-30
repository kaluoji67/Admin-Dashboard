<!-- created on 12.06.2017 -->
<!--the form is a request, whether the user, the exercise group, the task group or 
	the task should be delete or not;
	therefore exists two buttons, one for the deletion and the other for closing -->


<div id="deleteRequestDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <?php $action = @$_GET["action"]; ?>
                <h4 class="modal-title">Delete
                <!-- with the current action, the heading of this form is adjusted -->
                <?php switch ($action) {
    					case "admin/viewUsers": ?>
       						User
      				<?php 	break;
						case "admin/viewExerciseGroups": ?>
        					ExerciseGroup
       				<?php 	break;
						case "admin/viewTaskGroups": ?>
        					TaskGroup
        			<?php 	break; 
						case "admin/viewTasks": ?>
							Task
					<?php 	break;
						case "admin/viewPreparation": ?>
							Preparation
					<?php 	break;
						case "admin/viewStatements": ?>
							Statement
                	<?php 
                	}
                	//in every request except the request for tasks, preparation and statement is show
                	//a name (the username, exercise group name or the task group name)
                	if($action!="admin/viewTasks" && $action!="admin/viewPreparation" && $action!="admin/viewStatements"){ ?>
                		<strong><span class="deleteRequest_name"></span></strong></h4>
                <?php
                	}
                	else{ //for the task, the preparation and the statement is show the id ?>
                		<strong>#<span class="deleteRequest_id"></span></strong></h4>
                <?php	
                	} ?>
            </div>
            <form action="index.php?action=delete" method="post" role="form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" class="deleteRequest_name" id="name" name="name" value="" />
           			<input type="hidden" class="deleteRequest_id" id="id" name="id" value="" />	
               		<!-- the question is adjusted for each action -->
               		<?php switch ($action) {
    					case "admin/viewUsers": ?>
       						<h4> <?php echo $l->getString('account_delete_adminMessage'); ?></h4>
      				<?php 	break;
						case "admin/viewExerciseGroups": ?>
        					<h4> <?php echo $l->getString('exercise_group_deleteMessage'); ?></h4>
       				<?php 	break;
						case "admin/viewTaskGroups": ?>
        					<h4> <?php echo $l->getString('task_group_deleteMessage'); ?></h4>
        			<?php 	break; 
						case "admin/viewTasks": ?>
							<h4><?php echo $l->getString('task_deleteMessage'); ?> </h4>
					<?php 	break;
						case "admin/viewPreparation": ?>
							<h4><?php echo $l->getString('preparation_deleteMessage'); ?></h4>
					<?php 	break;
						case "admin/viewStatements": ?>
							<h4><?php echo $l->getString('statement_deleteMessage'); ?> </h4>
                	<?php 
               		} ?>
                </div>
                <div class="modal-footer">     
                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $l->getString('account_edit_close'); ?></button>
                      <button onclick="delClick()" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?php echo $l->getString('delete'); ?></button>
 	            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

	//the Delete-Button in the request open this function
	function delClick()
	{
		//get the current id
		ctl=document.getElementById("id");
		//get the current and complete the deletion link with &delete= and the current id
		document.location+="&delete="+ctl.value;
	}

    //the Delete-Button in viewUsers open this function
    function deleteUser_open(user) {
    	 $.getJSON(
    	   	//get the user data via ajax
			"ajax.php?action=get_user_data&username=" + user,
    	    function(json) {
	    	    //get the username and the id of the user
    	        $(".deleteRequest_name").text(json.username);
    	        $(".deleteRequest_name").val(json.username);
    	        $(".deleteRequest_id").text(json.id);
    	        $(".deleteRequest_id").val(json.id);
    	        //open the request
    	        $("#deleteRequestDialog").modal('show');
    	     }
    	 ).always(function(d) {
    	     console.log(d);
    	 })
    }

    //the Delete-Button in viewExerciseGroup open this function
    function deleteExerciseGroup_open(id) {
        $.getJSON(
            //get the exercise group data via ajax
            "ajax.php?action=get_exgroup_data&eg_id=" + id,
            function(json) {
              	$(".deleteRequest_name").text(json.name);
             	$(".deleteRequest_name").val(json.name);
             	$(".deleteRequest_id").text(json.id);
             	$(".deleteRequest_id").val(json.id);
             	$("#deleteRequestDialog").modal('show');
          	}
		).always(function(d) {
				console.log(d);
		})
    }

	//the Delete-Button in viewTaskGroups open this function
    function deleteTaskGroup_open(id) {
    	$.getJSON(
    	    	//get the task group data via ajax
                "ajax.php?action=get_tskgroup_data&tg_id=" + id,
                function(json) {
                 	$(".deleteRequest_name").text(json.name);
                 	$(".deleteRequest_name").val(json.name);
                 	$(".deleteRequest_id").text(json.id);
                 	$(".deleteRequest_id").val(json.id);
                 	$("#deleteRequestDialog").modal('show');
              	}
    		).always(function(d) {
    				console.log(d);
    		})
	}

	//the Delete-Button in viewTasks open this function
	function deleteTask_open(id) {
		$.getJSON(
				//get the task data via ajax
	            "ajax.php?action=get_tsk_data&tsk_id=" + id,
	            function(json) {
	             	$(".deleteRequest_id").text(json.id);
	             	$(".deleteRequest_id").val(json.id);
	             	$("#deleteRequestDialog").modal('show');
	          	}
			).always(function(d) {
					console.log(d);
			})
	}

	//the Delete-Button in viewPreparation open this function
	function deletePreparation_open(id){
		$.getJSON(
			"ajax.php?action=get_preparation_data&p_id=" + id,
			function(json) {
				$(".deleteRequest_id").text(json.id);
     			$(".deleteRequest_id").val(json.id);
     			$("#deleteRequestDialog").modal('show');
  			}
		).always(function(d) {
				console.log(d);
		})
	}

	//the Delete-Button in viewStatements open this function
	function deleteStatement_open(id){
		$.getJSON(
			"ajax.php?action=get_statement_data&s_id=" + id,
			function(json) {
				$(".deleteRequest_id").text(json.id);
     			$(".deleteRequest_id").val(json.id);
     			$("#deleteRequestDialog").modal('show');
  			}
		).always(function(d) {
				console.log(d);
		})
	}
</script>