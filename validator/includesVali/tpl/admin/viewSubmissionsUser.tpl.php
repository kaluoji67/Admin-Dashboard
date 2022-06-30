<!-- created on 20.06.17 -->
<!-- on this page the submissions of the user are shown,
	 this page open via link "ViewSubmission" in viewUser  -->

<h1><?php echo $l->getString('view_submissions'); ?> User</h1>

<form action="index.php?action=admin/viewSubmissionsUser" class="form-horizontal" role="form" method="post">
    <div class="form-group">
        <?php if (isset($_GET["userId"])) {

            $user = User::getById($_GET["userId"]);
            $submissions = false;
            ?>
            <h4>User: <?php echo $user->getName(); ?></h4>
            <h4>UserID: <?php echo $user->getId(); ?></h4>
            <?php
            $queriesForUser = UserQuery::getUserQueries($user->getId());
            foreach ($queriesForUser as $userQuery){
                $task = Task::getById($userQuery->getTskId());
                if ($task != null)
                    echo "<h4>Task <strong>".$task->getId()."</strong> (".$task->getTitle($lang).")";
                if ($userQuery->getSuccess() == 'Y'): ?>
                    <span class="glyphicon glyphicon-ok"></span>
                <?php else: ?>
                    <span class="glyphicon glyphicon-remove"></span>
                <?php endif; ?>
                    </h4>
                    <pre><?php echo $userQuery->getSql(); ?></pre>
            <?php
            }
            if (count($queriesForUser) == 0) { ?>
                <h4>There are no submissions for this user.</h4>
            <?php }
        } ?>

    </div>
</form>
