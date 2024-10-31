<?php
class taxesView extends view {
    public function addTax() {
        return $this->_getAddEdit(0);
    }
    public function editTax() {
        return $this->_getAddEdit(req::getVar('id'));
    }
    protected function _getAddEdit($id = 0) {
        $taxFields = frame::_()->getModule('taxes')->getModel()->get(array('id' => $id));
        $userFields = frame::_()->getModule('options')->getModel('userfields')->get();
        $this->assign('taxFields', $taxFields);
        $this->assign('userFields', $userFields);
        $this->assign('id', $id);
        $this->assign('method', ($id ? 'put' : 'post'));
        return parent::getContent('addEditTax');
    }
}
?>