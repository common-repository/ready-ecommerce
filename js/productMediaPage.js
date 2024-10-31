jQuery(document).ready(function(){
    jQuery("#gallery-form, #image-form, #library-form").ajaxForm({
		beforeSubmit: function(){
			toeVarDump('lalala');
		},
        success: function(){
			var win = window.dialogArguments || opener || parent || top;
			updateProductMedia(win);
	   }
    });
});

function updateProductMedia(win) {
   var id = jQuery("#post_id").val();
   var data = 'reqType=ajax&page=products&action=updateProductMedia&id='+ id; 
   jQuery.post(ajaxurl, data,function(res){
       if (res.html != '') {
           jQuery(win.parent.document).find("#toeProductMedia .inside").html(res.html);
           /*jQuery("#toeProductMedia .inside").html(res);*/
           win.tb_remove();
       }
   }, 'json') ;
}


