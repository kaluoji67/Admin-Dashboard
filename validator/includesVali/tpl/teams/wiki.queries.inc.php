<div class="panel panel-default" style="">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse20" aria-expanded="true">
            Saved Queries</a>
        </h4>
    </div>
    <div id="collapse20" class="panel-collapse collapse in" aria-expanded="true" style=" ">
        <div class="js-top-saved-queries panel-body">

            <?php if(isset($myqueries) && is_array($myqueries)): ?>
                <?php foreach($myqueries as $query_row): 

                    $thisuser = User::getById($query_row['user_id']);
                    ?>
                    <div class="list-group-item"><?=$query_row['query']?><br><a>by: <?=$thisuser->getFullname()?></a></div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
    </div>
</div>