<?php
/**
 * Class for related_widget module
 * Module represents the widget of related products
 */

class related_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
		
        add_action('add_meta_boxes', array($this, 'addProductRelated'));
		
        add_action('admin_print_scripts-post.php', array($this,'loadRelatedProductPageScripts'));
        add_action('admin_print_scripts-post-new.php', array($this,'loadRelatedProductPageScripts'));
        add_action('wp_ajax_related_delete',array($this->getController(),'relatedDelete'));
        add_action('wp_ajax_related_add',array($this->getController(),'relatedAdd'));
    }
    public function registerWidget() {
        return register_widget('toeRelatedWidget');
    }
	
    /**
     * Adding product download files
     */
    public function addProductRelated() {
        add_meta_box('toeRelatedProducts', lang::_('Related Products'), array($this, 'addRelatedProducts'), S_PRODUCT, 'side');
    }
    /**
     * Rendering the product downloads block
     * 
     * @global object $post 
     */
    public function addRelatedProducts() {
        $this->getView('relatedProduct')->display('downloads');
    }
	
	
	
    public function addRelated($id,$rel_to) {
		if(!is_numeric($id))
			return false;
			
		$t_relwidget = frame::_()->getTable('related_widget');
		$data = array();
		$data['rel_from'] = $id;
		$data['rel_to'] = $rel_to;
		$result = $t_relwidget->store($data);
		return $result;
    }
	
    public function removeRelated($id) {
		if(!is_numeric($id))
			return false;
			
		$t_relwidget = frame::_()->getTable('related_widget');
		$result = $t_relwidget->delete(" id = ".$id);
		return $result;
    }
	
	
    public function getRelatedProducts($id) {
		global $wpdb;
		if(!is_numeric($id))
			return false;
		$t_relwidget = frame::_()->getTable('related_widget');
		$join = ' JOIN '. $wpdb->posts. ' ON '.$t_relwidget->alias().'.rel_to = '. $wpdb->posts. '.ID ' ;
		$t_relwidget->arbitraryJoin($join);
		$where = $t_relwidget->alias().".rel_from = ".$id;
		$result = $t_relwidget->get("*",$where,'','all','yho');
		return $result;
    }
    public function getNotRelatedProducts($id) {
		global $wpdb;
		//SELECT * FROM `wp_toe_products` as A JOIN wp_posts as B ON A.post_id = B.ID WHERE B.`ID` NOT IN (SELECT rel_to FROM wp_toe_bwidget WHERE rel_from = 180);
       // $id = $post->ID;
		if(!is_numeric($id))
			return false;
			
		$t_products = frame::_()->getTable('products');
		
		$join = ' JOIN '. $wpdb->posts. ' ON '.$t_products->alias().'.post_id = '. $wpdb->posts. '.ID ' ;
		$t_products->arbitraryJoin($join);
		
		$where = $t_products->alias().".post_id != ".$id." AND ".$t_products->alias().".`post_id` NOT IN (SELECT rel_to FROM ".frame::_()->getTable('related_widget')->getTable()." WHERE rel_from = ".$id.")";
		
		$result = $t_products->get("*", $where ,'','all','yjklo');
		
		return $result;
    }
	
    /**
     * Load Product page scripts
     * @global type $typenow 
     */
    function loadRelatedProductPageScripts(){
        if (S_PRODUCT == get_post_type()){
            wp_enqueue_script('jquery-form');
            frame::_()->addScript('productRelatedJs', S_JS_PATH.'productRelated.js');
        }
    }
}
/**
 * Related Products Widget Class
 */
class toeRelatedWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeRelatedWidget', 
            'description' => lang::_('Displays Related Products block')
        );
        $control_ops = array(
            'id_base' => 'toeRelatedWidget'
        );
	parent::__construct( 'toeRelatedWidget', lang::_('Ready! Related Widget'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('related_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('related_widget')->getView()->displayForm($instance, $this);
    }
}
?>