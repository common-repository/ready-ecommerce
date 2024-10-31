if(isNumber(TOE_DATA.animationSpeed)) 
    TOE_DATA.animationSpeed = parseInt(TOE_DATA.animationSpeed);
else if(jQuery.inArray(TOE_DATA.animationSpeed, ['fast', 'slow']) == -1)
    TOE_DATA.animationSpeed = 'fast';
TOE_DATA.showSubscreenOnCenter = parseInt(TOE_DATA.showSubscreenOnCenter);
var sdLoaderImg = '<img src="'+ TOE_DATA.loader+ '" />';
//array to hold toeListTable objects
var toeTables = new Array();
var toeStatesObjects = new Array();
var toeStates = new Array();
jQuery.fn.showLoader = function() {
    jQuery(this).html( sdLoaderImg );
}
jQuery.fn.appendLoader = function() {
    jQuery(this).append( sdLoaderImg );
}
jQuery.fn.nextInArray = function(element) {
    var nextId = 0;
    for(var i = 0; i < this.length; i++) {
        if(this[i] == element) {
            nextId = i + 1;
            break;
        }
    }
    if(nextId > this.length-1)
        nextId = 0;
    return this[nextId];
}
jQuery.sendForm = function(params) {
	// Any html element can be used here
	return jQuery('<br />').sendForm(params);
}
/**
 * Send form or just data to server by ajax and route response
 * @param string params.fid form element ID, if empty - current element will be used
 * @param string params.msgElID element ID to store result messages, if empty - element with ID "msg" will be used. Can be "noMessages" to not use this feature
 * @param function params.onSuccess funstion to do after success receive response. Be advised - "success" means that ajax response will be success
 * @param array params.data data to send if You don't want to send Your form data, will be set instead of all form data
 * @param array params.appendData data to append to sending request. In contrast to params.data will not erase form data
 * @param string params.inputsWraper element ID for inputs wraper, will be used if it is not a form
 * @param string params.clearMsg clear msg element after receive data, if is number - will use it to set time for clearing, else - if true - will clear msg element after 5 seconds
 */
jQuery.fn.sendForm = function(params) {
    var form = null;
    if(!params)
        params = {fid: false, msgElID: false, onSuccess: false};

    if(params.fid)
        form = jQuery('#'+ fid);
    else
        form = jQuery(this);
    
    /* This method can be used not only from form data sending, it can be used just to send some data and fill in response msg or errors*/
    var sentFromForm = (jQuery(form).tagName() == 'FORM');
    var data = new Array();
    if(params.data)
        data = params.data;
    else if(sentFromForm)
        data = jQuery(form).serialize();
    
    if(params.appendData) {
		var dataIsString = typeof(data) == 'string';
		var addStrData = [];
        for(var i in params.appendData) {
			if(dataIsString) {
				addStrData.push(i+ '='+ params.appendData[i]);
			} else
            data[i] = params.appendData[i];
        }
		if(dataIsString)
			data += '&'+ addStrData.join('&');
    }
    var msgEl = null;
    if(params.msgElID) {
        if(params.msgElID == 'noMessages')
            msgEl = false;
        else if(typeof(params.msgElID) == 'object')
           msgEl = params.msgElID;
       else
            msgEl = jQuery('#'+ params.msgElID);
    } else
        msgEl = jQuery('#msg');
	if(typeof(params.inputsWraper) == 'string') {
		form = jQuery('#'+ params.inputsWraper);
		sentFromForm = true;
	}
	if(sentFromForm && form) {
        jQuery(form).find('*').removeClass('toeInputError');
    }
	if(msgEl) {
		jQuery(msgEl).removeClass('toeSuccessMsg')
			.removeClass('toeErrorMsg')
			.showLoader();
	}
    var url = '';
	if(typeof(params.url) != 'undefined')
		url = params.url;
    else if(typeof(ajaxurl) == 'undefined')
        url = TOE_DATA.ajaxurl;
    else
        url = ajaxurl;
    
    jQuery('.toeErrorForField').hide(TOE_DATA.animationSpeed);
	var dataType = params.dataType ? params.dataType : 'json';
    jQuery.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: dataType,
        success: function(res) {
            toeProcessAjaxResponse(res, msgEl, form, sentFromForm, params);
			if(params.clearMsg) {
				setTimeout(function(){
					jQuery(msgEl).animateClear();
				}, typeof(params.clearMsg) == 'boolean' ? 5000 : params.clearMsg);
			}
        }
    });
}
/**
 * Hide content in element and then clear it
 */
jQuery.fn.animateClear = function() {
	var newContent = jQuery('<span>'+ jQuery(this).html()+ '</span>');
	jQuery(this).html( newContent );
	jQuery(newContent).hide(TOE_DATA.animationSpeed, function(){
		jQuery(newContent).remove();
	});
}
/**
 * Hide content in element and then remove it
 */
