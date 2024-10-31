<?php
class toectwitter_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeTwitterWidget');
    }
}
/**
 * Slider Widget Class
 */
class toeTwitterWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeTwitterWidget', 
            'description' => lang::_('Displays Last Tweets')
        );
        $control_ops = array(
            'id_base' => 'toeTwitterWidget'
        );
	parent::__construct( 'toeTwitterWidget', lang::_('Ready! Twitter'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('twitter_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('twitter_widget')->getView()->displayForm($instance, $this);
    }
}