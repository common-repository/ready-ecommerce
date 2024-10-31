function toeRemoveCountry(link) {
	var id = getIdFromTable(link);
	if(!isNaN(id)) {
		jQuery.sendForm({
			msgElID: 'toeCountriesRemoveRestoreMsg',
			data: {page: 'countries', action: 'remove', reqType: 'ajax', id: id},
			onSuccess: function(res) {
				if(!res.error) {
					toeTables['countries_list'].deleteRow('id', id);
				}
			}
		});
	} else
		alert(toeLang('Can\'t find ID for country'));
}
function toeEditCountry(cell) {
	var id = getIdFromTable(cell);
	if(!isNaN(id)) {
		toeShowEditCountryDialog(id);
	} else
		alert(toeLang('Can\'t find ID for country'));
}
function toeShowEditCountryDialog(id) {
	var editForm = jQuery('#toeCountriesEditFormShell form:first').clone();
	if(id) {
		var countryData = toeTables['countries_list'].getData('id', id);
		if(countryData) {
			for(var key in countryData) {
				jQuery(editForm).find('[name='+ key+ ']').val( countryData[key] );
			}
		}
	}
	subScreen.show(jQuery(editForm), 0, 0, function(content){
		var title = id ? 'Country edit form' : 'Country add form';
		subScreen.insertTitle(toeLang(title))
			.moveToCenter()
			.setAbsolute()
			.autoScroll();
	});
	jQuery(editForm).submit(function(){
		var form = this;
		jQuery(this).sendForm({
			msgElID: jQuery(this).find('.toeCountriesEditMsg'),
			onSuccess: function(res) {
				if(!res.error) {
					if(res.data.id) {
						var newData = {
							id:			res.data.id,
							name:		jQuery(form).find('[name=name]').val(),
							iso_code_2: jQuery(form).find('[name=iso_code_2]').val(),
							iso_code_3:	jQuery(form).find('[name=iso_code_3]').val()
						};
						if(id) {	// Edited data
							toeTables['countries_list'].redrawRow('id', id, newData);
						} else {	// Add new row
							toeTables['countries_list'].draw([newData]);
						}
					}
				}
			}
		});
		return false;
	});
}