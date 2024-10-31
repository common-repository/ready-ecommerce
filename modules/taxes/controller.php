<?php
class taxesController extends controller {
    public function getAddTax() {
        $res = new response();
        $res->setHtml($this->getView()->addTax());
        return $res->ajaxExec();
    }
    public function getEditTax() {
        $res = new response();
        $res->html = $this->getView()->editTax();
        return $res->ajaxExec();
    }
    public function postTax() {
        $res = new response();
        $res = $this->getModel()->post(req::get('post'));
        return $res->ajaxExec();
    }
    public function putTax() {
        $res = new response();
        $res = $this->getModel()->put(req::get('post'));
        return $res->ajaxExec();
    }
    public function delete() {
        $res = new response();
        $res = $this->getModel()->delete(req::get('post'));
        return $res->ajaxExec();
    }
}
?>