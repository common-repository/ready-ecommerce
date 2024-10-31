var mousePos = {x: 0, y: 0};
var mouseDown = false;
var mouse = {x: 0, y: 0, down: false};
var toeLoadElement = '<p class="subscreen_loading"><img src="'+ TOE_DATA.loader+ '" /></p>';
var removeClicked = false;
var notOnRowClicked = false;
var toeAdminTabsLoaded = {};
var toeAdminPaymentTabStatSent = false;
function toeLoadAdminOptTabContent(tabObj) {
	var tabLink = (tabObj && jQuery(tabObj).tagName() == 'A') ? jQuery(tabObj) : jQuery(tabObj).find('a:first')	// tabObj can be link ("a") or element, that contain link
	,	module = jQuery(tabLink).attr('module')
	,	view = jQuery(tabLink).attr('view')
	,	contentIdSelector = jQuery(tabLink).attr('href')
	,	msgEl = jQuery(contentIdSelector);
	if(module && view && !toeIsAdminOptTabLoaded(module, view)) {
		jQuery.sendForm({
			msgElID: msgEl,
			data: {page: 'options', action: 'getOptTabContentHtml', module: module, view: view, reqType: 'ajax'},
			onSuccess: function(res) {
				if(!res.error && res.html) {
					jQuery(contentIdSelector).html(res.html);
					toeAdminOptTabLoaded(module, view);
				}
			}
		});
	} else if(contentIdSelector == '#toe_opt_modules_payment' && !toeAdminPaymentTabStatSent) {	// It is loaded by default
		jQuery.sendForm({
			msgElID: jQuery('<div />'),
			data: {page: 'promo_ready', action: 'saveUsageStat', code: 'options.paymentsTab', reqType: 'ajax'}
		});
		toeAdminPaymentTabStatSent = true;
	}
}
function toeAdminOptTabLoaded(module, view) {
	toeAdminTabsLoaded[module+ '__'+ view] = {loaded: true};
}
function toeIsAdminOptTabLoaded(module, view) {
	if(toeAdminTabsLoaded[module+ '__'+ view] && toeAdminTabsLoaded[module+ '__'+ view].loaded)
		return true;
	return false;
}
jQuery(function() {
	// For version 3.4.1 or less WP use old jquery.tabs that have no action beforeActivate
	if(TOE_DATA.wpVersionFloat && TOE_DATA.wpVersionFloat <= 3.4) {
		jQuery("#toe_opt_tabs").tabs({
			select: function(event, ui) {
				toeLoadAdminOptTabContent(ui.tab);
			}
		,	create: function(event, ui) {	// This was added for case when we go to tab dirrectly, by url
				toeLoadAdminOptTabContent(ui.tab);
			}
		});
	} else {
		jQuery("#toe_opt_tabs").tabs({
			beforeActivate: function(event, ui) {
				toeLoadAdminOptTabContent(ui.newTab);
			}
		,	create: function(event, ui) {	// This was added for case when we go to tab dirrectly, by url
				toeLoadAdminOptTabContent(ui.tab);
			}
		});
		var allModulesTabIndex = jQuery('#toeModLastTab').index('#toeMainOptsTabsList li');
		if(allModulesTabIndex && allModulesTabIndex != -1) {
			jQuery('#toe_opt_tabs').tabs('disable', allModulesTabIndex);
			jQuery('#toeModLastTab').css('opacity', 1);	// Prevent common jquery ui disabled opacity fading
		}
	}
    jQuery("#toe_opt_tabs").addClass('ui-tabs-vertical-right-side ui-helper-clearfix');
    jQuery("#toe_opt_tabs li").removeClass('ui-corner-top').addClass('ui-corner-right');
	// Tab content width - in css it is 80%, but this can broke options page on some small screens.
	// Let's calc it as full tabs shell element width - width of tabs list element - 40px (last number is just for fixing some paddings, etc.)
	var tabContentWidth = jQuery('#toe_opt_tabs').width() - jQuery('#toeMainOptsTabsList').width() - 40;
	if(tabContentWidth && tabContentWidth > 0)
		jQuery('.toeOptTabContent').width(tabContentWidth);
    jQuery('.opt_general_save_butt').click(function(){
        var id = parseInt(jQuery(this).parents('tr:first').find('td:first').html());
        if(id) {
            var value = '';
            switch(jQuery(this).prev().attr('type')) {
                case 'checkbox':
                    value = jQuery(this).prev().attr('checked') ? 1 : 0;
                    break;
                default:
                    if(jQuery('#opt_general_form').find('[name="opt_values['+ id+ ']"]:first').size()) {
                        value = jQuery('#opt_general_form').find('[name="opt_values['+ id+ ']"]:first').val();
                    } else
                        value = jQuery(this).prev().val();
                    break;
            }
            jQuery('#opt_general_form :input[name=value]').val(value);
            jQuery('#opt_general_form :input[name=id]').val(id);
            jQuery('#opt_general_form').sendForm({
				msgElID: jQuery(this).parents('tr:first').find('.toeMainOptsMsg:first')
			});
        }
    });
    jQuery('body').on('mouseover', '.toeOptTip', function(event){
        if(!jQuery('#toeOptDescription').attr('toeFixTip')) {
			var pageY = event.pageY - jQuery(window).scrollTop();
			var pageX = event.pageX;
			var tipMsg = jQuery(this).attr('tip');
			var moveToLeft = jQuery(this).hasClass('toeTipToLeft');	// Move message to left of the tip link
			if(typeof(tipMsg) == 'undefined' || tipMsg == '') {
				tipMsg = jQuery(this).attr('title');
			}
			toeOptShowDescription( tipMsg, pageX, pageY, moveToLeft );
			jQuery('#toeOptDescription').attr('toeFixTip', 1);
		}
        return false;
    });
    jQuery('body').on('mouseout', '.toeOptTip', function(){
		toeOptTimeoutHideDescription();
        return false;
    });
	jQuery('body').on('mouseover', '#toeOptDescription', function(e){
		jQuery(this).attr('toeFixTip', 1);
		return false;
    });
	jQuery('body').on('mouseout', '#toeOptDescription', function(e){
		toeOptTimeoutHideDescription();
		return false;
    });
});
jQuery.fn.inputToTextTD = function(){
    var inpVal = jQuery(this).children('input[type=text]').val();
    if(inpVal) {
        jQuery(this).html(inpVal);
        jQuery(this).bind('click', jQuery.fn.insertEditInTD);
    }
}
jQuery(document).ready(function(){
    jQuery('body').on('click', '.options .add_option', function(){
       var html = '';
       switch(jQuery(this).attr('id')) {
           case 'toeAddUserfieldOpt':
               html = '<p> '+ toeLang('Value')+ ': <input type="text" value="" name="params[options][value][]"> <span class="delete_option"></span></p>';
               break;
           default: //For products params
			   var lastId = 0;
			   var lastBox = jQuery(this).parent().find('p:last');
			   if(lastBox && lastBox.size()) {
				   var lastCheckName = lastBox.find('input[type=checkbox][name^="params[options][price_absolute]"]').attr('name');
				   if(lastCheckName) {
					   var splittedName = str_replace(lastCheckName, ']', '').split('[');
					   if(splittedName && splittedName.length) {
						   lastId = parseInt(splittedName[ splittedName.length-1 ]);
						   if(!isNaN(lastId)) 
							   lastId++;	// Next ID will be greater than prev. one
					   }
				   }
			   }
			   html = '<p>'
				   + toeLang('Value')+ ':  <input type="text" value="" name="params[options][value]['+ lastId+ ']" />'
				   + '<br />'
				   + toeLang('Sort Order')+ ': <input type="text" value="" name="params[options][sort_order]['+ lastId+ ']" style="width: 30px;" /> '
				   + toeLang('Price')+ ': <input type="text" value="" name="params[options][price]['+ lastId+ ']" style="width: 70px;" /> '
				   + ',&nbsp;'
				   + toeLang('absolute')+ ': <input type="checkbox" value="" name="params[options][price_absolute]['+ lastId+ ']" /> '
				   + '<span class="delete_option"></span>'
			   + '</p>';
               break;
       }
       jQuery(this).parent().append(html); 
    });
    jQuery('body').on('click', '.options .delete_option', function(){
       var span = jQuery(this);
       var id = span.attr('rel');
       if (isNumber(id)) {
           var data = 'reqType=ajax&page=options&action=deleteEfOption&id='+ id;
           jQuery.post(ajaxurl,data, function(res){
               if (res.html == '1') {
                    span.parent().remove();       
               } else {
                   alert(res.html);
               }
           }, "json");
       } else {
           span.parent().remove();       
       }
    });
    jQuery('body').on('click', '.toe_opt_module', function(e){
        if(notOnRowClicked) {
            notOnRowClicked = false;
            return;
        }
        var id = jQuery(this).children('td:first').text();
		if(!parseInt(id) && jQuery(this).find('.toeGetModButton').size()) {
			window.open( jQuery(this).find('.toeGetModButton').attr('href') );
			return;
		}
		toeShowModuleEditForm(id);
    });
    // add user field
    jQuery('#add_opt_userfield').click(function(e){
        var data = 'reqType=ajax&page=options&action=getAddUserfields';
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#user_field_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#user_field_form .subscreen_loading').parent().html(res.html);
                }
            }
        });
        return false;
    });
    // edit user field
    jQuery('body').on('click', '.toe_opt_userfield', function(e){
        if(removeClicked || notOnRowClicked) return;
        var id = jQuery(this).children('td:first').text();
        var data = 'reqType=ajax&page=options&action=getEditUserfields&id='+ id;
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#user_field_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#user_field_form .subscreen_loading').parent().html(res.html);
                    jQuery('.extrafield_type').trigger('change');
                }
            }
        });
    });
    // add product field
    jQuery('#add_opt_productfield').click(function(e){
        var data = 'reqType=ajax&page=options&action=getAddProductfields';
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#product_field_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#product_field_form .subscreen_loading').parent().html(res.html);
                }
            }
        });
        return false;
    });
    // add product field in pop-up
    jQuery('#add_productfield_popup').click(function(e){
        var mouse = {x: e.pageX, y: e.pageY};
        var data = 'reqType=ajax&page=options&action=getAddProductfields&show_field=1&pids[]='+ jQuery(this).attr('href');
        subScreen.show(toeLoadElement, mouse.x, mouse.y);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
					subScreen.moveTopCenter();
					subScreen.insertContent(res.html, true);
                    jQuery('body').append('<div id="toeOptDescription"></div>');
                }
            }
        });
        return false;
    });
	// variations in pop-up
    jQuery('#add_variations').click(function(e){
        var mouse = {x: e.pageX, y: e.pageY};
        var data = 'reqType=ajax&page=products&action=getVariationPage&postid='+jQuery("#post_ID").val();
        
		subScreen.show(toeLoadElement, mouse.x, mouse.y);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
					subScreen.moveTopCenter();
					subScreen.insertContent(res.html, true);
                    jQuery('body').append('<div id="toeOptVariations"></div>');
                }
            }
        });
        return false;
    });
    // edit product field
    jQuery('body').on('click', '.toe_opt_productfield', function(e){
        if(removeClicked) return;
        var id = jQuery(this).children('td:first').text();
        var data = 'reqType=ajax&page=options&action=getEditProductfields&id='+ id;
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#product_field_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#product_field_form .subscreen_loading').parent().html(res.html);
                    jQuery('.extrafield_type').trigger('change');
                }
            }
        });
    });
    // add currency
    jQuery('#add_opt_currency').click(function(e){
        var data = 'reqType=ajax&page=currency&action=getAddCurrency';
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#currency_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#currency_form .subscreen_loading').parent().html(res.html);
                }
            }
        });
        return false;
    });
    // edit currency
    jQuery('body').on('click', '.toe_opt_currency', function(e){
        if(removeClicked) return;
        var id = jQuery(this).children('td:first').text();
        var data = 'reqType=ajax&page=currency&action=getEditCurrency&id='+ id;
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#currency_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#currency_form .subscreen_loading').parent().html(res.html);
                    //toeChangeCurrencyViewSelect();
                }
            }
        });
    });
    // add tax
    jQuery('body').on('click', '#add_opt_taxes', function(e){
        var data = 'reqType=ajax&page=taxes&action=getAddTax';
        //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
        jQuery("#tax_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                   jQuery('#tax_form .subscreen_loading').parent().html(res.html);
                }
            }
        });
        return false;
    });
    // edit tax
    jQuery('body').on('click', '.toe_opt_taxes', function(e){
        jQuery(this).toeGetEditTax(e);
    });
    // edit template
    jQuery('body').on('click', '.toe_opt_messenger_template', function(e){
        jQuery("#editTemplateForm").remove();
        jQuery("#messenger_form").html('');
        if(removeClicked) return;
        var id = jQuery(this).children('td:first').text();
        var data = 'reqType=ajax&page=messenger&action=getEditTemplate&id='+ id;
        jQuery("#messenger_form").html(toeLoadElement);
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.html) {
                    jQuery('#messenger_form .subscreen_loading').parent().html(res.html);
                }
            }
        });
    });
    // sortable product and user fields
    jQuery( "#toe_opt_productFieldsTab, #toe_opt_userFieldsTab" ).sortable({ 
            items: 'tr.toe_admin_row',
            cursor: 'move', 
            forcePlaceholderSize: true,
            update: function(event, ui) {
                    var fields = [];
                    jQuery('tr.toe_admin_row', this).each(function(index){
                            field_id = jQuery(this).find('td:first').text();
                            fields[index] = field_id;
                    });
                   var data = {
                        fields   : fields,
                        action   : 'sortExtraField',
                        page     : 'options',
                        reqType  : 'ajax'
                    };
                   jQuery.post(ajaxurl,data);
            }
    });
    // show needed blocks at extra field add form
	
	
	

    jQuery('body').on('change', '.extrafield_type', function(){
		
	var htmlSelect = '<tr id="selonoff"><td>First "Select":</td><td class="field_label"><select name="params[options][selonoff][0]"><option value="0">on</option><option value="1" selected="selected">off</option></select></td></tr>';
	jQuery('#selonoff').remove();
       
       var selector = '#'+jQuery(this).attr('rel'); 
       var parent = jQuery(selector);
       var type = jQuery(this).val();
        switch (type){
           case '1':
               jQuery(this).parent().parent().next('tr').show();
               parent.find('.options_tag').hide(TOE_DATA.animationSpeed);
           break;
           case '5':
              parent.find(".options_tag .add_option").text(TOE_LANG.add_checkbox);
              parent.find(".options_tag").show(TOE_DATA.animationSpeed);
              parent.find(".image_tag").hide(TOE_DATA.animationSpeed);
              jQuery(this).parent().parent().next('tr').hide(); 
           break;
           case '10':
              parent.find(".options_tag .add_option").text(TOE_LANG.add_radiobutton);
              parent.find(".options_tag").show(TOE_DATA.animationSpeed);
              parent.find(".image_tag").hide(TOE_DATA.animationSpeed);
              jQuery(this).parent().parent().next('tr').hide();
           break;
           case '9': case '12':
              parent.find(".options_tag .add_option").text(TOE_LANG.add_item);
              parent.find(".options_tag").show(TOE_DATA.animationSpeed);
              parent.find(".image_tag").hide(TOE_DATA.animationSpeed);
              jQuery(this).parent().parent().next('tr').hide();
			  jQuery('#editProductfieldForm table tr:eq(3)').after(htmlSelect);
           break;
           case '8':
              parent.find(".image_tag").show(TOE_DATA.animationSpeed);
              parent.find(".options_tag").hide(TOE_DATA.animationSpeed);
              jQuery(this).parent().parent().next('tr').hide();
               break;
           default:
               parent.find(".options_tag").hide(TOE_DATA.animationSpeed);
               parent.find(".image_tag").hide(TOE_DATA.animationSpeed);
               jQuery(this).parent().parent().next('tr').hide();
           break
        }
    });
    jQuery('body').on('change', '.toeMultipleSelectWithSelectAll', function(){
        var currValue = jQuery(this).val();
        if(currValue == 0) {
            var allOptionsSelected = jQuery(this).hasClass('toeAllSelected');
            if(allOptionsSelected) {
                jQuery(this).find('option').removeAttr('selected');
                jQuery(this).removeClass('toeAllSelected');
            } else {
                jQuery(this).find('option').attr('selected', 'selected');
                jQuery(this).addClass('toeAllSelected');
            }
        }
    });
	jQuery('body').on('click', '.toeDeselectMultipleSelectWithSelectAll', function(){
		jQuery('.toeMultipleSelectWithSelectAll[name="'+ jQuery(this).attr('bindTo')+ '"]').find('option').removeAttr('selected');
		return false;
	});
    // select all categories
    jQuery('body').on('click', '#editProductfieldForm input[type="checkbox"]', function(){
        var value = jQuery(this).val();
        var checked = jQuery(this).attr('checked');
        if (value == 'all') {
            if (checked == 'checked') {
                jQuery(this).parent().find('input').each(function(){
                    jQuery(this).attr('checked', 'checked'); 
                });
            } else {
                jQuery(this).parent().find('input').each(function(){
                    jQuery(this).removeAttr('checked'); 
                });
            }   
        }
    });
    // set id and class fields
    jQuery('body').on('click', '.set_properties', function(){
        jQuery('.attributes').show(TOE_DATA.animationSpeed);
        jQuery(this).hide(TOE_DATA.animationSpeed);
    });
    jQuery('body').on('click', '.tab_form h1', function(){
        if (jQuery('.tab_form form').is(':visible')){
            jQuery('.tab_form form').hide(TOE_DATA.animationSpeed);
        } else {
            jQuery('.tab_form form').show(TOE_DATA.animationSpeed);
        }
    });
	jQuery('body').on('change', 'select[name=destination\\[categories\\]\\[\\]]', function(){
			var check = jQuery("option:selected", this).val();
			if (typeof(check) == 'undefined') {
					jQuery("[bindto = destination\\[categories\\]\\[\\]]").html('Select All');
			} else {
					jQuery("[bindto = destination\\[categories\\]\\[\\]]").html('Deselect All');				
			}
	});
	
	jQuery('body').on('click', '[bindto = destination\\[categories\\]\\[\\]]', function(){
		if (jQuery(this).text() == 'Select All') {
			jQuery('select[name=destination\\[categories\\]\\[\\]] option').attr("selected", "selected");
			jQuery(this).text('Deselect All');
		} else {
			jQuery(this).text('Select All');
		}
	});
	
	jQuery('body').on('change', 'select[name=destination\\[pids\\]\\[\\]]', function(){
			var check = jQuery("option:selected", this).val();
			if (typeof(check) == 'undefined') {
					jQuery("[bindto = destination\\[pids\\]\\[\\]]").html('Select All');
			} else {
					jQuery("[bindto = destination\\[pids\\]\\[\\]]").html('Deselect All');				
			}
	});
	
	jQuery('body').on('click', '[bindto = destination\\[pids\\]\\[\\]]', function(){
		if (jQuery(this).text() == 'Select All') {
			jQuery('select[name=destination\\[pids\\]\\[\\]] option').attr("selected", "selected");
			jQuery(this).text('Deselect All');
		} else {
			jQuery(this).text('Select All');
		}
	});
});
// edit tax 
jQuery.fn.toeGetEditTax = function(e) {
    if(removeClicked) return;
    var id = jQuery(this).children('td:first').text();
    var data = 'reqType=ajax&page=taxes&action=getEditTax&id='+ id;
    //var loading = '<p class="subscreen_loading">'+TOE_LOADING+'</p>';
    jQuery("#tax_form").html(toeLoadElement);
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if(res.html) {
                jQuery('#tax_form .subscreen_loading').parent().html(res.html);
            }
        }
    });
}
// remove currency
function removeCurrency(remLink) {
    return removeOpt(remLink, 'reqType=ajax&page=currency&action=delete', 'toe_opt_currency');
}
// remove userfield
function removeUserfield(remLink) {
    return removeOpt(remLink, 'reqType=ajax&page=options&action=deleteUserfield', 'toe_opt_userfield');
}
// remove productfield
function removeProductfield(remLink) {
    return removeOpt(remLink, 'reqType=ajax&page=options&action=deleteProductfield', 'toe_opt_productfield');
}
// remove tax
function removeTax(remLink) {
    return removeOpt(remLink, 'reqType=ajax&page=taxes&action=delete', 'toe_opt_taxes');
}
// remove option
function removeOpt(remLink, url, trClass) {
    removeClicked = true;
    var id = parseInt(jQuery(remLink).parents('.'+ trClass).find('td:first').text());
    var data = url+ '&id='+id;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            removeClicked = false;
            if(!res.error) {
                jQuery('.'+ trClass).each(function(){
                    if(parseInt(jQuery(this).find('td:first').text()) == id) {
                        jQuery(this).remove();
                    }
                });
            }
        }
    });
    return false;
}
/**
 * Show description for options
 */
