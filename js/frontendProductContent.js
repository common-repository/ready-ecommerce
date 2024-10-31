jQuery(document).ready(function(){
	var variationId = getHash('toeVariation');
	if(variationId && jQuery('.toeVariationsSelectList').size() == 1) {
		jQuery('.toeVariationsSelectList')
				.find('option')
				.removeAttr('selected');
		jQuery('.toeVariationsSelectList')
				.find('option[id='+ variationId+ ']')
				.attr('selected', 'selected');
		jQuery('.toeVariationsSelectList')
				.trigger('change');
	}
});
