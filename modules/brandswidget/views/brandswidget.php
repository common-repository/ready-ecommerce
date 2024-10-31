<?php
class brandswidgetView extends view {
    public function display($instance) {
        $this->assign('params', $instance);
        parent::display('list');
    }
    public function displayForm($data, $widget) {
        $this->assign('data', $data);
        $this->assign('widget', $widget);
        parent::display('form');
    }
}
?>