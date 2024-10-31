<?php
class productfieldsView extends view {
    public function editProductfield($someWPVar = '', $show = true) {
        return $this->productfieldsForm(array('id' => req::getVar('id')));
    }
    public function addProductfields() {
        return $this->productfieldsForm(array('pids' => req::getVar('pids')));
    }
    public function productfieldsForm($d = array()) {
        $id = (isset($d['id']) && !empty($d['id'])) ? (int) $d['id'] : 0;
        $pids = (isset($d['pids']) && !empty($d['pids'])) ? $d['pids'] : array();   //Ability to add field from products page for one product ONLY
        $method = $id ? 'put' : 'post';
        $productfields = frame::_()->getModule('options')->getModel('productfields')->get(array('id' => $id));
        
        $destination = array();
        $destination['categories'][0] = $destination['pids'][0] = lang::_('Select All');
        $allCategories = frame::_()->getModule('products')->getHelper()->getCategoriesList();
        foreach($allCategories as $cid => $label)
            $destination['categories'][$cid] = $label;
        $allProducts = frame::_()->getModule('products')->getHelper()->getProducts();
        foreach($allProducts as $prodId => $label)
            $destination['pids'][$prodId] = $label;

        if(!empty($pids)) {
            if(!is_array($pids))
                $pids = array($pids);
            $destValue = $productfields['destination']->getValue();
            if(!empty($destValue['pids'])) {
                $destValue['pids'] = array_merge($destValue['pids'], $pids);
            } else
                $destValue['pids'] = $pids;
            $productfields['destination']->setValue($destValue);
        }
        $this->assign('paramsHtml', $this->_getParamsHtml(array_merge($productfields, array('id' => $id))));
        $this->assign('productfields', $productfields);
        $this->assign('id', $id);
        $this->assign('pids', $pids);
        $this->assign('destination', $destination);
        if($method == 'post')
            $this->assign('show_field', req::getVar('show_field'));
        $this->assign('method', $method);
        $tpl = 'addEditProductfield';
        if(isset($show))
            parent::display($tpl);
        else
            return parent::getContent($tpl);
    }
    protected function _getParamsHtml($d = array()) {
        $this->assign('productfields', $d);
        return parent::getContent('editProductfieldParams');
    }
}
?>
