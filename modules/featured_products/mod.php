<?php

/**
 * Class for featured_products module
 * Module represents the widget of featured products
 */

class featured_products extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeFPWidget');
    }
}
/**
 * Featured Products Widget Class
 */
class toeFPWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeFPWidget', 
            'description' => lang::_('Displays Featured Products block')
        );
        $control_ops = array(
            'id_base' => 'toeFPWidget'
        );
	parent::__construct( 'toeFPWidget', lang::_('Ready! Featured Products'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('featured_products')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('featured_products')->getView()->displayForm($instance, $this);
    }
}
?>