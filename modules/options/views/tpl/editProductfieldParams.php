<?php 
    $optionsDisplay = 'display: none;';
    $options = array();
    switch($this->productfields['htmltype_id']->getValue()) {
        case 5: case 9: case 10: case 12:       //Checkboxes, Drop Down, Radio Buttons, List
            $options = frame::_()->getModule('options')->getModel('extraoptions')->get(array('ef_id' => (int) $this->productfields['id']));
            $optionsDisplay = '';
            break;
        default:
            break;
    }
?>
<div class="options options_tag" style="<?php echo $optionsDisplay?>">
    <span class="add_option"><?php lang::_e('Add Option')?></span>
    <?php foreach($options as $o) { ?>
        <p>
            <?php lang::_e('Value')?>:  <?php echo html::text('params[options][value]['. $o['id']. ']', array('value' => $o['value']))?>
			<br />
			<?php lang::_e('Sort Order')?>:  <?php echo html::text('params[options][sort_order]['. $o['id']. ']', array('value' => $o['sort_order'], 'attrs' => 'style="width: 30px;"'))?>
			<?php lang::_e('Price')?>:  <?php echo html::text('params[options][price]['. $o['id']. ']', array('value' => $o['price'], 'attrs' => 'style="width: 70px;"'))?>
			,&nbsp;
			<?php lang::_e('absolute')?>:  <?php echo html::checkbox('params[options][price_absolute]['. $o['id']. ']', array('checked' => (bool)$o['price_absolute']))?>
			<span class="delete_option"></span>
        </p>
    <?php }?>
</div>
