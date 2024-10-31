<?php
class bcwidget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeBCWidget');
    }
}
class toeBCWidget extends toeWordpressWidget {
    public function __construct() {
		$widgetOps = array( 
            'classname' => 'toeBCWidget', 
            'description' => lang::_('Displays Categories block')
        );
		$control_ops = array(
            'id_base' => 'toeBCWidget'
        );
		parent::__construct( 'toeBCWidget', lang::_('Ready! Brands / Categories'), $widgetOps );
	}
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('bcwidget')->getView()->display($instance);
		$this->postWidget($args, $instance);
	}
    public function update($new_instance, $old_instance) {
		return $new_instance;
	}
    public function form($instance) {
        frame::_()->getModule('bcwidget')->getView()->displayForm($instance, $this);
	}
}
?>