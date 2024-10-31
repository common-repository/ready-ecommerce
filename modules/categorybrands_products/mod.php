<?php

/**
 * Class for category_products module
 * Module represents the widget of category/brands products
 */

class categorybrands_products extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeCategoryBrandsProductsWidget');
    }
}
/**
 * Featured Products Widget Class
 */
class toeCategoryBrandsProductsWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeCategoryBrandsProductsWidget', 
            'description' => lang::_('Displays Category/Brands Products block')
        );
        $control_ops = array(
            'id_base' => 'toeCategoryBrandsProductsWidget'
        );
	parent::__construct( 'toeCategoryBrandsProductsWidget', lang::_('Ready! Category/Brands Products'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('categorybrands_products')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('categorybrands_products')->getView()->displayForm($instance, $this);
    }
}
?>