<div id="editUserDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $l->getString("edit_users");?> <strong><span class="editUser_username"></span></strong></h4>
            </div>
            <form action="index.php?action=admin/viewUsers" method="post" role="form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_user" />
                    <input type="hidden" class="editUser_username" name="username" value="" />

                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="fullname"><?php echo $l->getString('register_fullname');?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="text" class="form-control" name="fullname" id="editUser_fullname" placeholder="Enter fullname" required />
                        </div>
                    </div>
                    <div class="form-group">
            			<label class="control-label col-sm-3" for="email"><?php echo $l->getString('register_mail'); ?></label>
            			<div>
            				<div class="col-sm-5">
                				<input type="input" class="form-control" name="email" id="editUser_email1" placeholder="Enter e-mail" value="<?php echo @$email; ?>" required />
            				</div>
          					<div class="col-sm-4">
            					<select name="email2" id="editUser_email2" class="form-control">
                					<option value="@st.ovgu.de">@st.ovgu.de</option>
                					<option value="@ovgu.de">@ovgu.de</option>
                				</select> 
            				</div>
            			</div>
       				</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"  for="password"><?php echo $l->getString('login_password'); ?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="password" class="form-control" name="password" id="editUser_password" placeholder="Enter new password or leave empty" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"  for="group"><?php echo $l->getString('register_group'); ?></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="editUser_group" name="group">
                                <option value=""></option>
                                <?php foreach(ExerciseGroup::getByCondition("egrp_sem_id=?",array($_SESSION["sem_id"])) as $g): ?>
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
                    <!-- created on 12.05.17 -->
                    <!-- for the form EditUser the selection of the admin rights -->
                     <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="admin">Admin?</label>
                        <div class="col-sm-9">
                            <select name="admin" id="editUser_admin" class="form-control">
                                <option value="N"><?php echo $l->getString('account_unpreveligedUser'); ?></option>
                                <option value="Y"><?php echo $l->getString('account_admin'); ?></option>
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

    function editUser_submit() {
        username = $("#editUser_username").text();
        fullname = $("#editUser_fullname").val();
        excgroup = $('#editUser_group').val();
        email = $("#editUser_email").val();
        password = $("#editUser_password").val();
        //12.05.17
        admin = $("#editUser_admin").val();

        $.post("ajax.php?action=set_user_data&username=" + username,
            {
                username: username,
                fullname: fullname,
                excgroup: excgroup,
                email: email,
                password: password,
                //12.05.17
                admin: admin
            },
            function(data) {
                $("#editUserDialog").modal('hide');
                location.reload();
            }
        );
    };

    function editUser_open(user) {
        $.getJSON(
            "ajax.php?action=get_user_data&username=" + user,
            function(json) {
                $("#editUser_group option[value='" + json.excgroup + "']").attr('selected', true);
                $("#editUser_password").val("");
                $(".editUser_username").text(json.username);
                $(".editUser_username").val(json.username);
                $("#editUser_fullname").val(json.fullname);
                $("#editUser_email1").val(json.email1);
                $("#editUser_email2").val("@"+json.email2);
                //15.05.17:
                //get the selection which rights (admin, unprivileged) the user has now
                $("#editUser_admin option[value='" + json.admin + "']").attr('selected', true);
                $("#editUserDialog").modal('show');
            }
        ).always(function(d) {
                console.log(d);
            })
    }
</script>