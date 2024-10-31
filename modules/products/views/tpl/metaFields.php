<style type="text/css">
    .toeEfValuesBox {
        display: none;
    }
	/*wordpress set it to some fixed value - don't know why*/
	.toeExtraFieldsSelectList {
		height: 100% !important;
	}
</style>
<script type="text/javascript">
// <!--
<?php if(frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
	jQuery(document).ready(function(){
		jQuery('#toeManageAdditionalFieldsButt').click(function(){
			jQuery.sendForm({
				msgElID: 'toeManageAdditionalFieldsButtMsg'
			,	data: {page: 'products_fields', action: 'getTabContent', reqType: 'ajax', pid: <?php echo $this->post->ID?>}
			,	onSuccess: function(res) {
					if(!res.error) {
						subScreen.show(res.html, 0, 0, function(content){
							subScreen.moveTopCenter()
								.setAbsolute()
								.autoScroll();
						});
					}
				}
			})
			
			return false;
		});
	})
<?php } else {?>
	var toeSelectedEfVals = new Array();
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

		jQuery('.toeEfValuesBox input[name=attr_sort_order]').keyup(function(){
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
		jQuery('.toeExtraFieldsSelectList option').mousedown(function(){
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
	function toeFillInEfVal(parentBox, optId) {
		jQuery(parentBox).find('.toeEfValuesBox input[name=selectedOpts]').val(optId);
		if(jQuery(optId).size() > 1) {  //If more than one value is selected - all inputs will be empty
			var setPrice = true;
			var setAbsolute = true;
			var setActive = 0;
			var setSort = true;
			for(var i = 1; i < optId.length; i++) {
				if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][price]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][price]"]').val())
					setPrice = false;
				if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][sort_order]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][sort_order]"]').val())
					setSort = false;
				if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][price_absolute]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][price_absolute]"]').val())
					setAbsolute = false;
				if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][disabled]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][disabled]"]').val())
					setActive = false;
			}
			if(setPrice)
				jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][price]"]').val() );
			else
				jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val('');
			if(setSort)
				jQuery(parentBox).find('.toeEfValuesBox input[name=attr_sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][sort_order]"]').val() );
			else
				jQuery(parentBox).find('.toeEfValuesBox input[name=attr_sort_order]').val('0');
			if(setAbsolute) {
				if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][price_absolute]"]').val() ))
					jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').attr('checked', 'checked');
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');
			} else
				jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');
			if(setActive) {
				if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][disabled]"]').val() ))
					jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').attr('checked', 'checked');
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
			} else
				jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
		} else {
			jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][price]"]').val() );
			jQuery(parentBox).find('.toeEfValuesBox input[name=attr_sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][sort_order]"]').val() );
			if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId+ '][price_absolute]"]').val() ))
				jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').attr('checked', 'checked');
			else
				jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');

			if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId+ '][disabled]"]').val() ))
				jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').attr('checked', 'checked');
			else
				jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
		}
	}
<?php }?>
// -->
</script>
<div id="product_extras">
    <fieldset class="toeProdFieldset">
        <legend><?php lang::_e('Main Product data')?></legend>
		<?php $i = 0; $itemPerRow = 3;?>
        <?php foreach($this->fields as $f) { ?>
            <?php if ($f->html != 'hidden') {?>
            <div class="product_extra">
                 <label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
                 <div class="product_field"><?php $f->display()?></div>
                 <br clear="all" />
             </div>
			<?php 
				if($i%$itemPerRow == $itemPerRow-1) { ?>
				<br clear="all" />
				<?php }
				$i++;
			?>
        <?php } else  {
             echo $f->viewField($f->name, $this->extra_values[$f->id]);
        }?>
        <?php }?>
    </fieldset>
    <br clear="all" />
	<?php if(frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
		<?php if(frame::_()->getModule('pages')->isProductEdit()) {?>
			<?php echo html::button(array('value' => lang::_('Manage Additional Fields'), 'attrs' => 'class="button button-primary button-hero" id="toeManageAdditionalFieldsButt"'))?>
			<span id="toeManageAdditionalFieldsButtMsg"></span>
		<?php } else {
			lang::_e('You can edit Additional Fields after you save product');
		}?>
	<?php } else {?>
		<fieldset class="toeProdFieldset">
			<legend><?php lang::_e('Extra Product data')?></legend>
			<?php foreach($this->extraFieldsMultiple as $f) { ?>
				 <div class="product_extra">
					<label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
					<div style="">
						<?php lang::_e('Disable')?>: 
						<?php if(frame::_()->getModule('options')->getModel('productfields')->useTextBlock($f->getHtml())) { 
							echo html::checkbox('exOptionsDisabled['. $f->id. ']', array('value' => 1, 'checked' => (int)$f->data['opt_disabled']));
						}?>
					</div>
					<div class="product_field"<?php if(frame::_()->getModule('options')->getModel('productfields')->useTextBlock($f->getHtml())) { echo 'style="float: left; padding: 0 1px 0 0;"'; }?>>
						<?php $f->display()?>
						<?php $desc = $f->getDescription(); ?>
						<?php if(!empty($desc)) { ?>
							<a href="#" class="toeOptTip" tip="<?php echo $desc?>"></a>
						<?php }?>
					</div>
					<?php if($f->getHtml() == 'selectlist') {?>
					<br style="clear: both;" />
					<div class="toeEfValuesBox">
						<table>
							<tr><td><?php lang::_e('Price')?>:</td><td><?php echo html::text('attrs_price')?></td></tr>
							<tr><td><?php lang::_e('Absolute')?>:</td><td><?php echo html::checkbox('price_absolute')?></td></tr>
							<tr><td><?php lang::_e('Disable')?>:</td><td><?php echo html::checkbox('disabled')?></td></tr>
							<tr><td><?php lang::_e('Sort order')?>:</td><td><?php echo html::text('attr_sort_order')?></td></tr>
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
			<?php }?>   
		</fieldset>
	<?php }?>
    <br clear="all" />
</div>
<br clear="all" />
<?php if(!frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
	<h4>
		<a id="add_productfield_popup" href="<?php echo $this->post->ID?>" target="_blank">
			 <?php lang::_e('+ Add New Parameter');?>
		</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo admin_url('admin.php?page=toeoptions#toe_opt_productFieldsTab'); ?>" target="_blank">
			<?php lang::_e('Manage Product Parameters');?>
		</a>
	</h4>
<?php }?>