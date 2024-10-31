<?php
class currencywidget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeCurrencyWidget');
    }
}
class toeCurrencyWidget extends toeWordpressWidget {
    public function __construct() {
		$widgetOps = array( 
            'classname' => 'toeCurrencyWidget', 
            'description' => lang::_('Displays Currency block')
        );
		$control_ops = array(
            'id_base' => 'toeCurrencyWidget'
        );
		parent::__construct( 'toeCurrencyWidget', lang::_('Ready! Currency'), $widgetOps );
	}
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('currencywidget')->getView()->display($instance);
		$this->postWidget($args, $instance);
	}
    public function update($new_instance, $old_instance) {
		return $new_instance;
	}
    public function form($instance) {
        frame::_()->getModule('currencywidget')->getView()->displayForm($instance, $this);
	}
}
?>
