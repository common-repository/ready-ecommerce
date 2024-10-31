<?php
abstract class toeWordpressWidget extends WP_Widget {
	public function preWidget($args, $instance) {
		if(frame::_()->isTplEditor())
			echo $args['before_widget'];
	}
	public function postWidget($args, $instance) {
		if(frame::_()->isTplEditor())
			echo $args['after_widget'];
	}
}
?>