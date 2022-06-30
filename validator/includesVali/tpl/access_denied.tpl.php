<h1>Access denied</h1>

<div class="alert alert-danger col-sm-8" role="alert">
    You are not allowed to do this!
</div>
<div class="clearfix"></div>

<?php if(!isset($user)): ?>
    <form action="index.php?action=login" method="post" role="form" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-2"  for="username">Username</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="username" placeholder="Enter username" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="password">Password</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password" placeholder="Enter password" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-2">
                <button name="submit" type="submit" class="btn btn-default">Sign in</button>
            </div>
        </div>
    </form>
<?php endif; ?>