jQuery.fn.animateRemove = function(animationSpeed) {
	animationSpeed = animationSpeed == undefined ? TOE_DATA.animationSpeed : animationSpeed;
	jQuery(this).hide(animationSpeed, function(){
		jQuery(this).remove();
	});
}
function toeProcessAjaxResponse(res, msgEl, form, sentFromForm, params) {
    if(typeof(params) == 'undefined')
        params = {};
    if(typeof(msgEl) == 'string')
        msgEl = jQuery('#'+ msgEl);
    if(msgEl)
        jQuery(msgEl).html('');
    /*if(sentFromForm) {
        jQuery(form).find('*').removeClass('toeInputError');
    }*/
    if(typeof(res) == 'object') {
        if(res.error) {
            if(msgEl) {
                jQuery(msgEl).removeClass('toeSuccessMsg')
					.addClass('toeErrorMsg');
            }
            for(var name in res.errors) {
                if(sentFromForm) {
                    jQuery(form).find('[name*="'+ name+ '"]').addClass('toeInputError');
                }
                if(jQuery('.toeErrorForField.toe_'+ nameToClassId(name)+ '').exists())
                    jQuery('.toeErrorForField.toe_'+ nameToClassId(name)+ '').show().html(res.errors[name]);
                else if(msgEl)
                    jQuery(msgEl).append(res.errors[name]).append('<br />');
            }
        } else if(res.messages.length) {
            if(msgEl) {
                jQuery(msgEl).removeClass('toeErrorMsg')
					.addClass('toeSuccessMsg');
                for(var i in res.messages) {
                    jQuery(msgEl).append(res.messages[i]).append('<br />');
                }
            }
        }
    }
    if(params.onSuccess && typeof(params.onSuccess) == 'function') {
        params.onSuccess(res);
    }
}
jQuery.fn.fillForm = function(data) {
    
}
function subScreenClass() {
    this.mouse = {x: 0, y: 0, down: false};
	this.topAdmiBarY = 50;	// Height in top admin 
}
subScreenClass.prototype.autoScroll = function() {
	var docScrollY = jQuery(window).scrollTop();
	if(docScrollY) {
		var y = parseFloat(subScreen.getY());
		if(isNaN(y))
			y = 0;
		subScreen.moveY( this.topAdmiBarY + docScrollY + y );
	}
	return subScreen;
}
subScreenClass.prototype.move = function(x, y) {
    jQuery('#toe_sub_screen').css('left', x+ 'px');
    jQuery('#toe_sub_screen').css('top', y+ 'px');
	return subScreen;
}
subScreenClass.prototype.moveX = function(x) {
	jQuery('#toe_sub_screen').css('left', x+ 'px');
	return subScreen;
}
subScreenClass.prototype.moveY = function(y) {
	jQuery('#toe_sub_screen').css('top', y+ 'px');
	return subScreen;
}
subScreenClass.prototype.getX = function(x) {
	return jQuery('#toe_sub_screen').css('left');
}
subScreenClass.prototype.getY = function(y) {
	return jQuery('#toe_sub_screen').css('top');
}
subScreenClass.prototype.moveToCenter = function() {
	var x = (jQuery(window).width() / 2) - (jQuery('#toe_sub_screen').width() / 2);
	var y = (jQuery(window).height() / 2) - (jQuery('#toe_sub_screen').height() / 2);
	subScreen.move(x, y);
	return subScreen;
}
subScreenClass.prototype.moveToCenterX = function() {
	var x = (jQuery(window).width() / 2) - (jQuery('#toe_sub_screen').width() / 2);
	subScreen.moveX(x);
	return subScreen;
}
subScreenClass.prototype.moveToCenterY = function() {
	var y = (jQuery(window).height() / 2) - (jQuery('#toe_sub_screen').height() / 2);
	subScreen.moveY(y);
	return subScreen;
}
subScreenClass.prototype.moveTopCenter = function(ignoreSetWidth) {
	subScreen.setFixed();
	/*if(!ignoreSetWidth)
		subScreen.setWidth('80%');*/
	subScreen.moveToCenterX();
	subScreen.moveY( this.topAdmiBarY );
	return subScreen;
}
subScreenClass.prototype.setWidth = function(width) {
	jQuery('#toe_sub_screen').width(width);
	return subScreen;
}
subScreenClass.prototype.hide = function() {
    jQuery('#toe_sub_screen_bg').hide();
    jQuery('#toe_sub_screen').hide(TOE_DATA.animationSpeed);
    jQuery('#toe_sub_screen_content').html('');
	return subScreen;
}
subScreenClass.prototype.show = function(content, x, y, onAfterShow, title){
    if(!content) return false;
    x = x ? x : 0;
    y = y ? y : 0;
    
    subScreen.move(x, y);
    subScreen.insertTitle(title);
    subScreen.insertContent(content);
    
    jQuery('#toe_sub_screen_bg').show();
    jQuery('#toe_sub_screen').show(TOE_DATA.animationSpeed, function(){
        subScreen.resizeBg();
        if(typeof(onAfterShow) == 'function')
            onAfterShow(this);
    });
	return subScreen;
}
subScreenClass.prototype.clear = function() {
	jQuery('#toe_sub_screen_content').html('');
	return subScreen;
}
subScreenClass.prototype.clearAndHide = function() {
	subScreen.clear();
	subScreen.hide();
	return subScreen;
}
subScreenClass.prototype.setFixed = function() {
	jQuery('#toe_sub_screen').css('position', 'fixed');
	jQuery('#toe_sub_screen').css('marginLeft', '0');
	jQuery('#toe_sub_screen').css('marginTop', '0');
	return subScreen;
}
subScreenClass.prototype.setAbsolute = function() {
	jQuery('#toe_sub_screen').css('position', 'absolute');
	return subScreen;
}
subScreenClass.prototype.insertContent = function(content, ignoreCenter) {
    jQuery('#toe_sub_screen_content').html(content);
    if(!ignoreCenter) {
		var setToCenter = false;
		if(TOE_DATA.showSubscreenOnCenter) {
			var newWidth = jQuery('#toe_sub_screen').width();
			var newHeight = jQuery('#toe_sub_screen').height();
			var windowHeight = jQuery(window).height();
			if(newHeight < windowHeight) {
				setToCenter = true;
			}
		}
		if(setToCenter) {
			jQuery('#toe_sub_screen').css('position', 'fixed');
			jQuery('#toe_sub_screen').css('top', '50%');
			jQuery('#toe_sub_screen').css('left', '50%');
			jQuery('#toe_sub_screen').css('marginLeft', '-'+ (newWidth/2)+ 'px');
			jQuery('#toe_sub_screen').css('marginTop', '-'+ (newHeight/2)+ 'px');
		} else {
			jQuery('#toe_sub_screen').css('position', 'absolute');
			jQuery('#toe_sub_screen').css('marginLeft', '0');
			jQuery('#toe_sub_screen').css('marginTop', '0');
		}
	}
	return subScreen;
}
subScreenClass.prototype.insertTitle = function(title) {
    if(typeof(title) == 'undefined') {
        title = '';
    } 
    jQuery('#toe_sub_screen_title').html(title);
	return subScreen;
}
subScreenClass.prototype.init = function(){
    jQuery('body').append('<div id="toe_sub_screen_bg" style="display: none;"></div><div id="toe_sub_screen"><div id="toe_sub_screen_header"><span id="toe_sub_screen_title"></span><a href="#" id="toe_sub_screen_close"></a></div><div id="toe_sub_screen_content"></div></div>');
    jQuery('#toe_sub_screen_header').mousedown(function(e){
        subScreen.mouse.down = true;
        subScreen.mouse.x = e.pageX;
        subScreen.mouse.y = e.pageY;
    });
    jQuery(document).mouseup(function(){
        subScreen.mouse.down = false;
        subScreen.mouse.x = 0;
        subScreen.mouse.y = 0;
    });
    jQuery(document).mousemove(function(e){
        if(subScreen.mouse.down) {
            var dX = e.pageX-subScreen.mouse.x;
            var dY = e.pageY-subScreen.mouse.y;
            var left = parseInt(jQuery('#toe_sub_screen').css('left'));
            var top = parseInt(jQuery('#toe_sub_screen').css('top'));
            subScreen.move(left + dX, top + dY);
            subScreen.mouse.x = e.pageX;
            subScreen.mouse.y = e.pageY;
        }
    });
    jQuery('#toe_sub_screen_bg').click(function(){
        subScreen.hide();
        return false;
    });
    jQuery('#toe_sub_screen_close').click(function(){
        subScreen.hide();
        return false;
    });
    subScreen.resizeBg();
}
subScreenClass.prototype.resizeBg = function() {
    var wWidth = jQuery(document).width();
    var wHeight = jQuery(document).height();
    jQuery('#toe_sub_screen_bg').width(wWidth+ 'px');
    jQuery('#toe_sub_screen_bg').height(wHeight+ 'px');
}
subScreenClass.prototype.simpleShow = function(content) {
	subScreen.show(content, 0, 0, function(){
		subScreen.moveTopCenter()
			.setAbsolute()
			.autoScroll();
	});
	return subScreen;
}
subScreen = new subScreenClass();