function toeOptShowDescription(description, x, y, moveToLeft) {
    if(typeof(description) != 'undefined' && description != '') {
        if(!jQuery('#toeOptDescription').size()) {
            jQuery('body').append('<div id="toeOptDescription"></div>');
        }
		if(moveToLeft)
			jQuery('#toeOptDescription').css('right', jQuery(window).width() - (x - 10));	// Show it on left side of target
		else
			jQuery('#toeOptDescription').css('left', x + 10);
        jQuery('#toeOptDescription').css('top', y);
        jQuery('#toeOptDescription').show(200);
        jQuery('#toeOptDescription').html(description);
    }
}
/**
 * Hide description for options
 */
function toeOptHideDescription() {
	jQuery('#toeOptDescription').removeAttr('toeFixTip');
    jQuery('#toeOptDescription').hide(200);
}
/**
 * This function will help us not to hide desc right now, but wait - maybe user will want to select some text or click on some link in it.
 */
function toeOptTimeoutHideDescription() {
	jQuery('#toeOptDescription').removeAttr('toeFixTip');
	setTimeout(function(){
		if(!jQuery('#toeOptDescription').attr('toeFixTip'))
			toeOptHideDescription();
	}, 500);
}
function toeRemoveTextFieldsDynamicTable(link) {
    jQuery(link).parents('.toeTextFieldDynamicRow:first').remove();
}
function toeAddTextFieldsDynamicTable(link, optsCount) {
    var newRow = jQuery(link).parents('.toeTextFieldsDynamicTable:first').find('.toeTextFieldDynamicRow:last').clone();
    jQuery(newRow).find('input').each(function(){                   //Govnokod? maybe... if so - try to do it better. good luck!
        var name = jQuery(this).attr('name');               //sites_3[3][0]
        var varName = name.substr(0, strpos(name, '['));    //sites_3
        name = name.substr(strpos(name, '[')+1);            //3][0]
        name = name.substr(0, name.length-1);               //3][0
        name = str_replace(name, '[', '');                  //3]0
        var currNameArr = name.split(']');                  //3,0
        if(optsCount == 2) {                                //It is realy govnokod - hardcode for admin - shipping - table rate
            currNameArr[2] = parseInt(currNameArr[2]) + 1;      //4,0
        } else {
            if(isNumber(currNameArr[0]))
                currNameArr[0] = parseInt(currNameArr[0]) + 1;      //4,0
            else if(isNumber(currNameArr[1]))
                currNameArr[1] = parseInt(currNameArr[1]) + 1;      //4,0
        }
        name = currNameArr.join('][');                      //4][0
        name = varName + '['+ name+ ']';
        jQuery(this).attr('name', name);
        jQuery(this).val('');
    });
    jQuery(newRow).find('input').val('');
    jQuery(link).parents('.toeTextFieldsDynamicTable:first').find('.toeTextFieldDynamicRow:last').after(newRow);
}
function toeChangeCurrencyViewSelect() {
    var newCurrViewArr = {};
    if(jQuery('input[name=symbol]').val() == '') {
        newCurrViewArr = toeDefaultCurrViewSelect;
    } else {
        var newCurrSymbol = jQuery('input[name=symbol]').val();
        for(var id in toeDefaultCurrViewSelect) {
            newCurrViewArr[id] = str_replace(toeDefaultCurrViewSelect[id], '$', newCurrSymbol);
        }
    }
    jQuery('select[name=currency_view]').toeRebuildSelect(newCurrViewArr, true, jQuery('input[name=currency_view]').val());

    //jQuery('input[name=currency_view]').val( jQuery('select[name=currency_view_select]').val() );
    //jQuery('input[name=price_view]').val( jQuery('select[name=price_view_select]').val() );

    //prostite za govnokod............
    jQuery('select[name=price_view]').children('option[value='+ jQuery('input[name=price_view]').val()+ ']').attr('selected', 'selected');
}
function toeCheckCurrencyPriceView(checkWhat) {
    if(typeof(checkWhat) == 'undefined')
        checkWhat = 'all';
    if(checkWhat == 'all' || checkWhat == 'currencyView') {
        var symbol = jQuery('input[name=symbol]').val();
        jQuery('input[name=symbol_left]').val('');
        jQuery('input[name=symbol_right]').val('');
        switch(jQuery('select[name=currency_view]').val()) {
            case '$1':
                jQuery('input[name=symbol_left]').val(symbol);
                break;
            case '$ 1':
                jQuery('input[name=symbol_left]').val(symbol+ ' ');
                break;
            case '1$':
                jQuery('input[name=symbol_right]').val(symbol);
                break;
            case '1 $':
                jQuery('input[name=symbol_right]').val(' '+ symbol);
                break;
        }
    }
    if(checkWhat == 'all' || checkWhat == 'priceView') {
        jQuery('input[name=symbol_point]').val('');
        jQuery('input[name=symbol_thousand]').val('');
        jQuery('input[name=decimal_places]').val('');
        switch(jQuery('select[name=price_view]').val()) {
            case '100 000.00':
                jQuery('input[name=symbol_point]').val('.');
                jQuery('input[name=symbol_thousand]').val(' ');
                jQuery('input[name=decimal_places]').val('2');
                break;
            case '100000.00':
                jQuery('input[name=symbol_point]').val('.');
                jQuery('input[name=decimal_places]').val('2');
                break;
            case '100 000,00':
                jQuery('input[name=symbol_point]').val(',');
                jQuery('input[name=symbol_thousand]').val(' ');
                jQuery('input[name=decimal_places]').val('2');
                break;
            case '100000,00':
                jQuery('input[name=symbol_point]').val(',');
                jQuery('input[name=decimal_places]').val('2');
                break;
            case '100.000,00':
                jQuery('input[name=symbol_point]').val(',');
                jQuery('input[name=symbol_thousand]').val('.');
                jQuery('input[name=decimal_places]').val('2');
                break;
            case '100 000':
                jQuery('input[name=symbol_thousand]').val(' ');
                break;
			case '100,000.00':
                jQuery('input[name=symbol_point]').val('.');
                jQuery('input[name=symbol_thousand]').val(',');
                jQuery('input[name=decimal_places]').val('2');
                break;
        }
    }
}
function toeSwitchModuleStatus(link) {
    notOnRowClicked = true;
    if(jQuery(link).parents('tr').find('td.type').html() == 'system') return;
    var id = getIdFromTable(link),
		code = jQuery(link).parents('tr').find('.code').html();
	if(toeUnregisteredModules && code) {
		for(var i in toeUnregisteredModules) {
			if(!toeUnregisteredModules[i]) continue;
			if(toeUnregisteredModules[i].code == code) {
				toeShowModuleActivationPopup(toeUnregisteredModules[i].plugName);
				return;
			}
		}
	}
    if(id) {
        jQuery(this).sendForm({
            data: {id: id, action: 'putModule', page: 'options', reqType: 'ajax', active: jQuery(link).hasClass('toeOptDisabled')},
            msgElID: 'toeOptModulesMsg',
            onSuccess: function(res) {
                if(toeTables['modules_list_payment']) {
                    toeTables['modules_list_payment'].redrawRow('id', res.data.id, res.data);
                }
                /*if(toeTables['modules_list_shipping']) {
                    toeTables['modules_list_shipping'].redrawRow('id', res.data.id, res.data);
                }*/
                if(toeTables['modules_list']) {
                    toeTables['modules_list'].redrawRow('id', res.data.id, res.data);
                }
            }
        });
    }
    return;
}
function toeSwitchUserfieldStatus(link) {
    notOnRowClicked = true;
    var id = getIdFromTable(link);
    if(id) {
        jQuery(this).sendForm({
            data: {id: id, 
                action: 'putUserfield', 
                page: 'options', 
                reqType: 'ajax', 
                active: jQuery(link).hasClass('toeOptDisabled') ? 1 : 0,
                ignore: ['mandatory', 'ordering']},
            msgElID: 'toeOptModulesMsg',
            onSuccess: function(res) {
                if(toeTables['user_fields_list']) {
                    toeTables['user_fields_list'].redrawRow('id', res.data.id, res.data);
                }
            }
        });
    }
}

