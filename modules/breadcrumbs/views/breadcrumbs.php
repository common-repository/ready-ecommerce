<?php
class breadcrumbsView extends view {
    public function display($instance) {
        if($instance['home_title'] != '') { 
			$homeTitle = $instance['home_title'];
        } else {
			$homeTitle = lang::_('Home Page');
		}
        $breadcrumbs = frame::_()->getModule('breadcrumbs')->toe_breadcrumbs($homeTitle);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('params', $instance);
        parent::display('list');
    }
    public function displayForm($data, $widget) {
        $this->displayWidgetForm($data, $widget);
    }
}
?>