jQuery(document).ready(function(){
    subScreen.init();
});
jQuery.fn.clearForm = function() {
	return this.each(function() {
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
			return jQuery(':input', this).clearForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')
			this.value = '';
		else if (type == 'checkbox' || type == 'radio')
			this.checked = false;
		else if (tag == 'select') 
			this.selectedIndex = -1;
	});
}
jQuery.fn.copyFieldsValues = function(to, id) {
	var from = '';
	if(id)
		from = id;
	else
		from = jQuery(this).attr('id');
	var updateStates = new Array();
    jQuery('[name^='+ from+ ']').each(function(){
        var newName = jQuery(this).attr('name').replace(from, to);
		if(newName.indexOf('_module') == -1) {		// Don't copy modules value
			jQuery(this).val( jQuery(jQuery(this).tagName()+ '[name='+newName+']').val() );
			if(jQuery(this).tagName() == 'SELECT' && toeStatesObjects[newName]) {
				updateStates.push({
					name: jQuery(this).attr('name'), 
					val: jQuery('[name='+newName+']:not(disabled)').val() });
			}
		}
    });
	if(updateStates.length) {
		for(var i = 0; i < updateStates.length; i++) {
			//jQuery(toeStatesObjects[ updateStates[i].name ].countryObj).trigger('keyup');
			jQuery('select[name='+ updateStates[i].name+ ']').val( updateStates[i].val );
		}
	} 
}
jQuery.fn.tagName = function() {
    return this.get(0).tagName;
}
jQuery.fn.exists = function(){
    return (jQuery(this).size() > 0 ? true : false);
}
function isNumber(val) {
    return /^\d+/.test(val);
}
jQuery.fn.serializeAnything = function(addData) {
    var toReturn    = [];
    var els         = jQuery(this).find(':input').get();
    jQuery.each(els, function() {
        if (this.name && !this.disabled && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
            var val = jQuery(this).val();
            toReturn.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( val ) );
        }
    });
    if(typeof(addData) != 'undefined') {
        for(var key in addData)
            toReturn.push(key + "=" + addData[key]);
    }
    return toReturn.join("&").replace(/%20/g, "+");
}
 

