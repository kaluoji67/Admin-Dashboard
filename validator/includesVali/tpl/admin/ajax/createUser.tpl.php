<div id="createUserDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $l->getString('create_user'); ?></h4>
            </div>
            <form action="index.php?action=admin/viewUsers" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="action" value="create_user" />
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="username"><?php echo $l->getString('login_username'); ?></label>
                        <div class="col-sm-9">
                            <input name="username" type="text" class="form-control" placeholder="Enter username" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="password"><?php echo $l->getString('login_password'); ?></label>
                        <div class="col-sm-9">
                            <input name="password" type="password" class="form-control" placeholder="Enter password" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="fullname"><?php echo $l->getString('register_fullname'); ?></label>
                        <div class="col-sm-9">
                            <input name="fullname" type="text" class="form-control" placeholder="Enter fullname" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="email"><?php echo $l->getString('register_mail'); ?></label>
                        <div>
            				<div class="col-sm-5">
                				<input type="input" class="form-control" name="email" id="editUser_email1" placeholder="Enter e-mail" value="<?php echo @$email; ?>" required />
            				</div>
          					<div class="col-sm-4">
            					<select name="email2" id="email2" class="form-control">
                					<option value="@st.ovgu.de">@st.ovgu.de</option>
                					<option value="@ovgu.de">@ovgu.de</option>
                				</select> 
            				</div>
            			</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="group"><?php echo $l->getString('register_group'); ?></label>
                        <div class="col-sm-9">
                            <select name="group" class="form-control">
                                <?php foreach(ExerciseGroup::getAll() as $g): ?>
                                    <option value="<?php echo $g->getId(); ?>"><?php echo $g->getName($lang); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="admin">Admin?</label>
                        <div class="col-sm-9">
                            <select name="admin" class="form-control">
                                <option value="N"><?php echo $l->getString('account_unpreveligedUser'); ?></option>
                                <option value="Y"><?php echo $l->getString('account_admin'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $l->getString('account_edit_close'); ?></button>
                    <button name="submit" type="submit" class="btn btn-primary"><?php echo $l->getString('account_edit_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function createUser_show() {
        $("#createUserDialog").modal('show');
    }
</script>