<h1><?php lang::_e(array(($this->method == 'post' ? 'Add' : 'Edit'), 'Userfields'))?></h1>
<form action="<?php echo uri::mod('options', '', '', array('id' => $this->id))?>" method="post" id="editUserfieldForm">
    <table width="100%">
    <?php
        $tag = $this->userfields['htmltype_id']->value;
        foreach($this->userfields as $k => $f) {
            if(!utils::is($f, 'field') || $f->html == 'hidden' || in_array($f->name, array('id'))) continue; ?>
        <tr>
            <td class="field_label">
            <?php if ($f->html != 'hidden'){
                    echo $f->label.':';
                } else {
                    echo '&nbsp;';
                } ?>
                <?php if ($f->description != ''):?>
                    <a href="#" class="toeOptTip" tip="<?php lang::_e($f->description)?>"></a>
                <?php endif;?>
            </td>
            <td>
                <?php 
                    $displayed = false;
                    switch($f->getName()) {
                        case 'htmltype_id':
                            $f->addHtmlParam('options', $this->userfields['type_labels']);
                            $f->addHtmlParam('attrs', 'class="extrafield_type" rel="editUserfieldForm"');
                            break;
                        case 'params':
                            echo $this->paramsHtml;
                            $displayed = true;
                            break;
                    }
                    if(!$displayed)
                        $f->display($tag, $this->id);
                ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <?php foreach($this->userfields as $k => $f) {
                    if(utils::is($f, 'field') && $f->html == 'hidden') {
                        $f->display();
                    }
                }?>
                <?php echo html::hidden('id', array('value' => $this->id))?>
                <?php echo html::hidden('action', array('value' => $this->method. 'Userfield'))?>
                <?php echo html::hidden('page', array('value' => 'options'))?>
                <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                <?php echo html::button(array('attrs' => 'class="button button-primary"', 'value' => lang::_('Save')))?>
            </td>
            <td id="mod_msg_user" class="mod_msg"></td>
        </tr>
    </table>
</form>