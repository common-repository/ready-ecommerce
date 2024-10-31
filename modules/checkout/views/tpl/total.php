<div class="toe_gifts">
    <?php if(!empty($this->additionalSubtotalData)) {
        echo $this->additionalSubtotalData;             //Any other additional Subtotal information
    }?>
</div>

<table class="total_table">
    <?php if(isset($this->costs['subTotal'])) { ?>
    <tr>
        <td class="total_table_label"><?php lang::_e('Sub Total')?></td>
        <td><?php echo frame::_()->getModule('currency')->display($this->costs['subTotal'])?></td>
    </tr>
    <?php }?>
    <?php if(isset($this->costs['shipping']) && !empty($this->costs['shipping'])) { ?>
    <tr>
        <td class="total_table_label"><?php lang::_e('Shipping')?></td>
        <td><?php echo frame::_()->getModule('currency')->display($this->costs['shipping'])?></td>
    </tr>
    <?php }?>
    <?php if(isset($this->costs['taxrate']) && !empty($this->costs['taxrate'])) { ?>

        <?php foreach($this->costs['taxrate'] as $t) { if(!is_array($t)) continue;?>
                <tr>
                    <td class="total_table_label"><?php lang::_e($t['label'])?></td>
                    <td><?php echo frame::_()->getModule('currency')->display($t['rate'])?></td>
                </tr>
            <?php }?>
			
    <?php }?>
	<?php echo $this->addTotalRows?>
    <?php if(isset($this->costs['total'])) { ?>
    <tr class="total_table_total_wrapper">
        <td class="total_table_label"><?php lang::_e('Total')?></td>
        <td class="total_coast"><?php echo frame::_()->getModule('currency')->display($this->costs['total'])?></td>
    </tr>
    <?php }?>
</table>
<?php echo html::hidden('total', array('value' => frame::_()->getModule('currency')->calculate($this->total)))?>
<?php echo html::hidden('currency', array('value' => frame::_()->getModule('currency')->getDefaultCode()))?>