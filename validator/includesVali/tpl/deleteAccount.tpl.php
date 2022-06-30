<!-- created on 22.05.2017 -->
<!--the form is a query, whether the account should be delete or not 
	therefore exists two buttons, one for the deletion of the account and
	the other for closing -->

<div id="deleteAccountDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $l->getString('account_delete_header','Delete Account | Account Löschen'); ?><strong><span> <?php echo $user->getName(); ?></span></strong></h4>
            </div>
            <form action="index.php?action=account" method="post" role="form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete_user" />
                    <input type="hidden" class="deleteAccount_username" name="username" value="" />
                    <input type="hidden" name="deleteAccount_id" id="deleteAccount_id" value=""/>
                    <h4> <?php echo $l->getString('account_delete_message','Would you like to delete your account? | Möchtest du deinen Account wirklich löschen?'); ?></h4>
                </div>
                <div class="form-group">
                        <label class="control-label col-sm-3"  for="password"><?php echo $l->getString('login_password');?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" type="password" class="form-control" name="password_current" id="editAccount_password" placeholder="<?php echo $l->getString('account_delete_pwdmessage','Enter current password for the deletion of your account|Bestätige die Löschung mit deinem aktuellen Passwort');?>" />
                        </div>
                    </div>
                <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $l->getString('account_edit_close');?></button>
                      <button type="submit" name="delete" class="btn btn-default"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span><?php echo $l->getString('account_delete');?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    
    function deleteAccount_open(user) {
        $.getJSON(
            "ajax.php?action=get_user_data&username=" + user,
            function(json) {
                $(".deleteAccount_username").text(json.username);
                $(".deleteAccount_username").val(json.username);
                $("#deleteAccount_id").val(json.id);
                $("#deleteAccountDialog").modal('show');
            }
        ).always(function(d) {
                console.log(d);
            })
    }
</script>