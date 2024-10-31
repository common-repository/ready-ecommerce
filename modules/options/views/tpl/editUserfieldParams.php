<?php 
    $optionsDisplay = 'display: none;';
    $options = array();
    switch($this->userfields['htmltype_id']->getValue()) {
        case 5: case 9: case 10: case 12:       //Checkboxes, Drop Down, Radio Buttons, List
            $options = frame::_()->getModule('options')->getModel('extraoptions')->get(array('ef_id' => (int) $this->userfields['id']));
            $optionsDisplay = '';
            break;
        default:
            break;
    }
?>
<div class="options options_tag" style="<?php echo $optionsDisplay?>">
    <span class="add_option" id="toeAddUserfieldOpt"><?php lang::_e('Add Option')?></span>
    <?php foreach($options as $o) { ?>
        <p>
            <?php lang::_e('Value')?>:  <?php echo html::text('params[options][value][]', array('value' => $o['value']))?>
            <span class="delete_option"></span>
        </p>
    <?php }?>
</div>
