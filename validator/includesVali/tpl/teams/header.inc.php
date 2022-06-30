 <?php if($user->getFlagAdmin() == "Y"):?>

     . <a href="index.php?action=admin/teams/statistics">Statistics</a>
     . <a href="index.php?action=admin/teams/teamsAdmin">Admin</a>
 
     <?php

        $user_id = $user->getId();
        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //get all groups
        $sql = "select * from user_groups ";
        $mygroups = $DB->run($sql);
 
        //check if user belongs to a group
        $sql = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
        $mygroup = $DB->run($sql);

        $mygroupid = "";
        $mygroupname = "";
        if(is_array($mygroup))
        {
            $mygroupid = $mygroup[0]['group_id'];

            //my group
            $sql = "select * from user_groups where group_id = '$mygroupid' limit 1";
            $check = $DB->run($sql);

            if(is_array($check))
            {
                $mygroupname = $check[0]['group_name'];
            }
        }

     ?>
    <div style="float: left;max-width: 200px;height: 20px;margin-top: -5px;"> 
        <select class="form-control" onchange="change_group(this.value,<?=$user->getId()?>)">
         <option id="<?=$mygroupid?>"><?=($mygroupid=="") ? '--Select a Group--' : $mygroupname ;?></option>
            <?php if(is_array($mygroups)):?>
                <?php foreach($mygroups as $g):?>
                    <option value="<?=$g['group_id']?>"><?=$g['group_name']?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div> 

 <?php endif; ?>

 . <a href="index.php?action=teams/groupWiki">Projects / GroupWiki</a>
 . <a href="index.php?action=teams/editor">Editor</a>

<script type="text/javascript">
    
 
    function change_group_get_root()
    {
        var a = window.location.href;
        var b = a.split("index.php");
        return b[0] + "../../includesVali/tpl/teams/";

    }

    function change_group(group_id,user_id)
    {
       
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('group_id',group_id);
        form.append('user_id',user_id);

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                //refresh the page
                window.location.href = window.location.href;
            }
        });
        
        ajax.open("POST",change_group_get_root() + "change_group_ajax.php",true)
        ajax.send(form);
    }

</script>