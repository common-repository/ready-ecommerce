<?php
class productDownloadsView extends view {
    public function display($tpl = '') {
        global $post;
        $downloads = frame::_()->getModule('digital_product')->getModel('downloads')->getProductDownloads($post);
        $params = frame::_()->getModule('digital_product')->getParamsObject();
        $this->assign('downloads', $downloads);
        $this->assign('params', $params);
        parent::display('downloads');
    }
	public function displayVar($tpl = '',$id) {
		$post = get_post($id); 
        $downloads = frame::_()->getModule('digital_product')->getModel('downloads')->getProductDownloads($post);
        $params = frame::_()->getModule('digital_product')->getParamsObject();
        $this->assign('downloads', $downloads);
        $this->assign('params', $params);
        return parent::getContent('downloads');
    }
}
?>
