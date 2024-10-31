function toeSelectGift(gid, topAlign, title) {
    if(!gid) return;
    jQuery(this).sendForm({
        data: {
            mod: 'gifts', action: 'getSelectProductHtml', id: gid, reqType: 'ajax'
        },
        onSuccess: function(res) {
            if(res.html) {
				if(topAlign) {
					subScreen.show( res.html, 0, 0, function(){
						subScreen.moveTopCenter(true)
							.setAbsolute();
					} );
				} else
					subScreen.show( res.html, jQuery(document).scrollLeft() + 400, jQuery(document).scrollTop() + 200 );
				}
				if(title) {
					subScreen.insertTitle(toeLang(title));
				}
            }
    });
}
/**
 * Add product to cart via ajax request
 * @param object form - html form that contains all needed data for product adding
 * @param string subScreenTitle - if defined and non empty string - we will show this as title on sub-screen that will appear right after product has been added
 * @param bool hidePopup - if true - popup with result will be hidden
 * @param object opts.onSuccess - custom function to run onSuccess
 */
function toeAddToCart(form, subScreenTitle, hidePopup, opts) {
    if(typeof(hidePopup) == 'undefined')
        hidePopup = !parseInt(toeOption('dialog_after_prod_add'));
	var gotoOnError = jQuery(form).find('[name*="options["]').size() > 0 // If there are no options selection for this formn on page - should redirect to product details page
		? false 
		: 'product';
	opts = jQuery.extend({}, opts);
    jQuery(form).sendForm({
        msgElID: jQuery(form).find('.toeAddToCartMsg:first'),
		appendData: {gotoOnError: gotoOnError},
        onSuccess: function(res) {
			if(opts.onSuccess && typeof(opts.onSuccess) == 'function') {
				opts.onSuccess(form, res);
			}
			if(res.data.redirect) {
				toeRedirect(res.data.redirect);
				return;
			}
            if(!res.error) {
                if(res.html && !hidePopup) {
                    var msgPos = jQuery(form).find('input[type=submit][name=add]:first').offset();
                    subScreen.show(res.html, msgPos.left, msgPos.top);
                    if(typeof(subScreenTitle) != 'undefined' && subScreenTitle)
                        subScreen.insertTitle(subScreenTitle);
                }
                toeUpdateCartWidgets();
            }
        }
    });
}
function toeUpdateCartWidgets() {
    jQuery(this).sendForm({
        data: {
            mod: 'user', action: 'getShoppingCartHtmlAjax', reqType: 'ajax'
        },
        onSuccess: function(res) {
            if(res.error) {
                alert(res.error);
            } else if(res.html) {
                jQuery('.toeCartWidgetShell').html(res.html);
            }
        }
    });
}

/**
 * Update shopping cart html on client side
 * @param data array - product that was updated/deleted
 * @param totalHtml string - html for total box
 * @param newCartData array - current data in shopping cart inluding last update/delete
 */
function updateCart(data, totalHtml, newCartData) {
    var reloadPage = false;
	newCartData = jQuery.extend([], newCartData);
    if(data.length) {
        var deletedCount = 0;
        for(var i = 0; i < data.length; i++) {
			if(!data[i])
				continue;
            if(data[i]['deleted']) {
                jQuery('.cart_row_'+ data[i]['inCartId']).hide(TOE_DATA.animationSpeed, function(){
                    jQuery(this).remove();
                });
                deletedCount++;
            }
            if(data[i]['total']) {
                jQuery('.total_'+ data[i]['inCartId']).html(data[i]['total']);
            }
            if(data[i]['outOfStock']) {
                jQuery('.cart_row_'+ data[i]['inCartId']).find('.toeProdOutOfStock').show(TOE_DATA.animationSpeed);
            }
            if(data[i]['inOfStock']) {
                jQuery('.cart_row_'+ data[i]['inCartId']).find('.toeProdOutOfStock').hide(TOE_DATA.animationSpeed);
            }
        }
        if(!newCartData.length) {
            reloadPage = true;
            document.location.reload();
        } else {
            toeUpdateCartWidgets();
        }
    }
    /* Update total box on cart's page */
    if(!reloadPage && typeof(totalHtml) != 'undefined') {
        jQuery('#toeCartTotalBox').html(totalHtml);
    }
}
/**
 * Clear shopping cart
 * @param params.reload bool if true - page will be reloaded
 **/
