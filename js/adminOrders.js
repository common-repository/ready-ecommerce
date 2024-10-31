jQuery(document).ready(function(){
    
    jQuery('.toeOrderListCell').click(function(e){
        toeEditOrder(this, e);
    });
	jQuery('.toeOrderListCellRemove').click(function(){
		toeRemoveOrder(this);
	});
});
function toeGetEditOrderScreen(oid) {
	var data = 'reqType=ajax&page=order&action=showOneFull&id='+ oid;
	var url = '';
	if(typeof(ajaxurl) == 'undefined')
		url = TOE_DATA.ajaxurl;
	else
		url = ajaxurl;
	jQuery.ajax({
		url: url,
		data: data,
		type: 'GET',
		dataType: 'json',
		success: function(res) {
			if(res.html) {
				subScreen.show(res.html, 0, 0, function(content){
					subScreen.moveTopCenter()
						.setAbsolute()
						.autoScroll();
				});
			}
		}
	});
}
/**
 *Function will get edit order form html after click on order row
 *@param object clickedCell - table cell element that was clicked on
 *@param object clickEvent - event of clicking, @see jQuery::click()
 **/
function toeEditOrder(clickedCell, clickEvent) {
	var id = jQuery(clickedCell).parents('tr:first').children('td:first').text();
	if(id) {
		toeGetEditOrderScreen(id);
	}
	return false;
}
function toeUpdateOrderAudit(oid) {
	jQuery('.toeOrderDetailsAuditBox').appendLoader();
	jQuery('<br />' /*any html element, should be form, but we will use data attr here*/ ).sendForm({
		data: {page: 'order', action: 'getAuditHtml', oid: oid, reqType: 'ajax'},
		onSuccess: function(res) {
			if(res.error) {
				alert(res.errors[0]);
			} else {
				jQuery('.toeOrderDetailsAuditBox').html( res.html );
			}
		}
	});
}
function toeUpdateOrder(form) {
	jQuery(form).sendForm({
		onSuccess: function(res) {
			if(jQuery('.toe_order_row').exists()) {
				if(res.data) {
					jQuery('.toe_order_row').each(function(){
						var id = parseInt(jQuery(this).find('td:first').text());
						if(id == res.data['id']) {
							var curRow = this;	//Current row in rows iterations cicle
							jQuery(this).find('td').each(function(iter){
								switch(iter) {
									case 1:		//total row
										jQuery(this).html(res.data['total']);
										break;
									case 2:		//status row
										jQuery(this).html(res.data['status']);
										var tabName = jQuery(curRow).parents('div.ui-tabs-panel:first').attr('id');
										if(tabName != 'toe_orders_all' && tabName != 'toe_'+ res.data['status']) {	//Move order row to anoter tab
											var newRow = jQuery(curRow).clone();
											jQuery(newRow).find('.toeOrderListCell').click(function(e){
												toeEditOrder(this, e);
											});
											jQuery('#toe_'+ res.data['status']+ ' table:first').append( newRow );
											jQuery(curRow).remove();
										}
										break;
								}
							});
						}
					});
				}
			}
			if(res.data['id'])
				toeUpdateOrderAudit(res.data['id']);
		}
	});
}
function toeRemoveOrder(cell) {
	var id = getIdFromTable(cell);
	if(id && confirm(toeLang('Are you sure? This will delete order with it items and actions log (if exist).'))) {
		var msgEl = null;
		if(jQuery(cell).find('.toeRemoveOrderMsg').size())
			msgEl = jQuery(cell).find('.toeRemoveOrderMsg');
		else {
			msgEl = jQuery('<span class="toeRemoveOrderMsg"></span>');
			jQuery(cell).append(msgEl);
		}
		jQuery.sendForm({
			msgElID:	msgEl,
			data:		{page: 'order', action: 'remove', reqType: 'ajax', id: id},
			onSuccess:	function(res) {
				if(!res.error) {
					if(jQuery('.toe_order_row').exists()) {
						if(res.data && res.data.id) {
							jQuery('.toe_order_row').each(function(){
								var id = parseInt(jQuery(this).find('td:first').text());
								if(id == res.data['id']) {
									jQuery(this).animateRemove(300);
								}
							});
						}
					}
				}
			}
		});
	}
}
function toeSelectGiftFromOrder(oid, gid) {
	jQuery.sendForm({
		data: {mod: 'gifts', action: 'getSelectProductForOrderHtml', oid: oid, gid: gid, reqType: 'ajax'},
		msgElID: 'msg',
		onSuccess: function(res) {
			if(!res.error && res.html) {
				if(!jQuery('#toeOrderGiftsContentId').size()) {
					jQuery('<div id="toeOrderGiftsContentId" style="display: none;"/>').appendTo('body');
				}
				jQuery('#toeOrderGiftsContentId').html( res.html );
				tb_show(toeLang('Select your additional gifts'), '#TB_inline?inlineId=toeOrderGiftsContentId');
				// Make wp standard popup z-index more than z-index in our popup screen as tey should work together here
				jQuery('#TB_window').css('z-index', parseInt(jQuery('#toe_sub_screen').css('z-index')) + 1);
				jQuery('#TB_overlay').css('z-index', parseInt(jQuery('#toe_sub_screen_bg').css('z-index')) + 1);
			} 
		}
	});
	return false;
}
