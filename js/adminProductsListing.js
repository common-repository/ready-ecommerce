jQuery(document).ready(function(){
	jQuery('#the-list').sortable({
		update: function(event, ui) {
			var startIndex = 0;
			if(jQuery(ui.item).is(':first-child')) { // If now it is first element - let's take sort order from next row
				startIndex = parseInt(jQuery(ui.item).next('tr').find('.toeProdSortOrderText').val());
			} else {
				startIndex = parseInt(jQuery('#the-list .column-sort_order:first').find('.toeProdSortOrderText').val());
			}
			if(isNaN(startIndex)) {
				startIndex = 0;
			}
			jQuery('#the-list .column-sort_order').each(function(){
				jQuery(this).find('.toeProdSortOrderText').val( startIndex++ );
			});
			var msgEl = jQuery('<div />').css('position', 'absolute').css('left', event.pageX).css('top', event.pageY).appendTo('body');
			toeSaveProductsSortOrder(null, msgEl, function() { msgEl.remove() });
		}
	});
});
function toeSaveProductsSortOrder(buttonEl, msgEl, onSuccess) {
	// .column-sort_order is cells with our sort_order input fields, additional data is destination to mod controller action
	var sendData = jQuery('#the-list .column-sort_order').serializeAnything({page: 'products', action: 'saveSortOrder', reqType: 'ajax'});
	var msgEl = msgEl ? msgEl : jQuery(buttonEl).next('.toeProductsSortOrderMsg');
	jQuery.sendForm({
		msgElID: msgEl,
		data: sendData,
		onSuccess: function(res) {
			if(onSuccess && typeof(onSuccess) == 'function')
				onSuccess(res);
		}
	});
}