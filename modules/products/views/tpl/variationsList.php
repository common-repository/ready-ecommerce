<?php
	$hideEmpty = $this->haveVariations ? '' : 'display:none;';
?>
<style type="text/css">
	.toeVariationsMeta {
		display:none;
	}
	.toeMetaHref {
		cursor: pointer;
		border-right: 1px solid;
		border-left: 1px solid;
		margin-right: 10px;
		padding-left: 10px;
		padding-right: 10px;
	}
	#toeVariationsFile .upload_file {
		width:260px;
		float:left;
		margin-right: 15px;
	}
	#toeVariationsFile h4 {
		clear:both;
	}
</style>
<script type="text/javascript">
// <!--
	var toeSelectedEfVals = new Array();
	function toeCheckAllVaritionsBoxesHidden() {
		var all_hidden = !jQuery('#toeVariationsExtraData, #toeVariationsMainData, #toeVariationsFilesList').is(':visible');
		if(all_hidden) {
			jQuery('#toeHideShowAll').find('.toeHideLinkPart').html(toeLang('Show'));
		} else {
			jQuery('#toeHideShowAll').find('.toeHideLinkPart').html(toeLang('Hide'));
		}
	}
    jQuery(document).ready(function(){
		jQuery('#toeVariationsSaveForm').submit(function(){
			jQuery(this).sendForm({
				msgElID: 'toeVariationsSaveMsg',
				onSuccess: function(res) {
					if(!res.error) {
						var varName = jQuery('#toeVariationsList input[name="var_name"]').val();
						switch(res.data.method) {
							case 'add':
								jQuery('#toeVariationsSelect').append('<option value="'+ res.data.var_id+ '">'+ varName+ '</option>');
								break;
							case 'edit':
							default:
								jQuery('#toeVariationsSelect option[value='+ res.data.var_id+ ']').html(varName);
								break;
						}
					}
				}
			});
			return false;
		});
		jQuery('.toeAddButton').click(function(){
			jQuery('input[name="pid"]').val(0);
			jQuery('.toeVariationsMeta:first').hide(TOE_DATA.animationSpeed);
			jQuery('.toeVariationsMeta input').each(function(){ if(jQuery(this).attr("type")!="file") jQuery(this).val(0);} );
			jQuery('#toeVariationsList input[name="var_name"]').show().val('');
			jQuery('#toeVariationsList input[name="var_sort_order"]').show().val('');
			jQuery('#toeVariationsSelect option[value="0"]').attr('selected', 'selected');
			jQuery('#toeVariationsExtraData .toeProdFieldset').html('<legend><?php lang::_e('Extra Product data')?></legend>');
			jQuery('#toeVariationsFile').html('');
			jQuery('.toeSaveButton').show();
			jQuery('.toeDeleteButton').hide();
			jQuery('.toeVariationsMeta:first').show(TOE_DATA.animationSpeed);
			return false;
		});
		jQuery('#toeHideShowExtraData').click(function(){
			jQuery('#toeHideShowExtraData').find('.toeHideLinkPart').html(jQuery('#toeVariationsExtraData').is(':visible') ? toeLang('Show') : toeLang('Hide'));
			jQuery('#toeVariationsExtraData').toggle(TOE_DATA.animationSpeed, toeCheckAllVaritionsBoxesHidden);
			//toeCheckAllVaritionsBoxesHidden();
			return false;
		});
		jQuery('#toeHideShowProductData').click(function(){
			jQuery('#toeHideShowProductData').find('.toeHideLinkPart').html(jQuery('#toeVariationsMainData').is(':visible') ? toeLang('Show') : toeLang('Hide'));
			jQuery('#toeVariationsMainData').toggle(TOE_DATA.animationSpeed, toeCheckAllVaritionsBoxesHidden);
			//toeCheckAllVaritionsBoxesHidden();
			return false;
		});
		jQuery('#toeHideShowFiles').click(function(){
			jQuery('#toeHideShowFiles').find('.toeHideLinkPart').html(jQuery('#toeVariationsFilesList').is(':visible') ? toeLang('Show') : toeLang('Hide'));
			jQuery('#toeVariationsFilesList').toggle(TOE_DATA.animationSpeed, toeCheckAllVaritionsBoxesHidden);
			//toeCheckAllVaritionsBoxesHidden();
			return false;
		});
		jQuery('#toeHideShowAll').click(function(){
			var all_hidden = !jQuery('#toeVariationsExtraData, #toeVariationsMainData, #toeVariationsFilesList').is(':visible');
			if(jQuery(this).attr('all_hidden') || all_hidden) {
				jQuery('#toeVariationsExtraData, #toeVariationsMainData, #toeVariationsFilesList').show(TOE_DATA.animationSpeed);
				jQuery('.toeHideLinkPart').html(toeLang('Hide'));
				jQuery(this).removeAttr('all_hidden');
			} else {
				jQuery('#toeVariationsExtraData, #toeVariationsMainData, #toeVariationsFilesList').hide(TOE_DATA.animationSpeed);
				jQuery('.toeHideLinkPart').html(toeLang('Show'));
				jQuery(this).attr('all_hidden', '1');
			}
			return false;
		});
		jQuery('.toeSaveButton').click(function(){
			// Savebutton
		});
		jQuery('.toeDeleteButton').click(function(){
			var name = jQuery('#toeVariationsSelect :selected').text();
			var id = jQuery('#toeVariationsSelect :selected').attr('value');
			if(id==0)
				return false;
			var res = confirm("<?php lang::_e('Delete Variation')?> '"+name+"'?");
			if (res) {
				jQuery("#toeVariationsDeleteForm input[name='postid']").val(id);
				jQuery("#toeVariationsDeleteForm").sendForm({
					onSuccess: function(res){
						if(!res.error) {
							jQuery('#toeVariationsSelect option[value="'+ id+ '"]').remove();
							jQuery('#toeVariationsSelect option[value="0"]').attr('selected', 'selected').change();
						}
					}
				});
			}
		});
		jQuery('#toeVariationsSelect').change(function(){
			var id = jQuery('#toeVariationsSelect :selected').attr('value');
			jQuery('.toeDeleteButton, .toeSaveButton, #toeVariationsList input[name="var_name"], #toeVariationsList input[name="var_sort_order"]').hide();
			if(id==0) {
				jQuery('.toeVariationsMeta:first').hide(TOE_DATA.animationSpeed);
				return false;
			}
			jQuery('#toeVariationsUpdateForm input[name="var_id"]').val(id);
			jQuery('#toeVariationsUpdateForm').sendForm({
				onSuccess: function(res){
					if(res.html) {
						jQuery('.toeVariationsMeta:first').hide(TOE_DATA.animationSpeed);
						jQuery('.toeVariationsMeta:first').show(TOE_DATA.animationSpeed);
						jQuery('.toeVariationsMeta').html(res.html);
					} else if(res.data.variation) {
						jQuery('.toeVariationsMeta:first').hide(TOE_DATA.animationSpeed);
						jQuery('.toeVariationsMeta:first').show(TOE_DATA.animationSpeed);
						for (var i in res.data.variation) {
							jQuery('#toeVariationsList input[name=\''+i+'\']').val(res.data.variation[i]);
						}
						jQuery('input[name="pid"]').val(id);
						jQuery("#toeVariationsExtraData").html(res.data.extraFields);
					} else {
						jQuery('.toeVariationsMeta:first').hide(TOE_DATA.animationSpeed);
						jQuery('input[name="pid"]').val(0);
					}
					if(res.data.downloads) {
						jQuery('#toeVariationsFile').html(res.data.downloads);
						var name = 'userfile['+id+'][]';
						jQuery('#toeVariationsFile #first_raw').find('.uploader').attr('name',name);
						jQuery('#post').attr('enctype','multipart/form-data');
						jQuery('.delete_product_file').click(function(){
							var link = jQuery(this); 
							if (confirm(toeLang('Are you sure you want to delete this file?'))) {
								var id = link.attr('rel');
								var data = {
									id : id,
									action: 'digital_delete'
							   };
							   jQuery.post(ajaxurl, data, function(){
									link.parent().remove();
							   });
							} 
						});
						jQuery('#toeVariationsFile [name="save"]').click(function() { 
							var id = jQuery('#toeVariationsSelect :selected').attr('value');
							var name = 'userfile['+id+'][]';
							jQuery('#toeProductDownloads').find('[name^="product_ftp_urls"]').remove();
							jQuery('#toeProductDownloads').find('[name^="product_ftp_urls"]').remove();
							if(jQuery('#toeVariationsFile .upload_file').length > 0) {
								jQuery('#toeVariationsFile .upload_file').each(function(){
									jQuery(this).appendTo('#toeProductDownloads');
								});
								jQuery('#toeProductDownloads').append('<input type="hidden" name="save_variation_files" value="'+ id+ '" />');
								jQuery('#toeProductDownloads [name="save"]').click();
							}
						});
						jQuery('#toeVariationsFile #add_product_download').click(function(){
							var count = jQuery('#toeVariationsFile .uploader').length;
							var name = 'userfile['+id+'][]';
							jQuery('#toeVariationsFile #first_raw').clone().appendTo('#toeVariationsFile #upload_file').removeAttr('id').find('.uploader').attr('name',name);
						});
					}
					jQuery('.toeDeleteButton, .toeSaveButton, #toeVariationsList input[name="var_name"], #toeVariationsList input[name="var_sort_order"]').show();
				}
			});
		});
    });
	<?php if(frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
		
	<?php } else { ?>
		function toeFillInEfVal(parentBox, optId) {
			jQuery(parentBox).find('.toeEfValuesBox input[name=selectedOpts]').val(optId);
			if(jQuery(optId).size() > 1) {  //If more than one value is selected - all inputs will be empty
				var setPrice = true;
				var setAbsolute = true;
				var setActive = 0;
				var setSKU = true;
				var setWidth = true;
				var setHeight = true;
				var setWeight = true;
				var setLength = true;
				var setSort = true;
				for(var i = 1; i < optId.length; i++) {
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][price]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][price]"]').val())
						setPrice = false;
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][sku]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][sku]"]').val())
						setSKU = false;
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][width]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][width]"]').val())
						setWidth = false;
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][height]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][height]"]').val())
						setHeight = false;
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][length]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][length]"]').val())
						setLength = false;
					if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][weight]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][weight]"]').val())
						setWeight = false;
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
				if(setSKU)
					jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][sku]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val('');

				if(setHeight)
					jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][height]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val('');
				if(setWeight)
					jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][weight]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val('');
				if(setLength)
					jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][length]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val('');
				if(setWidth)
					jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][width]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val('');


				if(setSort)
					jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][sort_order]"]').val() );
				else
					jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val('0');
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

				jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][sku]"]').val() );

				jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][width]"]').val() );
				jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][length]"]').val() );
				jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][weight]"]').val() );
				jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][height]"]').val() );

				jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][sort_order]"]').val() );

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
<div id="toeVariationsList">
	<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="toeVariationsDeleteForm">
		<?php echo html::hidden('postid' , 	array('value' => 0))?>
		<?php echo html::hidden('action' , 	array('value' => 'deleteVariation'))?>
		<?php echo html::hidden('page'	 , 	array('value' => 'products'))?>
		<?php echo html::hidden('reqType',	array('value' => 'ajax'))?>
	</form>
	<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="toeVariationsUpdateForm">
		<?php echo html::hidden('var_id' , 	array('value' => 0))?>
		<?php echo html::hidden('action' ,	array('value' => 'getVariationInfo'))?>
		<?php echo html::hidden('page'   , 	array('value' => 'products'))?>
		<?php echo html::hidden('reqType',	array('value' => 'ajax'))?>
	</form>
	<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="toeVariationsSaveForm">
		<div id="product_extras">
			<div style="float:left;">
				<?php echo html::button(array('attrs' => 'class="toeAddButton button"', 'value' => lang::_('Add new')))?>
				<?php foreach($this->fields as $f) {?>
					<?php if($f->name != 'var_name') continue;?>
					<?php $f->value = '';?>
					<?php $f->display()?>
				<?php }?>
				<?php echo html::text('var_name', array('attrs' => 'style="display: none;" placeholder="'. lang::_('Variation Name'). '"'));?>
				<?php echo html::text('var_sort_order', array('attrs' => 'style="display: none;" placeholder="'. lang::_('Variation Sort Order'). '"'));?>
				<?php echo html::button(array('attrs' => 'class="toeSaveButton button" style="display: none;"', 'value' => lang::_('Save')))?>
				<div id="toeVariationsSaveMsg"></div>
			</div>
			<div style="float:right;"><?php echo html::button(array('attrs' => 'class="toeDeleteButton button" style="display: none;"', 'value' => lang::_('Remove Selected')))?></div>
		</div>
		<div style="float:left; margin-left:15px;<?php echo $hideEmpty;?>">
			<select id="toeVariationsSelect">
				<option value='0'><?php lang::_e('Select variation')?></option>
				<?php foreach($this->variations as $var): ?>
					<option value="<?php echo $var[0]['post_id'];?>"><?php echo $var[0]['var_name'];?></option>
				<?php endforeach;?>
			</select>
		</div>
		<br style="clear:both;" />
		<br/>
		<div class="toeVariationsMeta" style="display:none;">
			<a id="toeHideShowProductData" class="toeMetaHref"><span class="toeHideLinkPart"><?php lang::_e('Hide')?></span> <?php lang::_e('product data')?></a>
			<a id="toeHideShowExtraData" class="toeMetaHref"><span class="toeHideLinkPart"><?php lang::_e('Hide')?></span> <?php lang::_e('extra product data')?></a>
			<a id="toeHideShowFiles" class="toeMetaHref"><span class="toeHideLinkPart"><?php lang::_e('Hide')?></span> <?php lang::_e('files')?></a>
			<a id="toeHideShowAll" class="toeMetaHref"><span class="toeHideLinkPart"><?php lang::_e('Hide')?></span> <?php lang::_e('all')?></a>
			<div id="toeVariationsMainData">
				<fieldset class="toeProdFieldset mybox">
					<legend><?php lang::_e('Main Product data')?></legend>
					<?php $i = 0; $itemPerRow = 3;?>
					<?php foreach($this->fields as $f) : ?>
						<?php if($f->name == "var_name") continue;?>
						<?php if ($f->html != 'hidden') {?>
							<?php $f->value = "";?>
							<div class="product_extra">
								 <label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
								 <div class="product_field">
									<?php $f->display()?>
									<?php if(!empty($desc)) { ?>
										<a href="#" class="toeOptTip" tip="<?php echo $desc?>"></a>
									<?php }?>
								 </div>
								 <br clear="all" />
							 </div>
							<?php 
								if($i%$itemPerRow == $itemPerRow-1) { ?>
									<br clear="all" />
								<?php } $i++;?>
						<?php } else  {
							echo $f->viewField($f->name, $this->extra_values[$f->id]);
						}?>
					<?php endforeach;?>
				</fieldset>
			</div>
			<?php if(!frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
				<div id="toeVariationsExtraData"></div>
			<?php } ?>
			<div id="toeVariationsFilesList">
				<fieldset class="toeProdFieldset mybox">
					<legend><?php lang::_e('Product files')?></legend>
					<div id="toeVariationsFile"></div>
				</fieldset>
			</div>
		</div>
		<br style="clear:both;"/>
		<?php echo html::hidden('ID'     , array('value' => $this->postid))?>
		<?php echo html::hidden('pid'    , array('value' => '-1'))?>
		<?php echo html::hidden('action' , array('value' => 'postVariation'))?>
		<?php echo html::hidden('page'	 , array('value' => 'products'))?>
		<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
		<br clear="all" />
	</form>
	<?php if(frame::_()->getModule('options')->get('use_new_products_fields')) { ?>
		<div id="toeVariationsExtraData"></div>
	<?php } ?>
</div>