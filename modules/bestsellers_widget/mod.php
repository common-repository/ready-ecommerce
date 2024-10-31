<?php
/**
 * Class for bestsellers_widget module
 * Module represents the widget of bestsellers
 */

class bestsellers_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeBestSellersWidget');
    }
}
/**
 * Bestsellers Products Widget Class
 */
class toeBestSellersWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeBestSellersWidget', 
            'description' => lang::_('Displays Bestsellers block')
        );
        $control_ops = array(
            'id_base' => 'toeBestSellersWidget'
        );
	parent::__construct( 'toeBestSellersWidget', lang::_('Ready! Bestsellers'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('bestsellers_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('bestsellers_widget')->getView()->displayForm($instance, $this);
    }
}
?>