<?php

/**
 * Class for single_product module
 * Module represents the widget of sinlge product
 */

class single_product extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeSPWidget');
    }
}
/**
 * Featured Products Widget Class
 */
class toeSPWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeSPWidget', 
            'description' => lang::_('Displays Single Product anywhere on your site')
        );
        $control_ops = array(
            'id_base' => 'toeSPWidget'
        );
	parent::__construct( 'toeSPWidget', lang::_('Ready! Single Product'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('single_product')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('single_product')->getView()->displayForm($instance, $this);
    }
}
?>