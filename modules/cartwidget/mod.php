<?php
class cartwidget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeShoppingCartWidget');
    }
}
class toeShoppingCartWidget extends toeWordpressWidget {
    public function __construct() {
		$widgetOps = array( 
            'classname' => 'toeShoppingCartWidget', 
            'description' => lang::_('Displays Shopping Cart')
        );
		$control_ops = array(
            'id_base' => 'toeShoppingCartWidget'
        );
		parent::__construct( 'toeShoppingCartWidget', lang::_('Ready! Shopping Cart'), $widgetOps );
	}
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('cartwidget')->getView()->display();
		$this->postWidget($args, $instance);
	}
    public function update($new_instance, $old_instance) {
		return $new_instance;
	}
}
?>