function str_replace(haystack, needle, replacement) { 
	var temp = haystack.split(needle); 
	return temp.join(replacement); 
}
/**
 * @see php html::nameToClassId($name) method
 **/
function nameToClassId(name) {
    return str_replace(
        str_replace(name, ']', ''), 
            '[', ''
    );
}
function strpos( haystack, needle, offset){
    var i = haystack.indexOf( needle, offset ); // returns -1
    return i >= 0 ? i : false;
}
function extend(Child, Parent) {
    var F = function() { };
    F.prototype = Parent.prototype;
    Child.prototype = new F();
    Child.prototype.constructor = Child;
    Child.superclass = Parent.prototype;
}
function toeRedirect(url) {
    document.location.href = url;
}
function toeReload() {
    document.location.reload();
}
function toeListTable(table, data) {
    this.table = null;
    this.data = null;
    //If some data needs to be converted before insert
    this.converters = new Array();
    this.converters['active'] = this._convertActive;
    if(typeof(table) != 'undefined')
        this.init(table, data);
}
toeListTable.prototype.init = function(table, data) {
    if(typeof(table) == 'string') {
       table = jQuery('#'+ table);
    }
    this.table = table;
    this.data = data;
	return this;
}
toeListTable.prototype.draw = function(data) {
    if(typeof(data) == 'undefined')
        data = this.data;
    else {
        for(var i = 0; i < data.length; i++) {
            this.data.push(data[i]);
        }
    }
    var rowExample = jQuery(this.table).find('.toeRowExample');
    var newRow = null;
    for(var iter in data) {
        newRow = jQuery(rowExample).clone();
        jQuery(newRow).removeClass('toeRowExample');
        for(var id in data[iter]) {
            this._drawRow(newRow, id, data[iter][id]);
        }
        jQuery(this.table).append(newRow);
    }
	return this;
}
toeListTable.prototype.redrawRow = function(findId, findVal, data) {
    var listTableInstance = this;
    var result = false;
    jQuery(this.table).find('tr').each(function(){
        if(jQuery(this).find('.'+ findId).html() == findVal) {
            for(var id in data) {
                listTableInstance._drawRow(this, id, data[id]);
            }
            listTableInstance._updateData(findId, findVal, data);
            result = true;
            return;
        }
    });
    return result;
}
toeListTable.prototype._updateData = function(findId, findVal, data) {
    for(var i in this.data) {
        if(this.data[i][findId] == findVal) {
            this.data[i] = data;
        }
    }
	return this;
}
toeListTable.prototype.deleteRow = function(findId, findVal) {
    var result = false;
    var tempThis = this;
    jQuery(this.table).find('tr').each(function(){
        if(jQuery(this).find('.'+ findId).html() == findVal) {
            tempThis.removeRow(this);
            result = true;
            return;
        }
    });
    return result;
}
toeListTable.prototype.removeRow = function(row) {
    jQuery(row).hide(TOE_DATA.animationSpeed, function(){
        jQuery(this).remove();
    });
	return this;
}
toeListTable.prototype.clearTable = function() {
    jQuery(this.table).find('tr').not('.sdHeader').not('.toeRowExample').remove();
	return this;
}
toeListTable.prototype.resetTable = function() {
    jQuery(this.table).html(this.startHtml);
	return this;
}
toeListTable.prototype.getData = function(findId, findVal) {
    for(var i in this.data) {
        if(this.data[i][findId] == findVal)
            return this.data[i];
    }
    return false;
}
toeListTable.prototype._drawRow = function(row, id, data) {
    var element = jQuery(row).find('.'+ id);
    var htmlTo = jQuery(element).attr('htmlTo');
    if(this.converters[id]) {
        data = this.converters[id](data, element);
    }
    if(typeof(htmlTo) == 'string') {
        jQuery(element).attr(htmlTo, data);
    } else {
        jQuery(element).html(data);
    }
	return this;
}
toeListTable.prototype._convertActive = function(data, element) {
    data = parseInt(data);
    if(data) {
        jQuery(element).removeClass('toeOptDisabled');
        jQuery(element).addClass('toeOptActive');
    } else {
        jQuery(element).removeClass('toeOptActive');
        jQuery(element).addClass('toeOptDisabled');    
    }
    return '';
}
toeListTable.prototype.setData = function(data) {
    this.data = data;
	return this;
}
function toeUserFieldsListTabe(table, data) {
    var attrs = new Array();
    attrs['table']= table;
    attrs['data']= data;
    toeUserFieldsListTabe.superclass.constructor.apply(this, attrs);
    this.converters['system'] = this._convertSystem;
    this.init(table, data);
}
extend(toeUserFieldsListTabe, toeListTable);
toeUserFieldsListTabe.prototype._convertSystem = function(data, element) {
    data = parseInt(data);
    if(data) 
        data = '';
    else
        data = '<a href="" class="toe_opt_remove_userfield" onclick="removeUserfield(this); return false;"><img src="'+ TOE_DATA.close+ '" /></a>'
    return data;
}
/**
 * bool params.doNotInit - if true - init() method will be ignored after object creation
 * htmlObject params.linkToElement - if is NOT undefined - than states will be linked to this country element
 **/
