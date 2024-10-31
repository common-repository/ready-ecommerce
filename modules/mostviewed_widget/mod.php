<?php
/**
 * Class for mostviewed_widget module
 * Module represents the widget of bestsellers
 */

class mostviewed_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeMostViewedWidget');
    }
}
/**
 * Bestsellers Products Widget Class
 */
class toeMostViewedWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeMostViewedWidget', 
            'description' => lang::_('Displays Most Viewed Products')
        );
        $control_ops = array(
            'id_base' => 'toeMostViewedWidget'
        );
	parent::__construct( 'toeMostViewedWidget', lang::_('Ready! Most Viewed Products'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('mostviewed_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('mostviewed_widget')->getView()->displayForm($instance, $this);
    }
}
?>