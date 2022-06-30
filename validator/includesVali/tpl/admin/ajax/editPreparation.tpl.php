<div id="preparationDialog" class="modal fade" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <?php if(!empty($tskp)): ?>
                        Edit Preparations <strong>#<?php echo $tskp->getId(); ?></strong>
                    <?php else: ?>
                        Create Preparations
                    <?php endif; ?>
                </h4>
            </div>

            <script type="text/javascript">
                function loadTemplate(id) {
                    $.ajax({
                        url: "ajax.php?action=preparation_tpl&subaction=get&id=" + id,
                        dataType: "json",
                        success: function(json) {
                            $("#tskp_title").val(json.title);
                            $("#tskp_sql").val(json.sql);
                        }
                    });
                }
            </script>
            <form id="statementForm" action="index.php?action=admin/viewPreparation&task=<?php echo $task->getId(); ?>" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="task" value="<?php echo $task->getId(); ?>" />
                <input type="hidden" name="id" value="<?php echo (empty($tskp) ? 0 : $tskp->getId()); ?>" />
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left">Presets</label>
                        <div class="col-sm-9">
                            <?php foreach(TaskPreparationTemplate::getAll() as $tskpt): ?>
                                <a href="#" class="btn" onclick="loadTemplate(<?php echo $tskpt->getId(); ?>)">
                                    <?php echo $tskpt->getTitle(); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="title">Language</label>
                        <div class="col-sm-9">
                        	<!-- created on 16.05.17:
                        			get the actual selection of the language in the Edit-Form-->
                        	<?php $lang = $tskp ? $tskp->getLang() : null; ?>
                            <select name="lang" id="tskp_lang" class="form-control">
                                <option <?php if(is_null($lang)) { echo "selected"; } ?> value="">(all)</option>
                                <option <?php if($lang == 'de') { echo "selected"; } ?> value="de">de</option>
                                <option <?php if($lang == 'en') { echo "selected"; } ?> value="en">en</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left sql-input" for="sql">SQL</label>
                        <div class="col-sm-9">
                            <textarea autocomplete="off" name="sql" id="tskp_sql" rows="12" class="form-control"><?php if($tskp) { echo $tskp->getSql(); } ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button name="submit" type="submit" class="btn btn-primary">Save Preparation</button>
                </div>
            </form>
        </div>
    </div>
</div>
