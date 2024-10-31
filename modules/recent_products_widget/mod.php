<?php
/**
 * Class for recent_products_widget module
 * Module represents the widget of recent product
 */
class recent_products_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeRecentProductsWidget');
    }
}
/**
 * Recent Products Widget Class
 */
class toeRecentProductsWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeRecentProductsWidget', 
            'description' => lang::_('Displays Recent Products block')
        );
        $control_ops = array(
            'id_base' => 'toeRecentProductsWidget'
        );
	parent::__construct( 'toeRecentProductsWidget', lang::_('Ready! Recent Products'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('recent_products_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('recent_products_widget')->getView()->displayForm($instance, $this);
    }
}
?>
