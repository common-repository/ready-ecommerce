<?php
class currency extends module {
    protected $_default = array();
    protected $_currencies = array();
    protected $_selectedId = array();
	private $_defaultDetected = false;
    /*public function init() {
        parent::init();
        $this->_default = $this->getDefault();
        $selectedId = $this->getSelectedId();
        if(empty($this->_default)) {
            $all = $this->getModel()->get();
            $default = array();
            foreach($all as $c) {
                if(!empty($selectedId) && $selectedId == $c['id']) {
                    $this->_default = $c;
                    break;
                }
                if((bool)$c['use_as_default'] && empty($selectedId)) {
                    $this->_default = $c;
                    break;
                }
            }
            if(empty($this->_default)) {    //Set any currency as default - as default have not been defined
                $this->_default = $all[0];
            }
            $this->_default['value'] = (double)$this->_default['value'];
            $this->_default['decimal_places'] = (int)$this->_default['decimal_places'];
            $this->setDefault($this->_default);
        }
    }*/
	private function _detectDefault() {
		if(!$this->_defaultDetected) {
			//$this->_default = $this->getDefault();
			$selectedId = $this->getSelectedId();
			//if(empty($this->_default)) {
				$all = $this->getModel()->get();
				foreach($all as $c) {
					if(!empty($selectedId) && $selectedId == $c['id']) {
						$this->_default = $c;
						break;
					}
					if((bool)$c['use_as_default'] && empty($selectedId)) {
						$this->_default = $c;
						break;
					}
				}
				if(empty($this->_default)) {    //Set any currency as default - as default have not been defined
					$this->_default = $all[0];
				}
				$this->_default['value'] = (double)$this->_default['value'];
				$this->_default['decimal_places'] = (int)$this->_default['decimal_places'];
				$this->setDefault($this->_default);
			//}
			$this->_defaultDetected = true;
		}
	}
    public function setDefault($currency) {
        $this->_default = $currency;
    }
    public function getDefault($key = '') {
		if(!$this->_defaultDetected)
			$this->_detectDefault();
        $default = $this->_default;
        if(empty($key))
            return $default;
        else 
            return $default[$key];
    }
    public function setSelectedId($cid) {
        req::setVar('currencyId', $cid, 'session');
    }
    public function getSelectedId() {
        return req::getVar('currencyId', 'session', 0);
    }
	/**
	 * Format price according to current currency or to $currency parameter
	 * @param string/numeric $price - number value to display
	 * @param array $currency - if spesified - will convert price according to this currency, if empty - will use default store currency
	 * @return string - Formated price
	 */
    public function display($price, $currency = array()) {
		$this->_detectDefault();
        $currency = $this->_inCurrency($currency);
        $priceFloat = $this->calculate($price, $currency);
        $price = number_format($priceFloat, $currency['decimal_places'], $currency['symbol_point'], $currency['symbol_thousand']);
		return dispatcher::applyFilters('currencyDisplay', $currency['symbol_left']. $price. $currency['symbol_right']);
    }
    public function calculate($price, $currency = array()) {
		$this->_detectDefault();
        $currency = $this->_inCurrency($currency);
        $price = (double) $price;
        $price = $price * $currency['value'];
        $price = round($price, $currency['decimal_places']);
        return $price;
    }
    protected function _inCurrency($currency) {
		$this->_detectDefault();
        if(empty($currency))
            $currency = $this->_default;
        elseif(is_string($currency)) {
            if(isset($this->_currencies[$currency]))
                $currency = $this->_currencies[$currency];
            else {
                $code = $currency;
                $currency = $this->getController()->getModel()->get(array('code' => $currency));
                $currency = $this->_currencies[$code] = $currency[0];
            }
        }
        if(empty($currency)) 
            return false;
        return $currency;
    }
    public function getDefaultCode() {
		$this->_detectDefault();
        $def = $this->_default;
        if(!$def)
            $def = $this->getDefault();
        return $def['code'];
    }
    /**
     * Method calculates total price for one product
     * @param float $price price of product
     * @param int $qty quantity of product
     * @param int $pid product's ID, for future use
     * @return float total price of product
     */
    public function calculateTotal($price, $qty, $pid = 0, $params = array('options' => array())) {
		$this->_detectDefault();
        $total = 0;
        if(is_numeric($price) && is_numeric($qty) && is_numeric($pid)) {
            $cart = frame::_()->getModule('user')->getModel('cart')->get();
            $prodInCart = frame::_()->getModule('user')->getModel('cart')->getProduct($pid, $params['options']);
            if(!empty($prodInCart) && !empty($prodInCart['gifts'])) {
                $qty -= count($prodInCart['gifts']);
            }
            $price = (float)$price;
            $qty = (float)$qty;
            if(!empty($params['options'])) {
                $addForOptions = 0;
				if(frame::_()->getModule('options')->get('use_new_products_fields')) {
					foreach($params['options'] as $o) {
						$selectedValues = array();
						if(is_array($o['selected'])) 
							$selectedValues = $o['selected'];
						 else 
							$selectedValues = array($o['selected']);
						if(isset($o['options']) && !empty($o['options'])) {
							foreach($o['options'] as $opt) {
								if($opt['price'] && in_array($opt['id'], $selectedValues)) {
									$optPrice = (double) $opt['price'];
									if($opt['absolute'])
										$addForOptions += $optPrice;   //just add to total price
									else
										$addForOptions += $optPrice * $price / 100;   //% of total price
								}
							}
						}
					}
				} else {
					foreach($params['options'] as $o) {
						$selectedValues = array();
						if(is_array($o['selected'])) 
							$selectedValues = $o['selected'];
						 else 
							$selectedValues = array($o['selected']);
						foreach($selectedValues as $selected) {
							if(!empty($o['opt_values'][ $selected ]['price'])) {
								if($o['opt_values'][ $selected ]['price_absolute'])
									$addForOptions += (float) $o['opt_values'][ $selected ]['price'];   //just add to total price
								else
									$addForOptions += (float) $o['opt_values'][ $selected ]['price'] * $price / 100;   //% of total price
							}
						}
					}
				}
                $price += $addForOptions;
            }
            $total = $price * $qty;
        }
		$total = frame::_()->getModule('products')->getPrice($pid, $total);
        return $total;
    }
    /**
     * Dispalay total price for product
     */
    public function displayTotal($price, $qty, $pid = 0, $params = array()) {
        return $this->display( $this->calculateTotal($price, $qty, $pid, $params) );
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
		$this->_detectDefault();
        $tabs = array();
        $tab = new tab(lang::_('Currencies'), $this->getCode());
        $tab->setView('currencyTab');
        $tabs[] = $tab;
        return $tabs;
    }
}
