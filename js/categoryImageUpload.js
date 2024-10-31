//var currentCategoryId = 0;  //For current category or brand
//var currentTaxonomy = '';   //To determine - is this products category or brand
jQuery(document).ready(function() {
    var formfield;
    jQuery('#btn-upload').click(function() {
        jQuery('html').addClass('image');
        formfield = jQuery('#product_category_thumb_url').attr('name');
        tb_show('', 'media-upload.php?type=image&width=640&height=480&tag_id='+ TOE_DATA.tag_ID+ '&taxonomy='+ TOE_DATA.taxonomy+ '&TB_iframe=1');
        return false;
    });
    jQuery('#btn-delete').click(function() {
        jQuery("#image").attr('src','');
        jQuery('#product_category_thumb_url').val('');
    });
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html) {
        if (formfield) {
            url = jQuery('img', html).attr('src');
            jQuery('#product_category_thumb_url').val(url);
            thumb = jQuery('#image');
            if (url != '') {            
                thumb.show();
                thumb.attr('src', url);
            } else {
                thumb.hide();
            }
            jQuery('html').removeClass('image');
        } else {
            window.original_send_to_editor(html);
        }
        tb_remove();
    }
});