function toeSwitchSpecialProductStatus(link) {
    notOnRowClicked = true;
    var id = getIdFromTable(link);
    if(id) {
        jQuery(this).sendForm({
            data: {id: id, 
                action: 'storeSpecialProduct', 
                page: 'special_products', 
                reqType: 'ajax', 
                active: jQuery(link).hasClass('toeOptDisabled') ? 1 : 0,
                ignore: ['absolute', 'mark_as_sale', 'apply_to']},
            msgElID: 'speshialProductsMsg',
            onSuccess: function(res) {
                if(toeTables['toe_special_products']) {
                    toeTables['toe_special_products'].redrawRow('id', res.data.id, res.data);
                }
            }
        });
    }
}
/**
 *retrives ID from table by link in row in which exist element with class "id"
 **/
function getIdFromTable(link) {
    return parseInt(jQuery(link).parents('tr').find('td.id').html());
}
function toeSpSwitchSelectBox(checkbox, boxId) {
    if(jQuery(checkbox).attr('checked') == 'checked')
        jQuery('#'+ boxId).slideDown(TOE_DATA.animationSpeed);
    else {
        jQuery('#'+ boxId).slideUp(TOE_DATA.animationSpeed);
        jQuery('label[for='+ jQuery(checkbox).attr('id')+ ']').attr('aria-pressed', 'false');
        jQuery('label[for='+ jQuery(checkbox).attr('id')+ ']').removeClass('ui-state-active');
    }
}
/**
 * Slider widget functions
 **/
