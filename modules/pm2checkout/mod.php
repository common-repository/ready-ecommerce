<?php
class pm2checkout extends paymentModule {
    public function __construct($d, $params = array()) {
        parent::__construct($d, $params);
        $this->_haveProcessHtml = true;
    }
    public function getProcessHtml() {
        $url = 'https://www.2checkout.com/2co/buyer/purchase';
        $order = frame::_()->getModule('order')->getCurrent();
        $user = frame::_()->getModule('user')->getCurrent();
        $customerId = frame::_()->getModule('user')->getCurrentID();
        if(empty($customerId))
            $customerId = '0';

        $parameters = array(
            'sid' => $this->_params->id,
            'total' => $order['total'],
            'cart_order_id' => date('YmdHis') . '-' . $customerId . '-' . frame::_()->getModule('order')->getCurrentID(),
            'fixed' => 'Y', 
            'first_name' => $order['billing_address']['first_name'],
            'last_name' => $order['billing_address']['last_name'],
            'street_address' => $order['billing_address']['address'],
            'city' => $order['billing_address']['city'],
            'state' => $order['billing_address']['state'],
            'zip' => $order['billing_address']['zip'],
            'country' => $order['billing_address']['country']['iso_code_2'],
            'email' => $user->user_email,
            'phone' => $order['billing_address']['phone'],
            'ship_name' => $order['shipping_address']['first_name']. ' '. $order['shipping_address']['last_name'],
            'ship_street_address' => $order['shipping_address']['address'],
            'ship_city' => $order['shipping_address']['city'],
            'ship_state' => $order['shipping_address']['state'],
            'ship_zip' => $order['shipping_address']['zip'],
            'ship_country' => $order['shipping_address']['country']['iso_code_2'],
        );

        $i = 1;
        foreach($order['products'] as $p) {
            $parameters['c_prod_'. $i] = (int)$p['product_id'] . ',' . (int)$p['product_qty'];
            $parameters['c_name_'. $i] = $p['product_name'];
            $parameters['c_description_'. $i] = $p['product_name'];
            $parameters['c_price_'. $i] = $p['product_price'];
            $i++;
        }

        $parameters['id_type'] = '1';
        $parameters['skip_landing'] = '1';

        if ($this->_params->testMode) {
            $parameters['demo'] = 'Y';
        }
        $parameters['x_receipt_link_url'] = uri::mod('order', '', 'addSuccess', array(
                        'from' => 'payments',
                        'redirect' => frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml', 'data' => array('step' => 3)))));

        $parameters['cart_brand_name'] = 'readyecommerce';
        $parameters['cart_version_name'] = S_VERSION;
        
        $out = html::formStart('pm2checkout', array('method' => 'POST', 'action' => $url));
        foreach($parameters as $k => $v) {
            $out .= html::hidden($k, array('value' => $v));
        }
        $out .= html::submit('pay', array('value' => lang::_('Pay By 2Checkout')));
        $out .= html::formEnd();

        return $out;
    }
}
?>
