function toeRemoveState(link) {
	var id = getIdFromTable(link);
	if(!isNaN(id)) {
		jQuery.sendForm({
			msgElID: 'toeStatesRemoveRestoreMsg',
			data: {page: 'states', action: 'remove', reqType: 'ajax', id: id},
			onSuccess: function(res) {
				if(!res.error) {
					toeTables['states_list'].deleteRow('id', id);
				}
			}
		});
	} else
		alert(toeLang('Can\'t find ID for state'));
}
function toeEditState(cell) {
	var id = getIdFromTable(cell);
	if(!isNaN(id)) {
		toeShowEditStateDialog(id);
	} else
		alert(toeLang('Can\'t find ID for state'));
}
function toeShowEditStateDialog(id) {
	var editForm = jQuery('#toeStatesEditFormShell form:first').clone();
	if(id) {
		var stateData = toeTables['states_list'].getData('id', id);
		if(stateData) {
			for(var key in stateData) {
				jQuery(editForm).find('[name='+ key+ ']').val( stateData[key] );
			}
		}
	}
	subScreen.show(jQuery(editForm), 0, 0, function(content){
		var title = id ? 'State edit form' : 'State add form';
		subScreen.insertTitle(toeLang(title))
			.moveToCenter()
			.setAbsolute()
			.autoScroll();
	});
	jQuery(editForm).submit(function(){
		var form = this;
		jQuery(this).sendForm({
			msgElID: jQuery(this).find('.toeStatesEditMsg'),
			onSuccess: function(res) {
				if(!res.error) {
					if(res.data.id) {
						var newData = {
							id:				res.data.id,
							name:			jQuery(form).find('[name=name]').val(),
							code:			jQuery(form).find('[name=code]').val(),
							c_iso_code_3:	toeGetCountry(jQuery(form).find('[name=country_id]').val(), 'iso_code_3')
						};
						if(id) {	// Edited data
							toeTables['states_list'].redrawRow('id', id, newData);
						} else {	// Add new row
							toeTables['states_list'].draw([newData]);
						}
					}
				}
			}
		});
		return false;
	});
}
