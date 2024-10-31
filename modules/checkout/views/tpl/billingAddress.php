<table>
<?php
if($this->billingUserMeta) {
    foreach($this->billingUserMeta as $f) { ?>
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
    <?php }
}
?>
</table>
<?php /*if($this->showSameAsButton) {?>
<div><?php echo html::inputButton(array('value' => lang::_('Same As Shipping'), 'attrs' => 'id="billing_"'))?></div>
<?php }*/?>