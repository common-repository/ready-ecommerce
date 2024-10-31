<script type="text/javascript">
// <!--
    jQuery(function() {
        jQuery('#editModuleForm').submit(function(){
            jQuery(this).sendForm({msgElID: 'mod_msg', onSuccess: function(res) {
                    if(toeTables['modules_list_payment']) {
                        toeTables['modules_list_payment'].redrawRow('id', res.data.id, res.data);
						toeShowModuleEditForm(res.data.id);
                    }
                }}
            );
            return false;
        });
    });
// -->
</script>
<h1><?php lang::_e(array('Edit', 'module'))?></h1>
<form action="<?php echo uri::mod('options', '', '', array('id' => $this->id))?>" method="post" id="editModuleForm">
    <table width="100%">
    <?php
        $module = $this->moduleFields['code']->value;
        foreach($this->moduleFields as $k => $f) {
            if($k == 'types') continue; ?>
        <tr>
            <td><?php if ($f->html != 'hidden'){
                    echo $f->label.':';
                } else {
                    echo '&nbsp;';
                }
                ?></td>
            <td>
                <?php 
                    $params = array('value' => $f->value);
                    if($f->name == 'type_id') {
                        $f->addHtmlParam('options', $this->moduleFields['types']);
                    } elseif($f->name == 'active') {
                        $f->addHtmlParam('checked', $f->value);
                    }
                    if ($f->name == 'params') {
                        $f->displayConfig($module);
                    } else {
                        $f->display();
                    }
                ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <?php echo html::hidden('id', array('value' => $this->id))?>
                <?php echo html::hidden('action', array('value' => 'putModule'))?>
                <?php echo html::hidden('page', array('value' => 'options'))?>
                <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                <?php echo html::submit(lang::_('Save'), array('value' => 'save', 'attrs' => 'class="button button-primary"'))?>
            </td>
            <td id="mod_msg"></td>
        </tr>
    </table>
</form>