function toeClearCart(params) {
    jQuery(this).sendForm({
        data: {
            mod: 'user', action: 'clearCart', reqType: 'ajax'
        },
        msgElID: jQuery('.toeCartMsg'),
        onSuccess: function(res) {
            if(params.reload || toePages('isCart') || toePages('isCheckoutStep1')) 
                toeReload();
            else
                toeUpdateCartWidgets();
        }
    });
}
function toeSetNavigationSelected(select, animatioSpeed) {
	if(select == 'success')			// Update shopping cart widgets on success checkout - at this moment we cleared a shopping cart
		toeUpdateCartWidgets();
    if(typeof(animatioSpeed) == 'undefined')
        animatioSpeed = 1000;                 //Animation speed by default
    var dest = jQuery('.toeCheckoutNavigationItemPoint.'+ select).position();
    if(!dest)
        return;
    var destWidth = jQuery('.toeCheckoutNavigationItemPoint.'+ select).width();
    var destHeight = jQuery('.toeCheckoutNavigationItemPoint.'+ select).height();
    var selectedWidth  = jQuery('.toeCheckoutNavigationSelected').width();
    var selectedHeight  = jQuery('.toeCheckoutNavigationSelected').height();

    dest.left += Math.abs(destWidth-selectedWidth)/2;
    dest.top += Math.abs(destHeight-selectedHeight)/2;

    jQuery('.toeCheckoutNavigationSelected').animate({
        left: dest.left,
        top: dest.top
    }, animatioSpeed, function(){
        jQuery(this).show();
    });

    jQuery('.toeCheckoutNavigationItem').removeClass('toeCheckoutNavigationCurrent');  //Remove prev. current steps, that was current in prev. action
    //jQuery('.toeCheckoutNavigationItemPoint').removeClass('toeCheckoutNavigationCurrent');  //Remove prev. current steps, that was current in prev. action
    jQuery('.toeCheckoutNavigationItemPoint.'+ select).parents('.toeCheckoutNavigationItem:first').addClass('toeCheckoutNavigationCurrent');
}
function toeSetNavigationPassed(select) {
    if(typeof(select) == 'string')
        select = [select];
    for(var i = 0; i < select.length; i++) {
        if(!jQuery('.toeCheckoutNavigationItemPoint.'+ select[i]).parents('.toeCheckoutNavigationItem:first').hasClass('toeCheckoutNavigationPassed')) {
            jQuery('.toeCheckoutNavigationItemPoint.'+ select[i]).parents('.toeCheckoutNavigationItem:first').addClass('toeCheckoutNavigationPassed');
        }
    }
}
function toeUpdateProductPrice(optEl) {
    var prodForm = jQuery(optEl).parents('.toeAddToCartForm:first');
    var data = jQuery(prodForm).serializeArray();
    for(var i in data) {
        if(data[i].name == 'mod')
            data[i].value = 'currency';
        if(data[i].name == 'action')
            data[i].value = 'displayTotal';
		if(data[i].name == 'qty')
            data[i].value = 1;				// For one item only
    }
    jQuery(optEl).sendForm({
        msgElID: jQuery(prodForm).find('.toeAddToCartMsg'),
        data: data,
        onSuccess: function(res) {
            if(res.html) {
                var priceBox = jQuery(optEl).parents('.product_info:first').find('.product_price:first');  //For categories page
                if(!jQuery(priceBox).size())
                    priceBox = jQuery('.product_price');        //For products page
				if(!jQuery(priceBox).size())
                    priceBox = jQuery('#product_price');        // Try for old types...............
                jQuery(priceBox).html( res.html );
            }
        }
    });
}
function toePasswordRecover(form) {
	jQuery(form).sendForm({
		msgElID: 'toePasswordForgotMsg',
		onSuccess: function(res) {
			if(res.error) {
				if(res.errors['email'] && Recaptcha) {
					Recaptcha.reload();
				}
			} else {
				jQuery(form).clearForm();
			}
		}
	});
} 
function toeAddSubscriber(form, successElement) {
	jQuery(form).sendForm({
		msgElID: 'mod_msg_subscriber',
		onSuccess: function(res) {
			if(res.html) {
				if(successElement) {
					jQuery(successElement).html(res.html);
				} else {
					jQuery(form).clearForm();
					jQuery('#mod_msg_subscriber').html(res.html);
				}
			}
		}
	});
	return false;
}


