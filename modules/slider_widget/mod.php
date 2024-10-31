<?php
/**
 * Class for slider_widget module
 * Module represents the slider widget
 */
class toecslider_widget extends module {
    protected $_slidesDir = '';
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeSliderWidget');
    }
    public function getSlidesDir() {
        return $this->_slidesDir;
    }
    public function getFullImgPath($name) {
        $uploadsInfo = wp_upload_dir();
        return $uploadsInfo['baseurl']. '/'. $name;
    }
    public function getFullImgDir($name) {
        $uploadsInfo = wp_upload_dir();
        return $uploadsInfo['basedir']. DS. $name;
    }
    public function imagesDataToArr($data) {
        $images = array();
        foreach($data as $k => $val) {
            if(strpos($k, 'images__') === 0) {  //Al this variables names starst from images___
                $keys = explode('__', $k);
                $images[ $keys[1] ][ $keys[2] ] = $val;
            }
        }
        return $images;
    }
}
/**
 * Slider Widget Class
 */
class toeSliderWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeSliderWidget', 
            'description' => lang::_('Displays Slider')
        );
        $control_ops = array(
            'id_base' => 'toeSliderWidget'
        );
	parent::__construct( 'toeSliderWidget', lang::_('Ready! Slider'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('slider_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('slider_widget')->getView()->displayForm($instance, $this);
    }
}
?>
