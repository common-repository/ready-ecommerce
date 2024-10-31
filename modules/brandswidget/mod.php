<?php

/**
 * Class for brandswidget module
 * Module represents the widget of brandswidget
 * Return a list of product brands
 */

class brandswidget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeProductBrandsWidget');
    }
}
/**
 * Featured Products Widget Class
 */
class toeProductBrandsWidget extends WP_Widget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeProductBrandsWidget', 
            'description' => lang::_('Displays list of product brands')
        );
        $control_ops = array(
            'id_base' => 'toeProductBrandsWidget'
        );
	parent::__construct( 'toeProductBrandsWidget', lang::_('Ready! Product brands'), $widgetOps );
    }
    public function widget($args, $instance) {
        frame::_()->getModule('brandswidget')->getView()->display($instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('brandswidget')->getView()->displayForm($instance, $this);
    }
}
?>