jQuery(document).ready(function(){
	jQuery('body').on('change', '.toeVariationsSelectList', function(){
		if(jQuery(this).attr('update_other'))
			return;
		var variationId = jQuery(this).find(':selected').attr('value')
		,	parentId = jQuery(this).parent().find('.variation_parent_id').val()
		,	variatioCurrentId = jQuery(this).parent().find('.variation_current_id')
		,	singleProductShell = jQuery(this).parents('.toeSingleProductShell:first')
		,	variationQuickChange = singleProductShell.find('input[type=hidden][name=variation_quick_change]');
		
		if(variationQuickChange && variationQuickChange.size() && variationQuickChange.val() == 1) {
			// This attr should be added to all selects during their update - to avoid recursion
			jQuery('.toeVariationsSelectList').attr('update_other', 1);
			jQuery('.toeVariationsSelectList').val( variationId );
			// Make update for customized foundation framework selects - as we often use them in our themes
			if(Foundation && Foundation.libs && Foundation.libs.forms && Foundation.libs.forms.refresh_custom_select) {
				jQuery('.toeVariationsSelectList').not(this).each(function(){
					Foundation.libs.forms.refresh_custom_select(jQuery(this), true);
				});
			}
			jQuery('.toeVariationsSelectList').removeAttr('update_other');
			if(variationId == 0)
				variationId = parentId;
			singleProductShell.find('input[name=pid]').val( variationId );
			return;
		}
		singleProductShell.hide(TOE_DATA.animationSpeed);

		jQuery(this).sendForm({
			data: {
				mod: 'products', action: 'getPostVariation', reqType: 'ajax', variation_id: variationId , parent_id: parentId
			},
			onSuccess: function(res) {
				if(variationId == 0)
					variationId = parentId;
				variatioCurrentId.val(variationId);
				singleProductShell.replaceWith(res.html).show(TOE_DATA.animationSpeed);
			}
		});
	});
	jQuery('body').on('click', '.change_main', function(e){
	   e.preventDefault();
	   var new_main = jQuery(this);
	   jQuery('.product_full_image').find('a').attr('href', new_main.attr('href'));
	   jQuery('.product_full_image').find('img').attr('src', new_main.attr('href'));
	});
	// Old way of product tabs definition - by ID, but we still need to support it
	if (jQuery('#product_tabs').size() > 0) {
		 jQuery('#product_tabs').tabs();
	}
	// New way of tab definition - by class
	if (jQuery('.product_tabs').size() > 0) {
		 jQuery('.product_tabs').tabs();
	}
	jQuery('body').on('change', '.toeProductOptions', function(){
		toeUpdateProductPrice(this);
	});
	if(jQuery('.toeProductOptions').size()) {
		toeUpdateProductPrice(jQuery('.toeProductOptions:first'));	// Any option element - it need only to detect parent form element
	}
	jQuery('body').on('click', '.toeForgotPasswordLink', function(){
		jQuery(this).sendForm({
			msgElID: 'toeLoginMsg',
			data: {mod: 'user', action: 'getPasswordForgotFormHtml', reqType: 'ajax'},
			onSuccess: function(res) {
				if(!res.error && res.html) {
					subScreen.show(res.html);
				}
			}
		});
		return false;
	});
    
    /* Default theme scripts */ 
    // Clear some default tags
    jQuery('.tax-products_categories .product').find('header').remove();
    jQuery('.tax-products_categories .product').find('footer').remove();
    jQuery('.single-product #nav-single').remove();
    jQuery('.single-product .edit-link').remove();
    
    //Slider moving
    jQuery('body').on('click', '.slider_prev', function(){
        var sliderObj = jQuery(this).parent().find('.content-slider');
        var countItem = sliderObj.find('li').length;
        var firstChild = sliderObj.find('li:first-child');
        var objWidth = (firstChild.css('marginLeft').replace('px',''))*1 + (firstChild.css('marginRight').replace('px',''))*1 + firstChild.width();
        if (sliderObj.css('left') != '0px') {
            sliderObj.animate({left:'+='+objWidth},500);
        } else {
            var i = -1;
            var summ = 0;
            do {
                summ += objWidth;
                i++;
            } while(summ < 302);
            var newLeftPost = (countItem-i)*objWidth;
            sliderObj.animate({left:-newLeftPost},500);
        }
    });
    
    jQuery('body').on('click', '.slider_next', function(){
        var sliderObj = jQuery(this).parent().find('.content-slider');
        var countItem = sliderObj.find('li').length;
        var firstChild = sliderObj.find('li:first-child');
        var objWidth = (firstChild.css('marginLeft').replace('px',''))*1 + (firstChild.css('marginRight').replace('px',''))*1 + firstChild.width();
        var maxLeftPost = (sliderObj.css('left').replace('px',''))*1 + (countItem*objWidth);
        if (maxLeftPost >= 302) {
            sliderObj.animate({left:'-='+objWidth},500);
        } else {
            sliderObj.animate({left:0},500);
        }
    });
    
    jQuery("#all-prod-images a:first-child").attr("rel"," ");
    jQuery('body').on('click', '.product-thumb-image', function(e){
        e.preventDefault(); 
        var bigImage = jQuery(this).attr('href');
        var largeImage = jQuery(this).attr('alt');
       
        jQuery("#all-prod-images a").each(function(){
            var link = jQuery(this).attr('href');
            if (link == largeImage) {
                jQuery(this).attr("rel"," ");
            } else {
                jQuery(this).attr("rel","lightbox[product]");
            }
        });
       
        jQuery(".full_image_wrapper .back-img").animate({opacity: 0}, 250, function(){
            jQuery(".full_image_wrapper .back-img").attr("src", bigImage).delay(500);
            jQuery(".full_image_wrapper a").attr("href", largeImage);
            jQuery(".full_image_wrapper .back-img").animate({opacity: 1},250);
        });  
    });
    
    // placeholder fix
	if(jQuery('input[placeholder], textarea[placeholder]').placeholder)
		jQuery('input[placeholder], textarea[placeholder]').placeholder();

    if(toePages('isCheckoutStep1')) {
		var toeCheckoutNamesForReload = ['billing_country', 'billing_state', 'shipping_country', 'shipping_state', 'shipping_module', 'billing_zip', 'shipping_zip'];
		for(var i = 0; i < toeCheckoutNamesForReload.length; i++) {
			toeCheckoutNamesForReload[i] = '[name='+ toeCheckoutNamesForReload[i]+ ']';
		}
		jQuery(toeCheckoutNamesForReload.join(','))
			.on('change', function(){
				toeUpdateTotalOnCheckout(this);
			});
	}
});
function toeUpdateTotalOnCheckout(changedElement) {
	// Let's sent all form data to recalculate total block
	// If shipping is same as billing - just setup them at first - make it equals
	var changedElName = jQuery(changedElement).attr('name')
	,	changeElVal = jQuery(changedElement).val();
	if(jQuery('#toeShippingSameAsBilling').attr('checked')) {
		switch(changedElName) {
			case 'billing_country':
				jQuery('[name=shipping_country]').val( changeElVal ).trigger('keyup');
				break;
			case 'billing_state':
				jQuery('[name=shipping_state]').val( changeElVal );
				break;
			case 'billing_zip':
				jQuery('[name=shipping_zip]').val( changeElVal );
				break;
		}
	}
	// Receive all data as object
	var dataSend = parseStr(jQuery('#toe_checkout_form_1').serialize());
	// Substitute mod and action - to send it to another controller
	dataSend.mod = 'checkout';
	dataSend.action = 'getTotalHtml';
	jQuery.sendForm({
		msgElID: 'msg',
		data: dataSend,
		onSuccess: function(res) {
			if(res.html && res.html != '') {
				jQuery('.toe_checkout_part_totalBlock').html(res.html);
			}
			if(res.data.shipping_module_options && !jQuery('.toeAdditionalShippingOptionsShell').size()) {
				var shippingModuleInput = jQuery('#toe_checkout_form_1').find('[name=shipping_module]');
				if(shippingModuleInput.size()) {
					var shippingModuleElement = shippingModuleInput.attr('type') == 'radio' ? jQuery('#toe_checkout_form_1').find('[name=shipping_module]:checked') : shippingModuleInput;
					if(shippingModuleElement.size()) {
						jQuery('.toeAdditionalShippingOptionsShell').remove();
						shippingModuleElement.parents('.ModListButtons:first').after(
							jQuery('<div class="toeAdditionalShippingOptionsShell toeInputError" style="display: none;"></div>')
								.append(res.data.shipping_module_options)
								.show( TOE_DATA.animationSpeed )
						);
						jQuery('#toe_checkout_form_1').find('[name=shipping_module_options]').change(function(){
							toeUpdateTotalOnCheckout(this, true);
						});
					}
				}
			}
			if(changedElName !== 'shipping_module')
				toeHideUnavailableShipping(res.data.unavailable_shipping_method_ids);
		}
	});
}
/**
 * Hide unavailable for user selected address shipping methods
 */
function toeHideUnavailableShipping(unavailableShippingIds) {
	jQuery('#toe_checkout_form_1').find('[name=shipping_module]').each(function(){
		if(jQuery.inArray(parseInt(jQuery(this).val()), unavailableShippingIds) != -1) {
			if(jQuery(this).parents('.ModListButtons:first').is(':visible')) {
				jQuery(this).parents('.ModListButtons:first').hide( TOE_DATA.animationSpeed );
			}
		} else {
			if(!jQuery(this).parents('.ModListButtons:first').is(':visible')) {
				jQuery(this).parents('.ModListButtons:first').show( TOE_DATA.animationSpeed ).css('display', 'inherit');	// .css('display', 'inherit'); we need this here as jquery made it display: inline or something like that and it break some parts of tempalte
			}
		}
	});
}

function toeUpdateShoppingCartOnCheckout() {
	jQuery.sendForm({
		magElID: 'msg',
		data: {mod: 'user', action: 'getShoppingCartHtmlForCheckoutAjax', reqType: 'ajax'},
		onSuccess: function(res) {
			if(!res.error && res.html) {
				jQuery('.toe_checkout_part_summaryBlock').html( res.html );
			}
		}
	});
}