  <a id="href" href="index.php?action=admin/teams/statistics">
    <button class="btn btn-primary" style="float:right">Back to statistics</button>
  </a>

<?php

    $u = User::getByCondition("usr_id = ?", array($student));
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'submission';
?>
<table class="table table-hover table-striped">
  
  <tr><th>User:</th><td><?=$u[0]->getFullname()?></td><th>Email:</th><td><?=$u[0]->getEmail()?></td></tr>
  <tr><th>Group:</th><td><?=$u[0]->getFullname()?></td><th>Project:</th><td><?=$u[0]->getEmail()?></td></tr>
</table>

<style>
  .nav-tabs li .active{
    border: solid thin #aaa;
  }
</style>
 <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link <?=$tab=='submission'?'active':''?> " href="index.php?action=admin/teams/statistics&user=<?=$u[0]->getId()?>">Submissions</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?=$tab=='chats'?'active':''?> " href="index.php?action=admin/teams/statistics&user=<?=$u[0]->getId()?>&tab=chats">Chats</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?=$tab=='queries'?'active':''?> " href="index.php?action=admin/teams/statistics&user=<?=$u[0]->getId()?>&tab=queries">Queries</a>
    </li>
   <li class="nav-item">
      <a class="nav-link <?=$tab=='errors'?'active':''?> " href="index.php?action=admin/teams/statistics&user=<?=$u[0]->getId()?>&tab=errors">Query errors</a>
    </li>
   
  </ul>