<?php 
    require __DIR__ . "/database.php";
    $DB = Database::getInstance();
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);


function group_name($group_id,$DB){

    $group_name = "Unknown";

    $query = "select group_name from user_groups where group_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['group_name'];
    }
    return $group_name;
}


    //confirm the user belongs to a group
    $user_id = $user->getId();
    $group_id = 0;
    $belongs = false;

    $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

    	$belongs = true;
        $group_id = $check[0]['group_id'];
        $group_name = group_name($check[0]['group_id'],$DB);

    }

    //collect all groups
    $query = "select * from user_groups ";
    $groups = $DB->run($query);


?>

<style type="text/css">

    #ToDo{
        height: 50px; background-color: #d3d3d3;color: gray;
    }
    #search_box{
        width: 400px; height: 29px; border-radius: 5px; border: none; padding: 4px;
        background-image: ;
    }
    #cover_Area{
        max-width: 900px; width: 100%; margin: auto; background-color: whitesmoke; min-height: 500px;
        text-align: left;padding: 10px;
    }

    #post_area{
        background-color: ghostwhite; flex: 2.5; padding: 20px;
    }
    #white_board{
        border: solid thin #aaa; padding: 10px;
    }

</style>

<body style="font-family: 'Arial Unicode MS',serif">
	<div id="cover_Area">
		<div style="text-align: center;">
			
			<h2>
				<img src="../../includesVali/tpl/teams/user-group-new-icon.png" style="width:75px;"><br>
				Join Group
			</h2>


			<form method="post" style="width: 100%;max-width: 500px;padding: 10px;border:solid thin #aaa;border-radius: 5px;margin: auto;">
				
				<?php if(!$belongs):?>

                    <?php if(false):?>
        				<span>Enter your email address and select a group to join</span><br><br>

        				<input type="email" name="email" class="form-control js-email" placeholder="Your Email" required autofocus><br>
        				
        				<select name="group_id" class="form-control js-group-id">
        					
        					<option value="">Select a group</option>

        					<?php 
        						if(is_array($groups)){

        							foreach ($groups as $key => $row) {
        								# code...
        								echo "<option value='$row[group_id]' >$row[group_name]</option>";
        							}
        						}
        					?>
        					
        				</select><br><br>

        				<input type="button" class="btn btn-warning" value="Join" onclick="add_member(event)"><br>
                    <?php endif;?>

                    Please contact your tutor to assign you to a group

				<?php else:?>
					You already belong to a group named '<?=$group_name?>'
                <?php endif;?>
			</form>
		</div>
	</div>
</body>

<script type="text/javascript">
	
	function add_member(e){

		var email = e.target.parentNode.querySelector(".js-email");
		var group_id = e.target.parentNode.querySelector(".js-group-id");

		if(email.value.trim() == "" && !email.value.includes("@")){

			alert("Please enter a valid email address");
			email.focus();
			return;
		}

		if(group_id.value.trim() == "Select a group" || isNaN(group_id.value.trim())){

			alert("Please select a valid group");
			group_id.focus();
			return;
		}

		join_send_data({
            email:email.value.trim(),
            user_id: '<?=$user_id?>',
            group_id: group_id.value.trim(),
            data_type: 'add_group_member'
        });

	}

	function join_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('data',JSON.stringify(data));

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                join_handle_result(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "admin_ajax.php",true)
        ajax.send(form);
    }

    function join_handle_result(result)
    {
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_group_member")
            {
                alert(obj.message);
                 
            }else{
            	alert(obj.message);
            }
        }
    }

    function get_root()
    {
        var a = window.location.href;
        var b = a.split("index.php");
        return b[0] + "../../includesVali/tpl/teams/";

    }

</script>