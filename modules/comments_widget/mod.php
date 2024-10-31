<?php
/**
 * Class for comments_widget module
 * Module represents the widget of comments
 */

class comments_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeCommentsWidget');
    }
}
/**
 * Recent Comments Widget Class
 */
class toeCommentsWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeCommentsWidget', 
            'description' => lang::_('Displays Products Comments block')
        );
        $control_ops = array(
            'id_base' => 'toeCommentsWidget'
        );
	parent::__construct( 'toeCommentsWidget', lang::_('Ready! Products Comments'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('comments_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('comments_widget')->getView()->displayForm($instance, $this);
    }
}