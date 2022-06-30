<div id="statementDialog" class="modal fade" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <?php if(!empty($statement)): ?>
                        Edit Statement <strong>#<?php echo $statement->getId(); ?></strong>
                    <?php else: ?>
                        Create Statement
                    <?php endif; ?>
                </h4>
            </div>

            <script type="text/javascript">
                function loadTemplate(id) {
                    $.ajax({
                        url: "ajax.php?action=statement_tpl&subaction=get&id=" + id,
                        dataType: "json",
                        success: function(json) {
                            if (json.title == "Schema (detailed)")
                                $("#stmt_title").val("Schema");
                            else
                                $("#stmt_title").val(json.title);
                            $("#stmt_desired").val(json.desired);
                            $("#stmt_actual").val(json.actual);
                        }
                    });
                }
            </script>
            <form id="statementForm" action="index.php?action=admin/viewStatements&task=<?php echo $task->getId(); ?>" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="task" value="<?php echo $task->getId(); ?>" />
                <input type="hidden" name="id" value="<?php echo (empty($statement) ? 0 : $statement->getId()); ?>" />
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left">Presets</label>
                        <div class="col-sm-9">
                            <?php foreach(StatementTemplate::getAll() as $stmtt): ?>
                                <a href="#" class="btn" onclick="loadTemplate(<?php echo $stmtt->getId(); ?>)">
                                    <?php echo $stmtt->getTitle(); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="title">Title/Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="title" id="stmt_title">
                                <option value="Table" <?php if($statement && $statement->getTitle() == "table") echo "selected";?>>Table</option>
                                <option value="Constraints" <?php if($statement && $statement->getTitle() == "Constraints") echo "selected";?>>Constraints</option>
                                <option value="Schema" <?php if($statement && $statement->getTitle() == "Schema") echo "selected";?>>Schema</option>
                                <option value="Foreign Keys" <?php if($statement && $statement->getTitle() == "Foreign Keys") echo "selected";?>>Foreign Keys</option>
                            </select>
                           <!-- <input autocomplete="off" name="title" id="stmt_title" type="text" class="form-control"
                                   placeholder="Enter title" value="<?php if($statement) { echo $statement->getTitle(); } ?>" />-->
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="lang">Language</label>
                        <div class="col-sm-9">
                            <?php $lang = $statement ? $statement->getLang() : null; ?>
                            <select name="lang" id="stmt_lang" class="form-control">
                                <option <?php if(is_null($lang)) { echo "selected"; } ?> value="">(all)</option>
                                <option <?php if($lang == 'de') { echo "selected"; } ?> value="de">de</option>
                                <option <?php if($lang == 'en') { echo "selected"; } ?> value="en">en</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="sql_desired">SQL (desired)</label>
                        <div class="col-sm-9">
                            <textarea  autocomplete="off" name="sql_desired" id="stmt_desired" rows="12" class="form-control"><?php if($statement) { echo $statement->getSqlDesired(); } ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="sql_actual">SQL (actual)</label>
                        <div class="col-sm-9">
                            <textarea autocomplete="off" name="sql_actual" id="stmt_actual" rows="12" class="form-control"><?php if($statement) { echo $statement->getSqlActual(); } ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="checkNull">check Null</label>
                        <div class="checkbox col-sm-9">
                            <input type="checkbox" name="checkNull" <?php if ($statement) {if($statement->getChecknull() == "1") echo "checked";}?>>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="checkDefault">check Default</label>
                        <div class="checkbox col-sm-9">
                            <input type="checkbox" name="checkDefault" <?php if ($statement) {if($statement->getCheckdefault() == "1") echo "checked";}?>>
                        </div>
                    </div>
                   <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="checkCase">check Case</label>
                        <div class="checkbox col-sm-9">
                            <input type="checkbox" name="checkCase" <?php if ($statement) {if($statement->getcheckcase() == "1") echo "checked";}?>>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button name="submit" type="submit" class="btn btn-primary">Save Tasks</button>
                </div>
            </form>
        </div>
    </div>
</div>
