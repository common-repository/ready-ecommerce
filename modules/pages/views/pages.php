<?php
class pagesView extends view {
    public function displayDeactivatePage() {
        $this->assign('GET', req::get('get'));
        $this->assign('POST', req::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(req::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(req::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}
?>
