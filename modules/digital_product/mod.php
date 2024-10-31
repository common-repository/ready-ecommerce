<?php
class digital_product extends module {
    public function init() {
       
        add_action('add_meta_boxes', array($this, 'addProductDownloads'));
        add_action('admin_print_scripts-post.php', array($this,'loadProductPageScripts'));
        add_action('admin_print_scripts-post-new.php', array($this,'loadProductPageScripts'));
        add_action('admin_print_styles-post.php', array($this,'loadProductPageStyles'));
        add_action('admin_print_styles-post-new.php', array($this,'loadProductPageStyles'));
        
        add_action('wp_ajax_digital_download',array($this->getController(),'digitalDownload'));
        add_action('wp_ajax_digital_file_download',array($this->getController(),'digitalFileDownload'));
        add_action('wp_ajax_nopriv_digital_file_download',array($this->getController(),'digitalFileDownload'));
        add_action('wp_ajax_digital_delete',array($this->getController(),'digitalDelete'));
        
		if(is_admin()) {
			$upload_dir = wp_upload_dir();
			$df_upload_dir = $upload_dir['basedir'].DS.'product_downloads';
			if (!file_exists($df_upload_dir)) {
				if (!wp_mkdir_p($df_upload_dir)){
					add_action('admin_notices',array($this,'digitalProductFolderError'));       
				}
			}
			if (!is_writable($df_upload_dir)) {
				// Set correct file permissions
				$stat = @stat( $df_upload_dir );
				if($stat) {
				$perms = $stat['mode'] & 0000755;
				$change_chmod = @chmod( $df_upload_dir, $perms );
				if (!$change_chmod) {
					add_action('admin_notices',array($this,'digitalProductFolderNotWriteable'));   
				}
			}
			}
			$protectDir = $df_upload_dir.DS.'.htaccess';
			if (!file_exists($protectDir)) {
				$this->protectDirectory($protectDir);
			}
		}
        // hook into post add/edit process
        add_filter('wp_insert_post_data', array($this->getController(), 'digitalUpload'), 100, 2);
        
        dispatcher::addAction('onSuccessOrder',array($this->getController(),'createUserFile'));
        
        parent::init();
    }
    /**
     * Returns the object of module params
     * 
     * @return object params 
     */
    public function getParamsObject(){
        $params = $this->getParams();
        if (is_array($params)) {
            $digital_params = $params[0];
        } elseif (is_object($params)) {
            $digital_params = $params;
        }
		if(!is_object($digital_params))
			$digital_params = new stdClass();
        if (empty($digital_params)) {
            $digital_params->download_limit = 0;
            $digital_params->period_limit = 0;
        }
        return $digital_params;
    }
    
    /**
     * Load Product page scripts
     * @global type $typenow 
     */
    function loadProductPageScripts(){
        if (S_PRODUCT == get_post_type()){
            wp_enqueue_script('jquery-form');
            frame::_()->addScript('productDownloadJs', S_JS_PATH.'productDownload.js');
        }
    }
    
    /**
     * Load Product Page Styles
     */
    function loadProductPageStyles(){ 
        if (S_PRODUCT == get_post_type()){
            wp_enqueue_style('productDownloadCss', S_CSS_PATH.'productDownload.css');
        }
    }
    
    /**
     * Adding product download files
     */
    public function addProductDownloads() {
        add_meta_box('toeProductDownloads', lang::_('Download Options'), array($this, 'addDownloadFiles'), S_PRODUCT, 'side');
    }
    
    /**
     * Rendering the product downloads block
     * 
     * @global object $post 
     */
    public function addDownloadFiles() {
        $this->getView('productDownloads')->display('downloads');
    }
    
    public function digitalProductFolderError(){
        $upload_dir = wp_upload_dir();
        echo '<div class="error">';
        echo lang::_('Folder for digital products can not be created. Create folder: '. $upload_dir['basedir'].DS.'product_downloads'. ' manually, setup 755 permissions').
                '<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts/#folder_not_writeable" target="_blank">'.lang::_('Please check FAQ').'</a>';
        echo '</div>';
    }
    
    public function digitalProductFolderNotProtected(){
        echo '<div class="error">';
        echo lang::_('Folder for digital products is not protected.').'<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts/#digitale_products" target="_blank">'.lang::_('Please check FAQ.').'</a>';
        echo '</div>';
    }
    
    public function digitalProductFolderNotWriteable(){
        echo '<div class="error">';
        echo lang::_('Folder for digital products is not writeable.').'<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts/#not_protected" target="_blank">'.lang::_('Please check FAQ.').'</a>';
        echo '</div>';
    }
    
    public function protectDirectory($protectDir = ''){
        $content = 'DENY FROM ALL';
        if ($protectDir != '')
        if (!@file_put_contents($protectDir, $content)){
            add_action('admin_notices', array($this,'digitalProductFolderNotProtected'));
        };
    }
    
}
?>
