<script>
jQuery(document).ready(function(){
	jQuery('.toeExtraFieldsSelectList').change(function(){
		jQuery(this).parents('.product_extra:first').find('.toeEfValuesBox:first').show(TOE_DATA.animationSpeed);
		toeFillInEfVal( jQuery(this).parents('.product_extra:first'), jQuery(this).val() );
	});
	jQuery('.toeEfValuesBox input[name=attrs_price]').keyup(function(){
		var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
		var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
		for(var i = 0; i < selectedOpts.length; i++) {
			jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][price]"]').val( jQuery(this).val() );
		}
	});
	jQuery('.toeEfValuesBox input[name=sort_order]').keyup(function(){
		var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
		var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
		for(var i = 0; i < selectedOpts.length; i++) {
			jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][sort_order]"]').val( jQuery(this).val() );
		}
	});
	jQuery('.toeEfValuesBox input[name=price_absolute], .toeEfValuesBox input[name=disabled]').change(function(){
		var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
		var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
		var checked = jQuery(this).attr('checked');
		var name = jQuery(this).attr('name');
		for(var i = 0; i < selectedOpts.length; i++) {
			if(checked)
				jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ ']['+ name+ ']"]').val( 1 );
			else
				jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ ']['+ name+ ']"]').val( 0 );
		}         
	});
	// Drag-an-drop for product fields options
	jQuery('.toeProdFieldsetVariation').find('.toeExtraFieldsSelectList option').mousedown(function(){
		jQuery(this).attr('clicked', 1);
	}).mouseup(function(){
		jQuery(this).parent('select').find('option').removeAttr('clicked');
	}).mousemove(function(){
		// If current option is not clicked and there are clicked option (mouse is still in down position)
		if(!jQuery(this).attr('clicked') && jQuery(this).parent('select').find('option[clicked=1]').size()) {
			var i = 0,
				currentOpt = jQuery(this),
				currentOptI = 0,
				currentClickedOpt = jQuery(this).parent('select').find('option[clicked=1]'),
				currentClickedOptI = 0;
			// Find where we should move our dragged option - before or after current option
			jQuery(this).parent('select').find('option').each(function(){
				if(currentOpt.val() == jQuery(this).val())
					currentOptI = i;
				else if(currentClickedOpt.val() == jQuery(this).val())
					currentClickedOptI = i;
				i++;
			});
			// Move dragged option
			if(currentOptI > currentClickedOptI) {
				currentOpt.insertBefore(currentClickedOpt);
			} else if(currentOptI < currentClickedOptI) {
				currentOpt.insertAfter(currentClickedOpt);
			}
			if(currentOptI != currentClickedOptI) {
				i = 0;
				jQuery(this).parent('select').find('option').each(function(){
					jQuery('input[name="exVal['+ jQuery(this).val()+ '][sort_order]"]').val( i );
					i++;
				});
			}
		}
	});
});
</script>
<fieldset class="toeProdFieldset toeProdFieldsetVariation">
	<legend><?php lang::_e('Extra Product data')?></legend>
	<?php foreach($this->extraFieldsMultiple as $key => $f) :?>
		<div class="product_extra">
			<label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
			<div style="">
				<?php lang::_e('Disable')?>:
			</div>
			<div class="product_field">
				<?php $f->display()?>
				<?php $desc = $f->getDescription(); ?>
				<?php if(!empty($desc)) { ?>
					<a href="#" class="toeOptTip" tip="<?php echo $desc?>"></a>
				<?php }?>
			</div>
			<?php if($f->getHtml() == 'selectlist') {?>
				<br style="clear: both;" />
				<div class="toeEfValuesBox <?php echo $key;?>">
				<table>
					<tr><td><?php lang::_e('Price')?>:</td><td><?php echo html::text('attrs_price')?></td></tr>
					<tr><td><?php lang::_e('Absolute')?>:</td><td><?php echo html::checkbox('price_absolute')?></td></tr>
					<tr><td><?php lang::_e('Disable')?>:</td><td><?php echo html::checkbox('disabled')?></td></tr>
					<tr><td><?php lang::_e('Sort order')?>:</td><td><?php echo html::text('sort_order')?></td></tr>
				</table>
				<?php echo html::hidden('selectedOpts')?>
				<?php foreach($f->getHtmlParam('options') as $id => $value) {
					echo html::hidden('exValuesToFields['. $id. ']', array('value' => $f->getId()));
					foreach($this->exFieldsEfVals[$id] as $efVal) {
						$efVal->display();
					}
				}?>
				</div>
			<?php }?>
			<br clear="all" />
		</div>
	<?php endforeach; ?>   
</fieldset>
<br clear="all" />