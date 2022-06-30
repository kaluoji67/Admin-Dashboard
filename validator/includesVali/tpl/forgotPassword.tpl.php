<!-- created on 14.06.17 -->

<h1><?php echo $l->getString("forgot_password"); ?></h1>

<!-- show the errors, if some exist -->
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

<?php $id = (isset($_GET["id"])? $_GET["id"]:null);?>
<?php
    if(!is_null($id))
    {
        $uid=substr($id,16);
        $fid=substr($id,0,16);
        $user_temp=User::getById($uid);
        if((!$user_temp)||(strcmp($user_temp->getForgotId(),$fid)!=null))
        {
            $id=null;
        }
    }
?>

<!-- show the fields, if the id is set show additionally to username and email also password an password repetition -->
<form action="index.php?action=forgotPassword" method="post" role="form" class="form-horizontal">
    <?php if(is_null($id)):?>
	    <div class="form-group">
	        <label class="control-label col-sm-2 text-left" for="username">Username</label>
	        <div class="col-sm-6">
	            <input type="input" class="form-control" name="username" placeholder="Enter username" value="<?php echo @$username; ?>"/>
	        </div>
	    </div>
	    <div class="form-group">
	        <label class="control-label col-sm-2 text-left">or</label>
	        <div class="col-sm-6">
	        </div>
	    </div>
	    <div class="form-group">
	        <label class="control-label col-sm-2 text-left" for="email">E-Mail</label>
	        <div class="col-sm-6">
	            <input type="email" class="form-control" name="email" placeholder="Enter e-mail" value="<?php echo @$email; ?>"/>
	        </div>
	    </div>
    <?php elseif(!is_null($id)):?>
	    <input name="id" value="<?php echo (isset($_GET["id"])? $_GET["id"]:"");?>" style="display:none;"/>
	    <div class="form-group">
	        <label class="control-label col-sm-2 text-left" for="password">Password</label>
	        <div class="col-sm-6">
	            <input type="password" class="form-control" name="password" placeholder="Enter password" value="<?php echo @$password; ?>" required />
	        </div>
	    </div>
	    <div class="form-group">
	        <label class="control-label col-sm-2 text-left" for="password_repetition">Password (repetition)</label>
	        <div class="col-sm-6">
	            <input type="password" class="form-control" name="password_repetition" placeholder="Enter password again" value="<?php echo @$password_repetition; ?>" required />
	        </div>
	    </div>
    <?php endif;?>
    
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button name="submit" type="submit" class="btn btn-default"><?php echo ((!is_null($id))? 'Reset':'Get Email');?></button>
        </div>
    </div>
</form>
