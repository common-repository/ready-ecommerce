function taxChangeType(type) {
    jQuery('.tax_type_data').hide();
    switch(type) {
        case 'address':
            jQuery('#tax_address').show('slow');
            break;
        case 'category':
            jQuery('#tax_category').show('slow');
            break;    
    }
}

function scCancelColorPicker(){
  var oldColor = jQuery("#colorpicker").attr('old-color');
  if (oldColor) {
    jQuery.farbtastic("#colorpicker").setColor(oldColor);
    jQuery("#colorpicker").hide();
  }
}

jQuery(function() {
    // Start taxes tab javascript
    jQuery('body').on('submit', '#editTaxForm', function(){
        jQuery(this).sendForm({msgElID: 'mod_msg_tax', onSuccess: function(res) {
				if(!res.error) {
					if(jQuery('#taxes_list').length) {  //For using in adminPage.php template
						var mainTableUpdated = false;
						jQuery('.toe_opt_taxes').each(function(){
							if(jQuery(this).children('td:first').text() == res.data.id) {
								jQuery(this).children('td').each(function(iter, el){
									switch(iter) {
										case 1:
											jQuery(el).html(res.data.label);
											break;
										case 2:
											jQuery(el).html(res.data.code);
											break;
									}
								});
								mainTableUpdated = true;
							}
						});
						if(!mainTableUpdated) { //No Updates - add new row
							var table = jQuery('#taxes_list');
							jQuery(table).append('<tr class="toe_admin_row toe_opt_taxes"></tr>');
							var tr = jQuery(table).find('tr:last');
							for(var id in res.data) {
								jQuery(tr).append('<td>'+ res.data[id]+ '</td>');
							}
							jQuery(tr).append('<td><a href="#" class="toe_opt_remove_tax" onclick="removeTax(this); return false;"><img src="'+ TOE_DATA.close+'" /></a></td>');
							jQuery(tr).on('click', function(e) {
								jQuery(this).toeGetEditTax(e);
							});
						}
					}
					if(jQuery('#editTaxForm').find('input[name=id]').val() == 0) { //INSERT action
						jQuery('#editTaxForm').clearForm();
					}
				}
            }}
        );
        return false;
    });
    jQuery('body').on('change', 'select[name=type]', function(){
        taxChangeType(jQuery(this).val());
    });
    // end taxes tab javascript
    
    // start user fields tab
    jQuery('body').on('submit', '#editUserfieldForm', function(){
        jQuery(this).sendForm({msgElID: 'mod_msg_user', onSuccess: function(res) {
                if(toeTables['user_fields_list']) {
                    if(!toeTables['user_fields_list'].redrawRow('id', res.data.id, res.data)) {
                        toeTables['user_fields_list'].draw([res.data]);
                    }
                }
                if(jQuery('#editUserfieldForm').find('input[name=id]').val() == 0 && !res.error) { //INSERT action
                    jQuery('#editUserfieldForm').clearForm();
                }
            }}
        );
        return false;
    });
    // end user field tab
    
    // start currency tab
    jQuery('body').on('submit', '#editCurrencyForm', function(){
        toeCheckCurrencyPriceView();    //To fill currency and price view fields with correct data
        jQuery(this).sendForm({msgElID: 'mod_msg_curr', onSuccess: function(res) {
                if(jQuery('.toe_opt_currency').length) {  //For using in adminPage.php template
                    if(!res.error) {
                        var mainTableUpdated = false;
                        jQuery('.toe_opt_currency').each(function(){
                            if(jQuery(this).children('td:first').text() == res.data.id) {
                                jQuery(this).children('td').each(function(iter, el){
                                    switch(iter) {
                                        case 1:
                                            jQuery(el).html(res.data.label);
                                            break;
                                        case 2:
                                            jQuery(el).html(res.data.code);
                                            break;
                                    }
                                });
                                mainTableUpdated = true;
                            }
                        });
                        if(!mainTableUpdated) { //No Updates - add new row
                            var table = jQuery('#toe_opt_currencyTab').children('table');
                            jQuery(table).append('<tr class="toe_admin_row toe_opt_currency"></tr>');
                            var tr = jQuery(table).find('tr:last');
                            for(id in res.data) {
                                jQuery(tr).append('<td>'+ res.data[id]+ '</td>');
                            }
                            jQuery(tr).append(jQuery('.toe_opt_remove_currency:first').clone());
                        }
                    }
                    if(jQuery('#editCurrencyForm').find('input[name=id]').val() == 0 && !res.error) { //INSERT action
                        jQuery('#editCurrencyForm').clearForm();
                    }
                }
            }}
        );
        return false;
    });
    // end currency tab
    // start product field tab 
    jQuery('body').on('submit', '#editProductfieldForm', function(){
        jQuery(this).sendForm({msgElID: 'mod_msg_prod', onSuccess: function(res) {
                var mainTableUpdated = false;
                if(jQuery('.toe_opt_productfield').length) {  //For using in adminPage.php template
                    jQuery('.toe_opt_productfield').each(function(){
                        if(jQuery(this).children('td:first').text() == res.data.id) {
                            jQuery(this).children('td').each(function(iter, el){
                                switch(iter) {
                                    case 1:
                                        jQuery(el).html(res.data.label);
                                        break;
                                    case 2:
                                        jQuery(el).html(res.data.code);
                                        break;
                                    case 3:
                                        jQuery(el).html(res.data.type);
                                        break;
                                }
                            });
                            mainTableUpdated = true;
                        }
                    });
                } else if(jQuery('#product_fields_list').size() == 0) {
                    document.location.reload();
                } else {    //for using at products page
                    if (res.field != '') {
                        var new_field = '<div class="product_extra">'+res.field+'</div>';
                        jQuery('#product_extras').append(new_field);
                    }
                }
                if(!mainTableUpdated) { //No Updates - add new row
                    var table = jQuery('#product_fields_list');
                    jQuery(table).append('<tr class="toe_admin_row toe_opt_productfield"></tr>');
                    var tr = jQuery(table).find('tr:last');
                    for(id in res.data) {
                        jQuery(tr).append('<td>'+ res.data[id]+ '</td>');
                    }
                    jQuery(tr).append('<td><a href="#" class="toe_opt_remove_productfield" onclick="removeProductfield(this); return false;"><img src="'+ TOE_DATA.close+ '" /></a></td>');
                }
                if(jQuery('#editProductfieldForm').find('input[name=id]').val() == 0) { //INSERT action
                    jQuery('#editProductfieldForm').clearForm();
                }
				if(!res.error && res.html && res.html != '') {
					jQuery('#product_field_form').html(res.html);
                    jQuery('.extrafield_type').trigger('change');
				}
            }}
        );
        return false;
    });
    // end product field tab
    // Start taxes tab javascript
    jQuery('body').on('submit', '#editTemplateForm', function(){
        jQuery(this).sendForm({msgElID: 'mod_msg_template', onSuccess: function(res) {
                if(jQuery('.toe_opt_messenger_template').length) {  //For using in adminPage.php template
                    jQuery('.toe_opt_messenger_template').each(function(){
                        if(jQuery(this).children('td:first').text() == res.data.id) {
                            jQuery(this).children('td').each(function(iter, el){
                                switch(iter) {
                                    case 1:
                                        jQuery(el).html(res.data.label);
                                        break;
                                    case 2:
                                        jQuery(el).html(res.data.code);
                                        break;
                                }
                            });
                        }
                    });
                }
                if(jQuery('#editTemplateForm').find('input[name=id]').val() == 0 && !res.error) { //INSERT action
                    jQuery('#editTemplateForm').clearForm();
                }
            }}
        );
        return false;
    });
      // Color picker. (Farbtastic.

      jQuery("body").append("<div id='colorpicker'></div>");
      
      if(jQuery("#colorpicker").exists() && jQuery(".colorpicker").exists())
        jQuery("#colorpicker").farbtastic(".colorpicker:first").prepend("<span class='ui-icon ui-icon-check'></span>");
      jQuery('.colorpicker').each(function(){
			jQuery.farbtastic("#colorpicker").linkTo(jQuery(this));
      });
      jQuery('.colorpicker').focus(function() {
            jQuery("#colorpicker").hide();
            jQuery.farbtastic("#colorpicker").linkTo(jQuery(this));
            jQuery("#colorpicker").attr('old-color', jQuery.farbtastic("#colorpicker").color);
            var offset = jQuery(this).offset();
            jQuery("#colorpicker").css('left', offset.left - 68).css('top', offset.top + 20).fadeIn(400);
      });
      jQuery("#colorpicker .ui-icon-check").click(function(){
            jQuery("#colorpicker").hide();
      });
      jQuery('body').on('click', '.colorpicker', function(){
            jQuery("#colorpicker").attr('old-color', jQuery.farbtastic("#colorpicker").color);
            jQuery("#colorpicker").show();
      });
      jQuery('.colorpicker').keydown(function(event) {
            // Esc.
            if (event.keyCode == 27) {scCancelColorPicker()}
            // Enter.
            if (event.keyCode == 13) {
              jQuery("#colorpicker .ui-icon-check").click();
              event.preventDefault();
            }
            // Space.
            if (event.keyCode == 32) {jQuery("#colorpicker").show();}
      });
	  jQuery('#toeModActivationPopupForm').submit(function(){
		  jQuery(this).sendForm({
			  msgElID: 'toeModActivationPopupMsg',
			  onSuccess: function(res){
				  if(res && !res.error) {
					  var goto = jQuery('#toeModActivationPopupForm').find('input[name=goto]').val();
					  if(goto && goto != '') {
						toeRedirect(goto);  
					  } else
						toeReload();
				  }
			  }
		  });
		  return false;
	  });
	  jQuery('.toeRemovePlugActivationNotice').click(function(){
		  jQuery(this).parents('.info_box:first').animateRemove();
		  return false;
	  });
	  if(window.location && window.location.href && window.location.href.indexOf('plugins.php')) {
		  if(TOE_DATA.allCheckRegPlugs && typeof(TOE_DATA.allCheckRegPlugs) == 'object') {
			  for(var plugName in TOE_DATA.allCheckRegPlugs) {
				  if(plugName == '[]' || strpos(plugName, '@') !== false || strpos(plugName, '#') !== false) continue;
				  var plugRow = jQuery('#'+ plugName.toLowerCase())
				  ,	updateMsgRow = plugRow.next('.plugin-update-tr');
				  if(plugRow.size() && updateMsgRow.find('.update-message').size()) {
					  updateMsgRow.find('.update-message').find('a').each(function(){
						  if(jQuery(this).html() == 'update now') {
							  jQuery(this).click(function(){
								  toeShowModuleActivationPopup( plugName, 'activateUpdate', jQuery(this).attr('href') );
								  return false;
							  });
						  }
					  });
				  }
			  }
		  }
	  }
	  
});

function toeShowModuleActivationPopup(plugName, action, goto) {
	action = action ? action : 'activatePlugin';
	goto = goto ? goto : '';
	jQuery('#toeModActivationPopupForm').find('input[name=plugName]').val(plugName);
	jQuery('#toeModActivationPopupForm').find('input[name=action]').val(action);
	jQuery('#toeModActivationPopupForm').find('input[name=goto]').val(goto);
	
	tb_show(toeLang('Activate plugin'), '#TB_inline?width=710&height=220&inlineId=toeModActivationPopupShell', false);
	var popupWidth = jQuery('#TB_ajaxContent').width()
	,	docWidth = jQuery(document).width();
	// Here I tried to fix usual wordpress popup displace to right side
	jQuery('#TB_window').css({'left': Math.round((docWidth - popupWidth)/2)+ 'px', 'margin-left': '0'});
}