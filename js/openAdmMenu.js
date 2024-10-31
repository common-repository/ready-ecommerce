jQuery(document).ready(function(){
    /**
     * Need more work around this function
     */
    jQuery('#adminmenu a').each(function(){
        if(jQuery(this).html() == 'The One Ecommerce') {
            if(jQuery(this).hasClass('wp-has-submenu')) {
                jQuery(this).addClass('wp-has-current-submenu');
                jQuery(this).parent().addClass('wp-menu-open');
                jQuery(this).parent().addClass('wp-has-current-submenu');
            }
        }
    });
});