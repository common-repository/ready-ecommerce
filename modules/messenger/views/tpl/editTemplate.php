<h1><?php lang::_e('Edit Template')?></h1>
<form action="<?php echo uri::mod('options', '', '', array('id' => $this->id))?>" method="post" id="editTemplateForm">
    <table width="100%">
    <?php
        $tag = isset($this->template['htmltype_id']) ? $this->template['htmltype_id']->value : '';
        foreach($this->template as $k => $f) {
            if($k == 'types' || $k == 'type_labels') continue; ?>
        <tr>
            <td class="field_label">
                <?php if ($f->html != "hidden") {
                        echo $f->label.':';
                    } else {
                        echo '&nbsp;';
                    }?>
                <?php if ($f->description != ''):?>
                    <a href="#" class="toeOptTip" tip="<?php echo $f->description;?>"></a>
                <?php endif;?>
            </td>
            <td>
                <?php $f->display($tag, $this->id);?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <?php echo html::hidden('id', array('value' => $this->id))?>
                <?php echo html::hidden('action', array('value' => $this->method. 'Template'))?>
                <?php echo html::hidden('page', array('value' => 'messenger'))?>
                <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                <?php echo html::button(array('attrs' => 'class="button button-primary"', 'value' => lang::_('Save')))?>
            </td>
            <td id="mod_msg_template" class="mod_msg"></td>
        </tr>
    </table>
</form>