function toeStatesSelect(id, params) {
   this.id = id;
   this.selected = '';
   this.countryObj = null;
   if(typeof(params) == 'undefined') {
       params = {};
       jQuery.extend(params, {doNotInit: false, linkToElement: false});
   }
   if(!params.doNotInit)
        this.init(params.linkToElement);
}
toeStatesSelect.prototype.init = function(linkToElement) {
    var id = this.id;
    this.selected = jQuery('#'+ id+ '_select').val();
    if(this.selected)
        jQuery('#'+ id+ '_text').val('');
    var tempObj = this;
    jQuery(function() {
        var currentPos = jQuery('#'+ id+ '_text').offset();
        var distance = 999999999;
        var countryObj = null;
        if(typeof(linkToElement) == 'undefined' || !linkToElement) {
			// select[type=country] is for normal usage, when country is selectbox
			// input[type=hidden][useFor=country] is for usage when use_only_one_country option is enabled
            jQuery('select[type=country], input[type=hidden][useFor=country]').each(function(){
                var countryPos = jQuery(this).offset();
                var newDistance = Math.sqrt(Math.pow((countryPos.left - currentPos.left), 2) + Math.pow((countryPos.top - currentPos.top), 2));
                if(newDistance < distance) {
                    distance = newDistance;
                    countryObj = this;
                }
            });
        } else {
            countryObj = linkToElement;
        }
        if(countryObj) {
			tempObj.countryObj = countryObj;
            jQuery(countryObj).change(function(){
                tempObj.countryChange(jQuery(this).val());
            });
			jQuery(countryObj).keyup(function(){
                tempObj.countryChange(jQuery(this).val());
            });
            tempObj.countryChange(jQuery(countryObj).val());
        } else 
            tempObj.countryChange(0);
    });
}
toeStatesSelect.prototype.countryChange = function(countryId) {
    var states = new Array();
    var searchInArray = (typeof(countryId) == 'object' || typeof(countryId) == 'Array');
    for(var i in toeStates) {
        if(searchInArray) {
            if(jQuery.inArray(toeStates[i].country_id, countryId) != -1)
                states.push(toeStates[i]);
        } else if(toeStates[i].country_id == countryId) {
            states.push(toeStates[i]);
        }
    }
    if(states.length == 0) {
        jQuery('#'+ this.id+ '_text').show();
        jQuery('#'+ this.id+ '_text').removeAttr('disabled');
        jQuery('#'+ this.id+ '_select').hide();
        jQuery('#'+ this.id+ '_select').attr('disabled', 'true');
    } else {
        jQuery('#'+ this.id+ '_select option').not('[value=0]').remove();
        for(i = 0; i < states.length; i++) {
            if(this.selected && ((searchInArray && jQuery.inArray(states[i].id, this.selected) != -1) || (!searchInArray && this.selected == states[i].id)))
                jQuery('#'+ this.id+ '_select').append('<option value="'+ states[i].id+ '" selected>'+ states[i].name+ '</option>');
            else
                jQuery('#'+ this.id+ '_select').append('<option value="'+ states[i].id+ '">'+ states[i].name+ '</option>');
        }
        
        jQuery('#'+ this.id+ '_text').hide();
        jQuery('#'+ this.id+ '_text').attr('disabled', 'true');
        jQuery('#'+ this.id+ '_select').removeAttr('disabled');
        jQuery('#'+ this.id+ '_select').show();
    }
}

var toePostbox = jQuery.extend(true, {}, postboxes);
toePostbox.closeClickBinded = false;
var toePostboxes = new Array();

