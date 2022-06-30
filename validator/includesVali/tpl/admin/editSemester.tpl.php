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

<h1><?php echo ($edit?"Edit semester $semester":"Add new Semester");?></h1>
<?php if(!empty($errors)): ?>
    <div class="alert alert-danger col-sm-8" role="alert">
            <strong>We're sorry, but some errors occurred. Please fix them and try again! </strong>
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?php echo $e; ?></li>
                <?php endforeach; ?>
            </ul>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>

<form action="index.php?action=admin/editSemester<?php echo ($edit?"&editSemId=$id":"");?>" method="post" role="form" class="form-horizontal">
    <?php if($edit):?>
        <input name='semid' style='display:none;' value='<?php echo $id;?>'/>
    <?php endif;?>
    <div class="form-group">
        <label class="control-label col-sm-2 text-left" for="semester">Semester</label>
        <div class="col-sm-6">
            <input type="input" class="form-control" name="semester" placeholder="Enter semester" value="<?php echo @$semester; ?>" required />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2 text-left" for="passphrase">Passphrase</label>
        <div class="col-sm-6">
            <input type="input" class="form-control" name="passphrase" placeholder="Enter passphrase" value="<?php echo @$passphrase; ?>" required />
        </div>
    </div>
        
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button name="submit" type="submit" class="btn btn-default">Save semester</button>
            <?php if($edit):?>
                <button name="delete" type="submit" style="float:right;" class="btn btn-default">Delete semester</button>
            <?php endif;?>
        </div>
    </div>
</form>