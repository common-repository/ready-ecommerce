jQuery(document).ready(function(){
    jQuery("#post-body-content #titlediv").after('<div id="product_changes"></div>');
    jQuery("#slugdiv").appendTo('#product_changes');
    jQuery("#postexcerpt").appendTo('#product_changes');
    jQuery("#postdivrich").prepend('<h3 class="hndle"><span>Full Description</span></h3>');
    jQuery("#slugdiv").show();
    jQuery("#postexcerpt").show();
    jQuery("#postdivrich").show();
    jQuery("#edit-slug-box").prepend('<span><strong>ID: </strong>'+jQuery("#post_ID").val()+'</span>');
   // Adding mceEditor to excerpt field
    jQuery("#excerpt").addClass("mceEditor");
    jQuery("#products_categories-adder").prepend('<h4><a href="'+TOE_LINKS.manage_categories+'" target="_blank">'+TOE_PRODUCT_LANG.manage_categories+'</a></h4>');
    jQuery("#products_brands-adder").prepend('<h4><a href="'+TOE_LINKS.manage_brands+'" target="_blank">'+TOE_PRODUCT_LANG.manage_brands+'</a></h4>');
    var status = jQuery("#comment_status");
    var label = status.parent();
    label.html('');
    label.append(status).append(' '+TOE_PRODUCT_LANG.allow_reviews);
    tinyMCE.execCommand("mceAddControl", false, "excerpt");
    jQuery("#taxonomy-products_categories input[type='checkbox']").click(function(){
        var id = jQuery(this).val();
        var data = '';
        var query_string = ''; 
        jQuery("#taxonomy-products_categories input[type='checkbox']").each(function() { 
                if (this.checked) { 
                    query_string += "&cats[]=" + this.value; 
                } 
            }); 
        if (jQuery(this).attr('checked') == "checked") {
            data = 'reqType=ajax&page=products&action=addCategoryFields&id='+ id + query_string;
            jQuery.post(ajaxurl, data, function(res){
                if (res.html != '') jQuery("#product_extras").append(res.html);
            }, 'json');
        } else {
            data = 'reqType=ajax&page=products&action=deleteCategoryFields&id='+ id;
            jQuery.post(ajaxurl, data, function(res){
                jQuery.each(res.data, function(index, value){
                    selector = '[name="'+value+'"]';
                    parent = jQuery(selector).parent().parent();
                    if (parent.hasClass('product_extra')) {
                        parent.remove();
                    } else {
                        jQuery(selector).remove();
                    }
                });
            }, 'json');
        }
    });
     jQuery('.product_media .delete_media').on('click',function(e){
         e.preventDefault();
         var tempDeLink = this;
         if (confirm('Are you sure you want to delete this media')) {
             jQuery.post(jQuery(this).attr('href'), function(){
                 jQuery(tempDeLink).parents('.product_media:first').remove();
             });
         }
     }); 
});