toePostbox.classContainer = '.meta-box-sortables';
toePostbox.init = function(page, args) {
    var isMobile = jQuery(document.body).hasClass('mobile');
    var self = this;
    jQuery.extend( this, args || {} );
    jQuery('#wpbody-content').css('overflow','hidden');
    jQuery(this.classContainer).sortable({
        placeholder: 'sortable-placeholder',
        connectWith: this.classContainer,
        items: '.postbox',
        handle: '.hndle',
        cursor: 'move',
        delay: ( isMobile ? 200 : 0 ),
        distance: 2,
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        stop: function(e,ui) {
            if ( jQuery(this).find('#dashboard_browser_nag').is(':visible') && 'dashboard_browser_nag' != this.firstChild.id ) {
                jQuery(this).sortable('cancel');
                return;
            }
            self.save_order(page);
        },
        receive: function(e,ui) {
            if ( 'dashboard_browser_nag' == ui.item[0].id )
                jQuery(ui.sender).sortable('cancel');

            self._mark_area();
        }
    });

    if ( isMobile ) {
        jQuery(document.body).bind('orientationchange.postboxes', function(){ postboxes._pb_change(); });
        this._pb_change();
    }

    this._mark_area();
}
toePostbox.save_order = function(page) {
    var postVars, page_columns = jQuery('.columns-prefs input:checked').val() || 0;

    postVars = {
        action: 'meta-box-order',
        _ajax_nonce: jQuery('#meta-box-order-nonce').val(),
        page_columns: page_columns,
        page: page
    }
    jQuery(this.classContainer).each( function() {
        postVars["order[" + this.id.split('-')[0] + "]"] = jQuery(this).sortable( 'toArray' ).join(',');
    } );
    jQuery.post( ajaxurl, postVars );
}
toePostbox._mark_area = function() {
    var visible = jQuery('div.postbox:visible').length, side = jQuery('#post-body #side-sortables');

    jQuery('#dashboard-widgets '+ this.classContainer+ ':visible').each(function(n, el){
        var t = jQuery(this);

        if ( visible == 1 || t.children('.postbox:visible').length )
            t.removeClass('empty-container');
        else
            t.addClass('empty-container');
    });

    if ( side.length ) {
        if ( side.children('.postbox:visible').length )
            side.removeClass('empty-container');
        else if ( jQuery('#postbox-container-1').css('width') == '280px' )
            side.addClass('empty-container');
    }
}
toePostbox.add_postbox_toggles = function(page, args) {
    this.init(page, args);
	/* It should be already initialized in base class
    if(!toePostbox.eventsBinded) {      //Let's avoid more than one time binding
        jQuery('.postbox h3, .postbox .handlediv').bind('click.postboxes', function() {
            var p = jQuery(this).parent('.postbox'), id = p.attr('id');

            if ( 'dashboard_browser_nag' == id )
                return;

            p.toggleClass('closed');
            postboxes.save_state(page);

            if ( id ) {
                if ( !p.hasClass('closed') && jQuery.isFunction(postboxes.pbshow) )
                    postboxes.pbshow(id);
                else if ( p.hasClass('closed') && jQuery.isFunction(postboxes.pbhide) )
                    postboxes.pbhide(id);
            }
        });

        jQuery('.postbox h3 a').click( function(e) {
            e.stopPropagation();
        });

        jQuery('.postbox a.dismiss').bind('click.postboxes', function(e) {
            var hide_id = jQuery(this).parents('.postbox').attr('id') + '-hide';
            jQuery( '#' + hide_id ).prop('checked', false).triggerHandler('click');
            return false;
        });

        jQuery('.hide-postbox-tog').bind('click.postboxes', function() {
            var box = jQuery(this).val();

            if ( jQuery(this).prop('checked') ) {
                jQuery('#' + box).show();
                if ( jQuery.isFunction( postboxes.pbshow ) )
                    postboxes.pbshow( box );
            } else {
                jQuery('#' + box).hide();
                if ( jQuery.isFunction( postboxes.pbhide ) )
                    postboxes.pbhide( box );
            }
            postboxes.save_state(page);
            postboxes._mark_area();
        });

        jQuery('.columns-prefs input[type="radio"]').bind('click.postboxes', function(){
            var n = parseInt(jQuery(this).val(), 10), pb = postboxes;

            if ( n ) {
                pb._pb_edit(n);
                pb.save_order(page);
            }
        });
        toePostbox.eventsBinded = true;
    }*/
}
jQuery.fn.toeRebuildSelect = function(data, useIdAsValue, val) {
    if(jQuery(this).tagName() == 'SELECT' && typeof(data) == 'object') {
        if(jQuery(data).size() > 0) {
            if(typeof(val) == 'undefined')
                val = false;
            if(jQuery(this).children('option').length) {
                jQuery(this).children('option').remove();
            }
            if(typeof(useIdAsValue) == 'undefined')
                useIdAsValue = false;
            var selected = '';
            for(var id in data) {
                selected = '';
                if(val && ((useIdAsValue && id == val) || (data[id] == val)))
                    selected = 'selected';
                jQuery(this).append('<option value="'+ (useIdAsValue ? id : data[id])+ '" '+ selected+ '>'+ data[id]+ '</option>');
            }
        }
    }
}
/**
 * @see html::slider();
 **/
function toeSliderMove(event, ui) {
    var id = jQuery(event.target).attr('id');
    jQuery('#toeSliderDisplay_'+ id).html( ui.value );
    jQuery('#toeSliderInput_'+ id).val( ui.value );
}
/**
 * We will not use just jQUery.inArray because it is work incorrect for objects
 * @return mixed - key that was found element or -1 if not
 */
function toeInArray(needle, haystack) {
    if(typeof(haystack) == 'object') {
        for(var k in haystack) {
            if(haystack[ k ] == needle)
                return k;
        }
    } else if(typeof(haystack) == 'array') {
        return jQuery.inArray(needle, haystack);
    }
    return -1;
}
/**
 * Calculate sort order in postbox elements
 */