function toeSliderCompleteSubmitNewFile(file, res) {
    if(res.error) {
        alert(res.errors[0]);
    } else {
        toeSliderDrawImageData(res.data);
    }
}
function toeSliderDrawImageData(data) {
    //alert(data.fieldName);
    var box = jQuery('#toeUploadbut_'+ data.fieldName).parents('.toeSliderWidgetOptions:first').find('.toeSliderImagesInputsBox:first');
    var newCell = jQuery(box).find('.toeSliderWidgetImagesInputsExample:first').clone();
    var newIdDataArr = jQuery(box).find('.toeSliderWidgetImagesInputs:last').find('input[type=text]:first').attr('name').split('__');
    var newId = newIdDataArr[1] == 'replId' ? 0 : parseInt(newIdDataArr[1])+1;
    if(data.type == 'product') {
        jQuery(newCell).find('img:first').remove();
        jQuery(newCell).find('input[type=text][name*="link"]:first').val('').hide().    //Hide input fields with it's label and <br> element
            prev('label').hide().               
            next('input').next('br').hide();
        jQuery(newCell).find('textarea:first').val('').hide().
            prev('label').hide().
            next('textarea').next('br').hide();
        jQuery(newCell).find('input[type=text][name*="title"]:first').attr('readonly', 'readonly');
    } else {
        jQuery(newCell).find('img:first').attr('src', data.path);
        jQuery(newCell).find('input[type=text][name*="link"]:first').val(data.link);
        jQuery(newCell).find('textarea:first').val(data.desc);
    }
    jQuery(newCell).find('input[type=hidden][name*="path"]:first').val(data.path);  //For product this will be product ID, for slides - path to image
    jQuery(newCell).find('input[type=text][name*="title"]:first').val(data.title);
    jQuery(newCell).find('input[type=text][name*="order"]:first').val(data.order);
    if(data.type != '' && typeof(data.type) != 'undefined')
        jQuery(newCell).find('input[type=hidden][name*="type"]:first').val(data.type);
    
    jQuery(newCell).find('input, textarea').removeAttr('disabled');
    jQuery(newCell).find('input, textarea').each(function(){
        var name = str_replace(jQuery(this).attr('name'), '__replId__', '__'+ newId+ '__');
        jQuery(this).attr('name', name);
    });
    jQuery(newCell).removeClass('toeSliderWidgetImagesInputsExample');
    jQuery(box).append(newCell);
}
function toeRemoveSlide(delElement) {
    var parentBox = jQuery(delElement).parents('.toeSliderWidgetImagesInputs:first');
    jQuery(delElement).sendForm({
        msgElID: 'none',
        data: {page: 'slider_widget', action: 'removeFile', reqType: 'ajax', filePath: jQuery(parentBox).find('img:first').attr('src')},
        onSuccess: function(res) {
            jQuery(parentBox).remove();
        }
    });
}
function toeSliderDrawImageDataList(imagesData, uniqBoxId) {
    if(imagesData.length) {
        var fieldName = jQuery('#'+ uniqBoxId).find('button:first').attr('id');
        for(var i = 0; i < imagesData.length; i++) {
            imagesData[i]['fieldName'] = str_replace(fieldName, 'toeUploadbut_', '');
            toeSliderDrawImageData(imagesData[i]);
        }
    }
}
function toeSliderGetProductsList(params) {
    if(typeof(params) != 'object') {
        params = {msgElID: '', uniqBoxId: ''};
    }
    jQuery(this).sendForm({
        msgElID: params.msgElID,
        data: {mod: 'slider_widget', action: 'getProductsListHtml', reqType: 'ajax', uniqBoxId: params.uniqBoxId},
        onSuccess: function(res) {
            if(res.html != '')
                subScreen.show(res.html);
        }
    });
}
function toeSliderSelectProducts(form) {
    var uniqBoxId = jQuery(form).find('input[name=uniqBoxId]:first').val();
    var fieldName = str_replace(jQuery('#'+ uniqBoxId).find('button:first').attr('id'), 'toeUploadbut_', '');

    var selectedProductsOptions = jQuery(form).find('select option:selected');
    if(jQuery(selectedProductsOptions).size()) {
        jQuery(selectedProductsOptions).each(function(){
            var prodAddData = {
                path: jQuery(this).val(),
                title: jQuery(this).html(),
                order: 0,
                fieldName: fieldName,
                type: 'product'
            };
            toeSliderDrawImageData(prodAddData);
        });
    }
    subScreen.hide();
}
function toeSliderAddProductHtml() {
    
}
function toeShowModuleEditForm(id) {
	var data = 'reqType=ajax&page=options&action=getEditModule&id='+ id;
	subScreen.simpleShow(toeLoadElement);
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'GET',
		dataType: 'json',
		success: function(res) {
			if(res.html) {
				subScreen.simpleShow(res.html);
			}
		}
	});
}
// For nultiple valued fields types there should be multiple select list
function toeDetectProductFieldDefaultType() {
	this.multipleSelectSize;
	if(!this.multipleSelectSize) {
		this.multipleSelectSize = jQuery('#toeProdFieldDefaultValueSelect').attr('size');
	}
	jQuery('#toeProdFieldDefaultValueText, #toeProdFieldDefaultValueTextarea')
		.attr('disabled', 'disabled')
		.hide();
	switch(parseInt(jQuery('#editProductfieldForm').find('[name=htmltype_id]').val())) {
		case 5:		//Checkboxes
		case 12:	//List
		case 13:	//Country List with posibility to select multiple countries
			jQuery('#toeProdFieldDefaultValueSelect')
					.attr('multiple', 'multiple')
					.attr('name', 'default_value[]')
					.attr('size', this.multipleSelectSize)
					.removeAttr('disabled')
					.show();
			
			break;
		case 1:		// Text
		case 17:	// Textarea
			if(parseInt(jQuery('#editProductfieldForm').find('[name=htmltype_id]').val()) == 1) {
				jQuery('#toeProdFieldDefaultValueText')
					.removeAttr('disabled')
					.show();
			} else {
				jQuery('#toeProdFieldDefaultValueTextarea')
					.removeAttr('disabled')
					.show();
			}
			jQuery('#toeProdFieldDefaultValueSelect')
					.attr('disabled', 'disabled')
					.hide();
			break;
		default:
			jQuery('#toeProdFieldDefaultValueSelect')
					.removeAttr('multiple')
					.attr('name', 'default_value')
					.removeAttr('size')
					.removeAttr('disabled')
					.show();
			break;
	}
}