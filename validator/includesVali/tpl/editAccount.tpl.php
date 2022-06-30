<!-- created on 21.05.17 -->
<!--with this form a user can change the account information
	the form is like the form in viewUser->Edit, but here a no field
	for changing the admin rights -->

<div id="editAccountDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $l->getString('account_edit_header'); ?><strong><span class="editAccount_username"></span></strong></h4>
            </div>
            <form action="index.php?action=account" method="post" role="form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_account" />
                    <input type="hidden" class="editAccount_username" name="username" value="" />

                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="fullname"><?php echo $l->getString('register_fullname'); //Translation already defined in register ?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="text" class="form-control" name="fullname" id="editAccount_fullname" placeholder="<?php echo $l->getString('register_enter_fullname'); ?>" required />
                        </div>
                    </div>
                    <div class="form-group">
            			<label class="control-label col-sm-3" for="email"><?php echo $l->getString('register_mail'); ?></label>
            			<div>
            				<div class="col-sm-5">
                				<input type="input" class="form-control" name="email" id="editAccount_email1" placeholder="<?php echo $l->getString('register_enter_mail'); ?>" value="<?php echo @$email; ?>" required />
            				</div>
          					<div class="col-sm-4">
            					<select name="email2" id="editAccount_email2" class="form-control">
                					<option value="@st.ovgu.de">@st.ovgu.de</option>
                					<option value="@ovgu.de">@ovgu.de</option>
                				</select> 
            				</div>
            			</div>
       				</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"  for="password"><?php echo $l->getString('login_password'); ?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="password" class="form-control" name="password_current" id="editAccount_password" placeholder="<?php echo $l->getString('account_enterPasswordChange'); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"  for="password"><?php echo $l->getString('account_newPassword', 'New Password, Neues Passwort'); ?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="password" class="form-control" name="password_new" id="editAccount_password" placeholder="<?php echo $l->getString('account_enter_newPassword'); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"  for="group"><?php echo $l->getString('register_group'); ?></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="editAccount_group" name="group">
                                <option value=""></option>
                                <?php foreach(ExerciseGroup::getByCondition("egrp_sem_id=?",array($_SESSION["sem_id"])) as $g):?>
                                    <option
                                        <?php if($g->getId() == @$group): ?>
                                            selected="selected"
                                        <?php endif; ?>
                                        value="<?php echo $g->getId(); ?>">
                                        <?php echo $g->getName($lang); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $l->getString('account_edit_close'); ?></button>
                    <button type="submit" name="submit" class="btn btn-primary"><?php echo $l->getString('account_edit_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    function editAccount_submit() {
        username = $("#editAccount_username").text();
        fullname = $("#editAccount_fullname").val();
        excgroup = $('#editAccount_group').val();
        email = $("#editAccount_email").val();
        password = $("#editAccount_password").val();
        
        $.post("ajax.php?action=set_user_data&username=" + username,
            {
                username: username,
                fullname: fullname,
                excgroup: excgroup,
                email: email,
                password: password,
            },
            function(data) {
                $("#editAccountDialog").modal('hide');
                location.reload();
            }
        );
    };

    function editAccount_open(user) {
        $.getJSON(
            "ajax.php?action=get_user_data&username=" + user,
            function(json) {
                $("#editAccount_group option[value='" + json.excgroup + "']").attr('selected', true);
                $("#editAccount_password").val("");
                $(".editAccount_username").text(json.username);
                $(".editAccount_username").val(json.username);
                $("#editAccount_fullname").val(json.fullname);
                $("#editAccount_email1").val(json.email1);
                $("#editAccount_email2").val("@"+json.email2);
                $("#editAccountDialog").modal('show');
            }
        ).always(function(d) {
                console.log(d);
            })
    }
</script>