function toeDetectSortOrder(postboxClass, writeTo) {
	var sortOrder = new Array();
       
	jQuery('.'+ postboxClass).each( function() {
		sortOrder.push(this.id.split('-')[1]);
	} );
	if(writeTo) {	// Write result to input element in form
		if(typeof(writeTo) == 'string')
			writeTo = jQuery('#writeTo');
		jQuery(writeTo).val(sortOrder.join(','));
	}
	return sortOrder;
}
jQuery.fn.setReadonly = function() {
	jQuery(this).addClass('toeReadonly').attr('readonly', 'readonly');
}
jQuery.fn.unsetReadonly = function() {
	jQuery(this).removeClass('toeReadonly').removeAttr('readonly', 'readonly');
}
function toeOption(key) {
	if(TOE_DATA.options && TOE_DATA.options[ key ] && TOE_DATA.options[ key ].value)
		return TOE_DATA.options[ key ].value;
	return false;
}
function toeLang(key) {
	if(TOE_DATA.siteLang && TOE_DATA.siteLang[key])
		return TOE_DATA.siteLang[key];
	return key;
}
jQuery.fn.getClassId = function(pref, test) {
	var classId = jQuery(this).attr('class');
	classId = classId.substr( strpos(classId, pref+ '_') );
	if(strpos(classId, ' '))
		classId = classId.substr( 0, strpos(classId, ' ') );
	classId = classId.split('_');
	classId = classId[1];
	return classId;
}
function toeTextIncDec(textFieldId, inc) {
	var value = parseInt(jQuery('#'+ textFieldId).val());
	if(isNaN(value))
		value = 0;
	if(!(inc < 0 && value < 1)) {
		value += inc;
	}
	jQuery('#'+ textFieldId).val(value);
}

/**
 * Out all arguments into console
 * @params any data in any number - all will be printed to console using console.log()
 */
function toeVarDump() {
	if(typeof(console) != 'undefined') {
		if(arguments.length) {
			for(var i = 0; i < arguments.length; i++) {
				console.log( arguments[i] );
			}
		}
	}
}

/**
 * Make first letter of string in upper case
 * @param str string - string to convert
 * @return string converted string - first letter in upper case
 */
function toeStrFirstUp(str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}

