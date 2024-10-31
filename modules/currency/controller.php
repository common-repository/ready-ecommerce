<?php
class currencyController extends controller {
    public function getAddCurrency() {
        $res = new response();
        $res->html = $this->getView()->addCurrency();
        return $res->ajaxExec();
    }
    public function getEditCurrency() {
        $res = new response();
        $res->html = $this->getView()->editCurrency();
        return $res->ajaxExec();
    }
    public function postCurrency() {
        $res = new response();
        $res = $this->getModel()->post(req::get('post'));
        return $res->ajaxExec();
    }
    public function putCurrency() {
        $res = new response();
        $res = $this->getModel()->put(req::get('post'));
        return $res->ajaxExec();
    }
    public function delete() {
        $res = new response();
        $res = $this->getModel()->delete(req::get('post'));
        return $res->ajaxExec();
    }
    public function setCurrency() {
        $res = new response();
        $newCurrency = $this->getModel()->get(array('code' => req::getVar('code')));
        if(!empty($newCurrency[0]))
            frame::_()->getModule('currency')->setSelectedId($newCurrency[0]['id']);
        return $res->ajaxExec();
    }
    public function displayTotal() {
        $res = new response();
        $pid = (int) req::getVar('pid');
        $pData = frame::_()->getModule('products')->getController()->getModel('products')->get($pid);
		if(frame::_()->getModule('options')->get('use_new_products_fields')) {
			$options = frame::_()->getModule('products_fields')->getModel()->getProductFieldsFromRequest( req::get('post') );
		} else {
			$options = frame::_()->getModule('options')->getModel('productfields')->getFieldsDesc( req::get('post') );
		}
        $price = $pData['price']->getValue();
        $res->setHtml( dispatcher::applyFilters('productPrice', frame::_()->getModule('currency')->displayTotal($price, req::getVar('qty'), $pid, array('options' => $options))));
        return $res->ajaxExec();
    }
}
?>
