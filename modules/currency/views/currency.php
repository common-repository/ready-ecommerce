<?php
class currencyView extends view {
    public function addCurrency() {
        $this->assign('currencyFields', $this->_getFields());
        $this->assign('id', 0);
        $this->assign('method', 'post');
        return parent::getContent('addEditCurrency');
    }
    public function editCurrency() {
        $this->assign('currencyFields', $this->_getFields(req::getVar('id')));
        $this->assign('id', req::getVar('id'));
        $this->assign('method', 'put');
        return parent::getContent('addEditCurrency');
    }
    protected function _getFields($id = 0) {
        $currencyFields = frame::_()->getModule('currency')->getModel()->get(array('id' => $id));
        
        $currencyFields['symbol_left']->addHtmlParam('attr', 'style="display: none;"');
        $currencyFields['symbol_right']->addHtmlParam('style', 'display="none"');
        $currencyFields['symbol_point']->addHtmlParam('style', 'display="none"');
        $currencyFields['decimal_places']->addHtmlParam('style', 'display="none"');
        
        /*unset($currencyFields['symbol_left']);
        unset($currencyFields['symbol_right']);
        unset($currencyFields['symbol_point']);
        unset($currencyFields['symbol_thousand']);
        unset($currencyFields['decimal_places']);*/
        return $currencyFields;
    }
}
?>
