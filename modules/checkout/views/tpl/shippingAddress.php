<?php
if($this->shippingUserMeta) { ?>
<div id="shippingSameAsBillingIndicator" style="display: none;"><?php lang::_e('Items will be shipped to the billing address')?></div>
<table style="display:block;">
    <?php foreach($this->shippingUserMeta as $f) { ?>
    <tr<?php if($f->getHtml() == 'hidden') {?> style="display: none;"<?php }?>>
        <td>
            <?php 
                if(in_array($f->getHtml(), array('text', 'textarea', 'statesList'))) {
                    $f->addHtmlParam('attrs', ' placeholder="'. $f->label. '" ');
                }
                $f->{ $this->displayMethod }();
            ?>
        </td>
    </tr>
    <?php } ?>
</table>
<?php }?>
<?php if($this->showSameAsButton) {?>
<div>
	<?php echo html::checkbox('shippingSameAsBilling', array('attrs' => 'id="toeShippingSameAsBilling"'));?>
	<?php lang::_e('Ship items to the billing address')?>
	<?php //echo html::inputButton(array('value' => lang::_('Same As Billing'), 'attrs' => 'id="shipping_"'))?>
</div>
<?php }?>