function toeHtmlSlider(id, params) {
	params = jQuery.extend({left: 80}, params);
	this.id = id;
	this.left = params.left;
	//this.locked = false;
	this.opened = false;
	this.animationInProcess = false;
	this.init();
}
toeHtmlSlider.prototype.init = function() {
	var self = this;
	var opener = jQuery('#'+ this.id+ '_opener');
	var openerWidth = parseInt(jQuery(opener).width()) + 2 * parseInt(jQuery(opener).css('border-left-width')) + 0 /*for other borders*/;
	/*jQuery(opener).click(function(e){
		if(!self.animationInProcess) {
			if(self.opened) {
				self.opened = false;
				self.close();
			} else {
				self.opened = true;
				self.open();
			}
		}
	});*/
	jQuery(opener).mouseover(function(){
		if(!self.opened && !self.animationInProcess) {
			self.opened = true;
			self.open();
		}
	});
	jQuery('#'+ this.id).mouseout(function(e){
		if(self.opened 
			&& !self.locked 
			&& this != e.relatedTarget 
			&& !self.animationInProcess
			&& !jQuery(e.relatedTarget).parents('#'+ this.id).size() 
			&& !jQuery(e.relatedTarget).hasClass('toeSlidePanelOpener')
		) {
			var rightBorder = jQuery(this).offset().left + jQuery(this).width();
			if(e.clientX < rightBorder) {
				self.opened = false;
				self.close();
			}
		}
	});
	jQuery('#'+ this.id).find('.toeSlidePanelLock')
		.addClass('toeSlidePanelUnlocked')
		.html(toeLang('Lock'))
		.click(function(){
			self.locked = !self.locked;
			if(self.locked)
				jQuery(this)
					.removeClass('toeSlidePanelUnlocked')
					.addClass('toeSlidePanelLocked')
					.html(toeLang('Unlock'));
			else
				jQuery(this)
					.removeClass('toeSlidePanelLocked')
					.addClass('toeSlidePanelUnlocked')
					.html(toeLang('Lock'));
	});
}
toeHtmlSlider.prototype.open = function() {
	var self = this;
	this.animationInProcess = true;
	jQuery('#'+ this.id).stop();
	jQuery('#'+ this.id).find('.toeSlidePanelOpener').stop();
	jQuery('#'+ this.id).animate({left: this.left+ '%'}, TOE_DATA.animationSpeed, function(){
		self.animationInProcess = false;
	});
	jQuery('#'+ this.id).find('.toeSlidePanelOpener').animate({right: (100 - this.left)+ '%'}, TOE_DATA.animationSpeed);
}
toeHtmlSlider.prototype.close = function() {
	var self = this;
	this.animationInProcess = true;
	jQuery('#'+ this.id).stop();
	jQuery('#'+ this.id).find('.toeSlidePanelOpener').stop();
	jQuery('#'+ this.id).animate({left: '100%'}, TOE_DATA.animationSpeed);
	jQuery('#'+ this.id).find('.toeSlidePanelOpener').animate({right: '0'}, TOE_DATA.animationSpeed, function(){
		self.animationInProcess = false;
	});
}
function parseStr (str, array) {
  // http://kevin.vanzonneveld.net
  // +   original by: Cagri Ekin
  // +   improved by: Michael White (http://getsprink.com)
  // +    tweaked by: Jack
  // +   bugfixed by: Onno Marsman
  // +   reimplemented by: stag019
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: stag019
  // +   input by: Dreamer
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
  // +   input by: Zaide (http://zaidesthings.com/)
  // +   input by: David Pesta (http://davidpesta.com/)
  // +   input by: jeicquest
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: When no argument is specified, will put variables in global scope.
  // %        note 1: When a particular argument has been passed, and the returned value is different parse_str of PHP. For example, a=b=c&d====c
  // *     example 1: var arr = {};
  // *     example 1: parse_str('first=foo&second=bar', arr);
  // *     results 1: arr == { first: 'foo', second: 'bar' }
  // *     example 2: var arr = {};
  // *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
  // *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
  // *     example 3: var abc = {3:'a'};
  // *     example 3: parse_str('abc[a][b]["c"]=def&abc[q]=t+5');
  // *     results 3: JSON.stringify(abc) === '{"3":"a","a":{"b":{"c":"def"}},"q":"t 5"}';
	var strArr = String(str).replace(/^&/, '').replace(/&$/, '').split('&'),
	sal = strArr.length,
	i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
	postLeftBracketPos, keys, keysLen,
	fixStr = function (str) {
		return decodeURIComponent(str.replace(/\+/g, '%20'));
	};
	// Comented by Alexey Bolotov
	/*
	if (!array) {
	array = this.window;
	}*/
	if (!array) {
		array = {};
	}

	for (i = 0; i < sal; i++) {
		tmp = strArr[i].split('=');
		key = fixStr(tmp[0]);
		value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

		while (key.charAt(0) === ' ') {
			key = key.slice(1);
		}
		if (key.indexOf('\x00') > -1) {
			key = key.slice(0, key.indexOf('\x00'));
		}
		if (key && key.charAt(0) !== '[') {
			keys = [];
			postLeftBracketPos = 0;
			for (j = 0; j < key.length; j++) {
				if (key.charAt(j) === '[' && !postLeftBracketPos) {
					postLeftBracketPos = j + 1;
				} else if (key.charAt(j) === ']') {
					if (postLeftBracketPos) {
						if (!keys.length) {
							keys.push(key.slice(0, postLeftBracketPos - 1));
						}
						keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
						postLeftBracketPos = 0;
						if (key.charAt(j + 1) !== '[') {
							break;
						}
					}
				}
			}
			if (!keys.length) {
				keys = [key];
			}
			for (j = 0; j < keys[0].length; j++) {
				chr = keys[0].charAt(j);
				if (chr === ' ' || chr === '.' || chr === '[') {
					keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
				}
				if (chr === '[') {
					break;
				}
			}

			obj = array;
			for (j = 0, keysLen = keys.length; j < keysLen; j++) {
				key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
				lastIter = j !== keys.length - 1;
				lastObj = obj;
				if ((key !== '' && key !== ' ') || j === 0) {
					if (obj[key] === undef) {
						obj[key] = {};
					}
					obj = obj[key];
				} else { // To insert new dimension
					ct = -1;
					for (p in obj) {
						if (obj.hasOwnProperty(p)) {
							if (+p > ct && p.match(/^\d+$/g)) {
								ct = +p;
							}
						}
					}
					key = ct + 1;
				}
			}
			lastObj[key] = value;
		}
	}
	return array;
}

function toePages(key) {
	if(typeof(TOE_PAGES) != 'undefined' && TOE_PAGES[key])
		return TOE_PAGES[key];
	return false;;
}
function toeGetCountry(id, key) {
	if(TOE_DATA && TOE_DATA.countries) {
		for(var i in TOE_DATA.countries) {
			if(TOE_DATA.countries[i].id == id) {
				if(key)
					return TOE_DATA.countries[i][ key ];
				else
					return TOE_DATA.countries[i];
			}
		}
	}
	return null;
}

/**
 * Retrive from GET params query
 */
function getSearchParam(key) {
	this.pars, this.parsed;
	if(!this.parsed) {
		var tmp = window.location.search.substr(1).split('&');
		var tmp1 = new Array();
		this.pars = new Array();
		for (var i in tmp) {
			tmp1 = tmp[i].split('=');
			this.pars[tmp1[0]] = unescape(tmp1[1]);
		}
		this.parsed = true;
	}
	if(typeof(key) !== 'undefined') {
		if(this.pars && this.pars[key])
			return this.pars[key];
		else
			return false;
	} else
		return this.pars;
}

/**
 * Retrive from GET params string all after # sign, like #key1_val1|key2_val2
 */
function getHash(key) {
	this.pars, this.parsed;
	if(!this.parsed) {
		var tmp = window.location.hash.substr(1).split('|');
		var tmp1 = new Array();
		this.pars = new Array();
		for(var i = 0; i < tmp.length; i++) {
			if(tmp[i]) {
				tmp1 = tmp[i].split('_');
				this.pars[tmp1[0]] = unescape(tmp1[1]);
			}
		}
		this.parsed = true;
	}
	if(typeof(key) !== 'undefined') {
		if(this.pars && this.pars[key])
			return this.pars[key];
		else
			return false;
	} else
		return this.pars;
}
