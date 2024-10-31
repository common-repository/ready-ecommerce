function toeSelectGift(gid) {
    if(!gid) return;
    jQuery(this).sendForm({
        data: {
            mod: 'gifts', action: 'getSelectProductHtml', id: gid, reqType: 'ajax'
        },
        onSuccess: function(res) {
            if(res.html) {
                subScreen.show( res.html, jQuery(document).scrollLeft() + 400, jQuery(document).scrollTop() + 200 );
            }
        }
    });
}
/**
 *Add product to cart via ajax request
 *@param object form - html form that contains all needed data for product adding
 *@param string subScreenTitle - if defined and non empty string - we will show this as title on sub-screen that will appear right after product has been added
 *@param bool hidePopup - if true - popup with result will be hidden
 **/
function toeAddToCart(form, subScreenTitle, hidePopup) {
    if(typeof(hidePopup) == 'undefined')
        hidePopup = !parseInt(TOE_DATA.options.dialog_after_prod_add.value);
    jQuery(form).sendForm({
        msgElID: jQuery(form).find('.toeAddToCartMsg:first'),
        onSuccess: function(res) {
            if(!res.error) {
                if(res.data.redirect) {
                    toeRedirect(res.data.redirect);
                    return;
                }
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
function updateCart(data, totalHtml) {
    var reloadPage = false;
    if(data.length) {
        var deletedCount = 0;
        for(i = 0; i < data.length; i++) {
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
                jQuery('.cart_row_'+ data[i]['inCartId']).find('.toeProdOutOfStock').show('slow');
            }
            if(data[i]['inOfStock']) {
                jQuery('.cart_row_'+ data[i]['inCartId']).find('.toeProdOutOfStock').hide('slow');
            }
        }
        if(deletedCount == data.length) {
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
            if(params.reload) 
                toeReload();
            else
                toeUpdateCartWidgets();
        }
    });
}
function toeSetNavigationSelected(select, animatioSpeed) {
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
    }
    jQuery(optEl).sendForm({
        msgElID: jQuery(prodForm).find('.toeAddToCartMsg'),
        data: data,
        onSuccess: function(res) {
            if(res.html) {
                var priceBox = jQuery(optEl).parents('.product_block_wrapper:first').find('.product_price:first');  //For categories page
                if(!jQuery(priceBox).size())
                    priceBox = jQuery('#product_price');        //For products page
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
jQuery(document).ready(function(){
	jQuery('.change_main').click(function(e){
	   e.preventDefault();
	   var new_main = jQuery(this);
	   jQuery('.product_full_image').find('a').attr('href', new_main.attr('href'));
	   jQuery('.product_full_image').find('img').attr('src', new_main.attr('href'));
	});
	if (jQuery('#product_tabs').length > 0) {
		 jQuery('#product_tabs').tabs();
	}
	if(jQuery('.toeProductOptions').size()) {
		jQuery('.toeProductOptions').change(function(){
			toeUpdateProductPrice(this);
		});
	}
	jQuery('.toeForgotPasswordLink').click(function(){
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
    jQuery('.slider_prev').click(function(){
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
    
    jQuery('.slider_next').click(function(){
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
    jQuery('.product-thumb-image').click(function(e){
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
    
});