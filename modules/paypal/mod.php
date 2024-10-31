<?php
class toecPaypal extends paymentModule {
    public function __construct($d, $params = array()) {
        parent::__construct($d, $params);
        $this->_haveProcessHtml = true;
    }
    public function getProcessHtml() {
        if($this->_params->testMode)
            $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; //Development
        else
            $url = 'https://www.paypal.com/cgi-bin/webscr'; //Live
        $order = frame::_()->getModule('order')->getCurrent();
		$gifts = frame::_()->getModule('checkout')->getGifts();
		$discountAmountCart = !empty($gifts) && is_array($gifts) && isset($gifts['total']) && !empty($gifts['total'])
			? $gifts['total']
			: 0;
        $parameters = array(
            'cmd' => '_cart',
			'upload' => '1', 
            'item_name' => frame::_()->getModule('options')->get('store_name'),
            'handling_cart' => is_array($order['shipping_module']) ? round(frame::_()->getModule('currency')->calculate($order['shipping_module']['cost'], $order['currency']), 2) : 0,
            'tax_cart' => round(frame::_()->getModule('currency')->calculate($order['tax_rate'], $order['currency']), 2),
            'business' => $this->_params->email,
            'amount' => round(frame::_()->getModule('currency')->calculate($order['total'], $order['currency']), 2),
            'currency_code' => $order['currency']['code'],
            'custom' => $order['user_id'],
            'no_note' => '1',
            'return' => uri::mod('order', '', 'addSuccess', array(
                        'from' => 'payments',
                        'redirect' => frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml', 'data' => array('step' => 3))))),
            'cancel_return' => frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml', 'data' => array('step' => 2))),
			'paymentaction' => (isset($this->_params->paymentaction) && !empty($this->_params->paymentaction)) ? $this->_params->paymentaction : 'authorization',
        );
		if($discountAmountCart)
			$parameters['discount_amount_cart'] = round(frame::_()->getModule('currency')->calculate($discountAmountCart, $order['currency']), 2);
        
        //$parameters['address_override'] = '1';
        $parameters['first_name'] = $order['billing_address']['first_name'];
        $parameters['last_name'] = $order['billing_address']['last_name'];
        $parameters['address1'] = $order['billing_address']['street_address'];
        $parameters['city'] = $order['billing_address']['city'];
        $parameters['state'] =  fieldAdapter::displayState($order['billing_address']['state'], 'code');
        $parameters['zip'] = $order['billing_address']['postcode'];
        $parameters['country'] = fieldAdapter::displayCountry($order['billing_address']['country'], 'iso_code_2');
		$parameters['charset'] = 'utf-8';
		if(!empty($order['products']) && is_array($order['products'])) {
			$i = 1;
			foreach($order['products'] as $p) {
				$parameters['amount_'. $i] = round(frame::_()->getModule('currency')->calculate(frame::_()->getModule('currency')->calculateTotal($p['product_price'], 1, $p['product_id'], $p['product_params']), $order['currency']), 2);
				$parameters['item_name_'. $i] = htmlspecialchars($p['product_name']);
				$parameters['quantity_'. $i] = $p['product_qty'];
				if(isset($p['product_params']) 
					&& !empty($p['product_params']) 
					&& isset($p['product_params']['options']) 
					&& !empty($p['product_params']['options']) 
					&& is_array($p['product_params']['options'])
				) {
					$j = 0;
					foreach($p['product_params']['options'] as $opt) {
						$parameters['on'. $j. '_'. $i] = $opt['label'];
						$parameters['os'. $j. '_'. $i] = is_array($opt['displayValue']) ? implode(', ', $opt['displayValue']) : $opt['displayValue'];
						$j++;
					}
				}
				$i++;
			}
		}
        $out = html::formStart('paypal', array('method' => 'POST', 'action' => $url));
        foreach($parameters as $k => $v) {
            $out .= html::hidden($k, array('value' => $v));
        }
        $out .= html::submit('pay', array('value' => lang::_('Pay By PayPal')));
        $out .= html::formEnd();
        return $out;
    }
}
?>
