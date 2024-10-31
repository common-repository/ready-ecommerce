<?php
class currencywidgetView extends view {
    public function display($instance) {
        $all = frame::_()->getModule('currency')->getModel()->get();
        $default = frame::_()->getModule('currency')->getDefault();
        $this->assign('all', $all);
        $this->assign('default', $default);
        $this->assign('title', $instance['title']);
        $this->assign('redirect', add_query_arg('', false));
        parent::display('frontend');
    }
    public function displayForm($data, $widget) {
        $this->displayWidgetForm($data, $widget);
    }
}
?>