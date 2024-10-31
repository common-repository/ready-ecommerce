jQuery(document).ready(function(){
	// Remove one of this items as it was added to left menu too 
	// from wp function add_theme_page, @see dummy_data::setup_theme_admin_menus()
	jQuery('#menu-appearance').find('a[href=toe_install_prod_dummy_data]').remove();	
	jQuery('a[href$=toe_install_prod_dummy_data]')
		.after('<div class="toeInstDummyFromTheme"></div>')
		.click(function(){
			jQuery.sendForm({
				msgElID: jQuery('.toeInstDummyFromTheme'),
				data: {mod: 'dummy_data', action: 'installProducts', reqType: 'ajax'}
			});
			return false;
		});
});