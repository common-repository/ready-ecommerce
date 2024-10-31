jQuery(document).ready(function(){
    jQuery("#post").attr('enctype','multipart/form-data');
    jQuery(".product_downloads_wrapper #add_product_download").click(function(){
        var count = jQuery(".uploader").length;
        var name = 'product_files_'+count;
        jQuery("#first_raw").clone().appendTo("#upload_file").removeAttr("id").find(".uploader").attr('name',name);
    });
    jQuery(".remove_product_file").on("click", function(){
       jQuery(this).parent().remove(); 
    });
    jQuery(".delete_product_file").click(function(){
       var link = jQuery(this); 
       if (confirm('Are you sure you want to delete this file?')) {
           var id = link.attr('rel');
           var data = {
               id : id,
               action: 'digital_delete'
           };
           jQuery.post(ajaxurl, data, function(){
               link.parent().remove();
           });
       } 
    });
	
	
	jQuery('.toeEditProductFile').click(function(){
		selectedId =this.id; 
		jQuery('.toeProductFileOptions').each(function(){
			if(jQuery(this).hasClass('file_item_'+selectedId)){
				jQuery(this).toggle();
			}else{
				jQuery(this).hide();
			}
		})
	})
	
	
	
	
	
	
	
	
});


