<?php
/**
 * Class for alsopurchased_widget module
 * Module represents the widget of Also purchased products
 */

class alsopurchased_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeAlsoPurchasedWidget');
    }
}
/**
 * Bestsellers Products Widget Class
 */
class toeAlsoPurchasedWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeAlsoPurchasedWidget', 
            'description' => lang::_('Displays Bestsellers block')
        );
        $control_ops = array(
            'id_base' => 'toeAlsoPurchasedWidget'
        );
	parent::__construct( 'toeAlsoPurchasedWidget', lang::_('Ready! Also purchased products'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('alsopurchased_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('alsopurchased_widget')->getView()->displayForm($instance, $this);
    }
}
?>