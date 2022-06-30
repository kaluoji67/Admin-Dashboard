<?php 
        if((!isset($deleted))&&((isset($_GET['editSemId']))||(isset($_POST['semid']))))
        {
            $id=(isset($_GET['editSemId'])?$_GET['editSemId']:$_POST['semid']);
            $editSem = Semester::getById($id);
            if($editSem)
            {
                $semester=$editSem->getDescr();
                $passphrase=$editSem->getPassphrase();
                $edit=true;
            }
            else 
            {
                $edit=false;
            }
        }
        else 
        {
            $edit=false;
        }
?>

<h1>Change viewed semester</h1>
<div class="mb-4">
    Here you can change the semester that is set as active when you are logging in to the validator.
    So you can set it to your active semester you are working on as a tutor for example.
    <br> If you are registered within an exercise group this registration will be deleted.
</div>
<?php if(!empty($errors)): ?>
    <div class="alert alert-danger col-sm-8" role="alert">
            <strong>We're sorry, but some errors occurred. Please fix them and try again! </strong>
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?php echo $e; ?></li>
                <?php endforeach; ?>
            </ul>
    </div>
<?php elseif (isset($errors)):?>
    <div class="alert alert-success">
        <?php echo  $l->getString("questionnaire_success_edit");?>
    </div>

<?php endif; ?>

<form action="index.php?action=admin/editPreferredSemester" method="post" role="form" class="form-horizontal">
    <div class="form-group">
        <label class="control-label col-sm-2 text-left" for="semester">Semester</label>
        <div class="col-sm-6">
            <select class="form-control" name="semid">
                <?php foreach (Semester::getAll() as $sem):?>
                    <option value='<?php echo $sem->getId()?>' <?php echo ($_SESSION["sem_id"]==$sem->getId()? "selected":"")?>><?php echo $sem->getDescr()?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
        
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button name="submit" type="submit" class="btn btn-default">Change start semester</button>
        </div>
    </div>
</form>