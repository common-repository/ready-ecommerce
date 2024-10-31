<?php
class cartView extends view {
    private $_cartEmpty = false;
    public function getCartContent($canEdit = true) {
        $cart = frame::_()->getModule('user')->getModel('cart')->get();
        $tpl = 'shoppingCart';
        if(empty($cart)) {
            $this->_cartEmpty = true;
            $tpl = 'shoppingCartEmpty';
        } else {
            $this->_cartEmpty = false;
            /*qty check*/
            $q = 'post_id IN (';
            foreach($cart as $inCartId => $p) {
                $q .= $p['pid']. ', ';
            }
            $q = substr($q, 0, -2);
            $q .= ')';
            $data = frame::_()->getTable('products')->get('post_id, quantity', $q);
            $checkoutLink = $this->getCheckoutLink();
            $totalBox = frame::_()->getModule('checkout')->getView()->getTotal();
            $this->assign('cart', $cart);
            $this->assign('canEdit', $canEdit);
            $this->assign('checkoutLink', $checkoutLink);
            $this->assign('totalBox', $totalBox);
            //This is product columns, @see cartModel::_columns
            $this->assign('columns', frame::_()->getModule('user')->getModel('cart')->getColumns());
        }
        return parent::getContent($tpl);
    }
    public function getCheckoutLink() {
        return parent::getContent('checkoutLink');
    }
    public function displayCart() {
        $checkoutNavigation = $this->_cartEmpty ? '' : frame::_()->getModule('checkout')->getView()->getNavigation();
        return dispatcher::applyFilters('shoppingCartContent', 
				$checkoutNavigation. 
                $this->getCartContent());
    }
    /**
     * Get html that will show after product was added to cart
     */
    public function afterProductAddMsg() {
        return parent::getContent('afterProductAddMsg');
    }
}
?>
