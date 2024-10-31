<?php
class relatedProductView extends view {
    public function display($tpl = '') {
        global $post;
		$id = $post->ID;
		$related_products = frame::_()->getModule('related_widget')->getRelatedProducts($id);
		if(empty($related_products)||is_null($related_products))
		$related_products = array();
		
		
		$not_related_products = frame::_()->getModule('related_widget')->getNotRelatedProducts($id);
		if(empty($not_related_products)||is_null($not_related_products))
		$not_related_products = array();
		
		$dbres = frame::_()->getTable('related_widget')->getById(2);
        $this->assign('related_products', $related_products);
        $this->assign('not_related_products', $not_related_products);
        parent::display('downloads');
    }
    public function updateAjax($tpl = '',$id,$msg) {
		$related_products = frame::_()->getModule('related_widget')->getRelatedProducts($id);
		if(empty($related_products)||is_null($related_products))
		$related_products = array();
		
		$not_related_products = frame::_()->getModule('related_widget')->getNotRelatedProducts($id);
		if(empty($not_related_products)||is_null($not_related_products))
		$not_related_products = array();
		
        $this->assign('msg', $msg);
        $this->assign('related_products', $related_products);
        $this->assign('not_related_products', $not_related_products);
        parent::display('updateAjax');
    }
}
?>
