<?php
class cartwidgetView extends view {
    public function display() {
        $this->assign('contentHtml', $this->getContentHtml());
        /**
         * use cartWidgetShell tpl to envelop cart widget to it's unique content - 
         * will use it in ajax cart widgget update
         */
        parent::display('cartWidgetShell');
    }
    public function getContentHtml() {
        $cart = frame::_()->getModule('user')->getModel('cart')->get();
        if(empty($cart)) {
            $tpl = 'emptyCart';
        } else {
            $count_items = count($cart);
            $this->assign('total', frame::_()->getModule('checkout')->getSubTotal());
            $this->assign('cart', $cart);
            $this->assign('count_items', $count_items);
            $this->assign('checkoutLink', frame::_()->getModule('user')->getView('cart')->getCheckoutLink());
            $tpl = 'cart';
        }
        return parent::getContent($tpl);
    }
}
?>