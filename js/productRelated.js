jQuery(document).ready(function(){
    jQuery(".add_rel").click(function(){
       var link = jQuery(this); 
           var id = link.attr('rel');
           var data = {
               id : jQuery("#post_ID").val(),
			   rel_to : jQuery("#rel_to").val(),
               action: 'related_add'
           };
           jQuery.post(ajaxurl, data, function(res){
               //link.parent().remove();
			   jQuery(".add_rel").off('click');
			   jQuery(".product_related_wrapper").html(res);
           });
    });
    jQuery(".del_rel").click(function(){
       var link = jQuery(this); 
           var id = link.attr('rel');
           var data = {
               id : jQuery("#post_ID").val(),
			   rel_from : jQuery("#rel_from").val(),
               action: 'related_delete'
           };
           jQuery.post(ajaxurl, data, function(res){
               //link.parent().remove();
			   jQuery(".del_rel").off('click');
			   jQuery(".product_related_wrapper").html(res);
           });
    });
});


