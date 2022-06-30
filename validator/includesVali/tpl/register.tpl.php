<h1><?php echo $l->getString('register'); ?></h1>
<?php if(!empty($errors)): ?>
    <div class="alert alert-danger col-sm-8" role="alert">
            <strong><?php echo $l->getString('register_error'); ?></strong>
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?php echo $e; ?></li>
                <?php endforeach; ?>
            </ul>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>

<form action="index.php?action=register" method="post" role="form" class="form-horizontal">
    <span <?php echo (isset($selectGroup)? "style='display:none;'":"");?>>
        <div class="form-group">
            <label class="control-label col-sm-2 text-left" for="username"><?php echo $l->getString('login_username'); ?></label>
            <div class="col-sm-6">
                <input type="input" class="form-control" name="username" placeholder="<?php echo $l->getString('login_enter_username'); ?>" value="<?php echo @$username; ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2 text-left" for="username"><?php echo $l->getString('register_fullname'); ?></label>
            <div class="col-sm-6">
                <input type="input" class="form-control" name="fullname" placeholder="<?php echo $l->getString('register_enter_fullname'); ?>" value="<?php echo @$fullname; ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2 text-left" for="email"><?php echo $l->getString('register_mail'); ?></label>
            <div>
	            <div class="col-sm-3">
	                <input type="input" class="form-control" name="email" placeholder="<?php echo $l->getString('register_enter_mail'); ?>" value="<?php echo @$email; ?>" required />
	            </div>
	          	<div class="col-sm-3">
	            	<select name="email2" id="email2" class="form-control">
	                	<option value="@st.ovgu.de">@st.ovgu.de</option>
	                	<option value="@ovgu.de">@ovgu.de</option>
	                </select> 
	            </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="password"><?php echo $l->getString('login_password'); ?></label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password" placeholder="<?php echo $l->getString('login_enter_password'); ?>" value="<?php echo @$password; ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="password"><?php echo $l->getString('register_password_repitition'); ?></label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password2" placeholder="<?php echo $l->getString('register_enter_password_repitition'); ?>" value="<?php echo @$password2; ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="semester">Semester</label>
            <div class="col-sm-6">
                <select class="form-control" id="semester" name="semester" onchange="semesterChanged()">
                    <?php foreach(Semester::getAll() as $s): ?>
                        <option
                            <?php if($s->getId() == @$semester): ?>
                                selected="selected"
                            <?php endif; ?>
                            value="<?php echo $s->getId(); ?>">
                                <?php echo $s->getDescr(); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="passphrase">Passphrase</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="passphrase" placeholder="<?php echo $l->getString('register_enter_passphrase'); ?>" value="<?php echo @$passphrase; ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="language"> <?php echo $l->getString("register_language") ?> </label>
            <div class="col-sm-6">
                <select class="form-control" id="language" name="language">
                    <option value="en" <?php if ($l->getLanguage() == "en") echo "selected"; ?>>English</option>
                    <option value="de" <?php if ($l->getLanguage() == "de") echo "selected"; ?>>Deutsch</option>
                </select>
            </div>
        </div>
    </span>
    
    <?php if(isset($selectGroup)):?>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="group"><?php echo $l->getString('register_group'); ?></label>
            <div class="col-sm-6">
                <select class="form-control" id="group" name="group">
                    <?php foreach(ExerciseGroup::getByCondition("egrp_sem_id=?",array($semester)) as $g): ?>
                        <option
                            <?php if($g->getId() == @$group): ?>
                                selected="selected"
                            <?php endif; ?>
                            value="<?php echo $g->getId(); ?>"
                            name="option<?php echo $g->getSemId(); ?>">
                                <?php echo $g->getName($lang); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif;?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button name="submit" type="submit" class="btn btn-default"><?php echo (isset($selectGroup)? $l->getString('register_signUp'):$l->getString('register_continue'));?></button>
        </div>
    </div>
</form>