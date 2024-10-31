<?php
class shippingController extends controller {
    public function storeModule() {
        $res = new response();
        if(($id = $this->getModel()->store(req::get('post'))) !== false) {
            $newModule = $this->getModel()->get(array('id' => $id));
            $tmpField = toeCreateObj('field', array( $newModule[0]['code'] ));
            $tmpField->setValue( $newModule[0]['params'] );
            $defaultShippingOptions = frame::_()->getModule('shipping')->getDefaultOptions();
            $newModule[0]['params'] = $tmpField->drawConfig( $newModule[0]['code'], $defaultShippingOptions );
            $res->addData( $newModule );
            $res->addMessage(lang::_('Done'));
        } else {
            $res->addError( $this->getModel()->getErrors() );
        }
        $res->ajaxExec();
    }
    public function deleteModule() {
        $res = new response();
        if($this->getModel()->delete(req::get('post'))) {
            $res->addMessage(lang::_('Deleted'));
            $res->addData('id', req::getVar('id'));
        } else
            $res->addError( $this->getModel()->getErrors() );
        return $res->ajaxExec();
    }
}
?>
