jQuery(document).ready(function(){
	jQuery('body').on('submit', '#toeProductFieldsEditForm', function(){
		if(!jQuery(this).attr('requestInProcess')) {
			jQuery(this).attr('requestInProcess', 1);
			jQuery(this).sendForm({
				msgElID: 'toeProductFieldsEditFormMsg',
				onSuccess: function(res) {
					jQuery('#toeProductFieldsEditForm').removeAttr('requestInProcess');
					if(!res.error) {
						var id = parseInt(jQuery('#toeProductFieldsEditForm').find('[name=id]').val());
						if(res.data.product_field) {
							if(res.data.product_fields_list) {	// Rebuild all table
								toeTables['products_fields_list'].clearTable();
								toeTables['products_fields_list'].draw(res.data.product_fields_list);
							} else if(id) {	// Edited data
								toeTables['products_fields_list'].redrawRow('id', id, res.data.product_field);
							} else {	// Add new row
								toeTables['products_fields_list'].draw([res.data.product_field]);
							}
							toeEditProdField(null, res.data.product_field.id);
						}
					}
				}
			});
		}
		return false;
	});
	
	jQuery('body').on('change', '#toeProductFieldsHtmlTypeId', function(){
		toeChangeProdFieldType();
	});
	jQuery('body').on('click', '#toeProductFieldsAddOption', function(){
		toeCreateProdOptionField();
		return false;
	});
	jQuery('body').on('click', '.toeProductFieldsOptionShell > .toeDeleteButt', function(){
		toeRemoveProdOption(this);
		return false;
	});
	jQuery('body').on('click', '#toeAddNewProductField', function() {
		toeClearEditProdFieldForm();
		return false;
	});
});
function toeChangeProdFieldType() {
	var newTypeId = parseInt(jQuery('#toeProductFieldsHtmlTypeId').val());
	var defaultValueInput = null;
	switch(newTypeId) {
		case 1:	// Text
			defaultValueInput = jQuery('<input type="text" name="default_value" />');
			break;
		case 17:	// Textarea
			defaultValueInput = jQuery('<textarea name="default_value" rows="3" cols="50"></textarea>');
			break;
		case 9:
		case 10:
			defaultValueInput = jQuery('<select name="default_value"></select>');
			break;
		case 5:	// Checkboxes
		case 12:	// Select List
			defaultValueInput = jQuery('<select name="default_value[]" size="5" multiple="multiple"></select>');
			break;
			
	}
	jQuery('#toeProductFieldsDefaultValueShell').html('').append( defaultValueInput );
	if(toeHtmlTypeOptioned(newTypeId)) {	// Options that have multiple values
		jQuery('#toeProductFieldsOptionsRow').show();
		toeRebuildProdOptionsDefaultValueSelect();
	} else {	// All other options
		jQuery('#toeProductFieldsOptionsRow').hide();
	}
}
function toeCreateProdOptionField(data) {
	this.optIter;
	if(!this.optIter)
		this.optIter = 1;
	var newCell = jQuery('#toeProductFieldsOptionExample').clone().removeAttr('id').appendTo('#toeProductFieldsOptionsShell').show();
	
	if(data) {
		newCell.find('[name="options[][label]"]').val( data.label );
		newCell.find('[name="options[][price]"]').val( data.price );
		newCell.find('[name="options[][sort_order]"]').val( data.sort_order );
		newCell.find('[name="options[][id]"]').val( data.id );
		if(data.absolute)
			newCell.find('[name="options[][absolute]"]').attr('checked');
	}
	var currIter = this.optIter;
	newCell.find(':input').attr('name', function(i, name){
		return name.replace('[]', '['+ currIter+ ']');
	});
	toeFillOptionsSortOrder();
	this.optIter++;
}
function toeRebuildProdOptionsDefaultValueSelect() {
	var defaultValueSelect = jQuery('#toeProductFieldsDefaultValueShell').find('select:first');
	if(defaultValueSelect.size()) {
		defaultValueSelect.find('option').remove();
		if(!defaultValueSelect.attr('multiple')) {
			defaultValueSelect.append( jQuery('<option value="">'+ toeLang('Not Selected')+ '</option>') );
		}
		jQuery('#toeProductFieldsOptionsRow').find('.toeProductFieldsOptionShell').each(function(){
			var newOptionLabel = jQuery(this).find('input.toeProductFieldsOptionLabel').val();
			if(newOptionLabel && newOptionLabel != '') {
				var newOption = jQuery('<option value="'+ newOptionLabel+ '">'+ newOptionLabel+ '</option>');
				defaultValueSelect.append( newOption );
			}
		});
	}
}
function toeHtmlTypeOptioned(typeId) {
	return (jQuery.inArray(parseInt(typeId), [5, 9, 10, 12]) !== -1);
}
function toeFillOptionsSortOrder() {
	var order = 1;
	jQuery('#toeProductFieldsOptionsRow').find('.toeProductFieldsOptionShell').each(function(){
		jQuery(this).find('input.toeProductFieldsOptionSortOrder').val( order++ );
	});
}
function toeRemoveProdOption(clickedRemover) {
	jQuery(clickedRemover).parents('.toeProductFieldsOptionShell:first').remove();
	toeRebuildProdOptionsDefaultValueSelect();
	toeFillOptionsSortOrder();
}
function toeEditProdField(cell, id) {
	var id = id ? id : getIdFromTable(cell),
		fieldData = toeTables['products_fields_list'].getData('id', id);
	if(fieldData) {
		toeClearEditProdFieldForm();
		var form = jQuery('#toeProductFieldsEditForm');
		form.find('[name=label]').val( fieldData.label );
		form.find('[name=htmltype_id]').val( fieldData.htmltype_id ).trigger('change');
		if(fieldData.options) {
			for(var i in fieldData.options) {
				toeCreateProdOptionField( fieldData.options[i] );
			}
			toeRebuildProdOptionsDefaultValueSelect();
		}
		if(fieldData.default_value) {
			// If it is ID - let's convert it to label
			if(parseInt(fieldData.default_value) && fieldData.options) {
				for(var i in fieldData.options) {
					if(fieldData.options[i].id == fieldData.default_value) {
						fieldData.default_value = fieldData.options[i].label;
						break;
					}
				}
			}
			if(form.find('[name=default_value]').size()) {
				form.find('[name=default_value]').val( fieldData.default_value );
			} else {
				for(var i in fieldData.options) {
					if(jQuery.inArray(fieldData.options[i].id, fieldData.default_value)) {
						form.find('[name="default_value[]"]').find('option[value="'+ fieldData.options[i].label+ '"]').attr('selected', 'selected');
					}
				}
			}
		}
		if(fieldData.categories) {
			var categoriesSelect = form.find('[name="categories[]"]');
			for(var i in fieldData.categories) {
				categoriesSelect.find('option[value="'+ fieldData.categories[i]+ '"]').attr('selected', 'selected');
			}
		}
		if(fieldData.products) {
			var categoriesSelect = form.find('[name="products[]"]');
			for(var i in fieldData.products) {
				categoriesSelect.find('option[value="'+ fieldData.products[i]+ '"]').attr('selected', 'selected');
			}
		}
		if(fieldData.mandatory)
			form.find('[name=mandatory]').attr('checked', 'checked');
		if(fieldData.active)
			form.find('[name=active]').attr('checked', 'checked');
		form.find('[name=sort_order]').val( fieldData.sort_order );
		form.find('[name=id]').val( fieldData.id );
		form.find('[name=original_id]').val( fieldData.original_id );
	} else {
		alert('Can not find data for product field');
	}
}
function toeClearEditProdFieldForm() {
	var form = jQuery('#toeProductFieldsEditForm');
	form.clearForm();
	form.find('[name=id]').val( 0 );
	form.find('[name=original_id]').val( 0 );
	jQuery('#toeProductFieldsOptionsShell').html('');
}
function toeRemoveProdField(link, id) {
	var id = id ? id : getIdFromTable(link),
		fieldData = toeTables['products_fields_list'].getData('id', id);
	if(fieldData) {
		if(confirm('Are you sure want to remove "'+ fieldData.label+ '" product field?')) {
			var msgEl = jQuery('<span />').insertAfter(link);
			jQuery.sendForm({
				msgElID: msgEl,
				data: {page: 'products_fields', action: 'removeProductField', reqType: 'ajax', id: id},
				onSuccess: function(res) {
					if(!res.error) {
						toeTables['products_fields_list'].deleteRow('id', id);
					}
				}
			});
		}
	}
}
