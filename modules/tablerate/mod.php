<?php
class toecTablerate extends shippingModule {
    protected function _calcRate() {
        $cart = frame::_()->getModule('user')->getModel('cart')->get();
        if(!empty($cart)) {
			$baseRateOn = isset($this->_params->base_rate_on) ? $this->_params->base_rate_on : 'weight';
			$compareValue = 0;
			switch($baseRateOn) {
				case 'price':
					$compareValue = frame::_()->getModule('user')->getModel('cart')->getTotalPrice();
					break;
				case 'qty':
					$compareValue = frame::_()->getModule('user')->getModel('cart')->getTotalQty();
					break;
				case 'weight':
				default:
					foreach($cart as $pid => $p) {
						$compareValue += (int)$p['qty'] * (float)$p['weight'];
					}
					break;
			}
            if(!empty($this->_params->table_rate) && is_array($this->_params->table_rate)) {
                foreach($this->_params->table_rate as $rowRate) {
                    if($compareValue <= (float)$rowRate->weight) {
                        $this->_rate = (float)$rowRate->price;
                        break;
                    }
                }
            }
        }
    }
}