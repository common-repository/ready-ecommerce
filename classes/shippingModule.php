<?php
abstract class shippingModule extends module {
    protected $_rate = 0;
    protected $_freeShipping = false;
    protected $_allowShipping = true;
    /**
     * Enable cache results based on order data, useful for shipping modules, that spend a lot of time to calc rate (as USPS for example)
     */
    protected $_enableCache = false;
    /**
     * Here will be stored results, key is md5(serialize($orderData)) and value is rate for this order data
     */
    protected $_cacheResults = array();
    /**
     * User data, entered from checkout
     */
    protected $_userData = array();
    /**
	 * Additional shipping options, that need to be entered by users
	 */
    protected $_additionalOptions = array();
    
    public function getRate($d = array()) {
        $this->_userData = $d;
        if(!$this->_tryCache()) {
            $this->_beforeRateCalc($d);
            if(!$this->_freeShipping && $this->_allowShipping) {
                $this->_calcRate();
                $this->_afterRateCalc();
            } elseif($this->_freeShipping) {
                $this->setRate(0);  //So shipping is free)
            }
            $this->_setCache();
        }
        return $this->_rate;
    }
    public function setRate($rate) {
        $this->_rate = $rate;
    }
    public function init() {
        parent::init();
        $this->_prepareParams();
    }
    protected function _prepareParams() {
        if(is_array($this->_params) && isset($this->_params[0]))
            $this->_params = $this->_params[0];
    }
    protected function _setCache() {
        if($this->_enableCache) {
            if(!empty($this->_userData)) {
                $orderKey = md5(utils::serialize($this->_userData));
                $this->_cacheResults[ $orderKey ] = $this->_rate;
            }
        }
    }
    protected function _tryCache() {
        if($this->_enableCache) {
            if(!empty($this->_userData)) {
                $orderKey = md5(utils::serialize($this->_userData));
                if(isset($this->_cacheResults[ $orderKey ])) {
                    $this->_rate = $this->_cacheResults[ $orderKey ];
                    return true;
                }
            }
        }
        return false;
    }
    protected function _afterRateCalc() {
        if(!empty($this->_rate)) {
            $this->_params->order_price_boundary = (float)$this->_params->order_price_boundary;
            $this->_params->order_price_decrease = (float)$this->_params->order_price_decrease;
            if(!empty($this->_params->order_price_boundary) && !empty($this->_params->order_price_decrease)) {
                $orderData = frame::_()->getModule('checkout')->calculate(array('subtotal'));
                if((float)$orderData['subTotal'] >= (float)$this->_params->order_price_boundary) {
                    if(($this->_rate - $this->_params->order_price_decrease) < 0) {
                        $this->_rate = 0;
                    } else {
                        $this->_rate -= $this->_params->order_price_decrease;
                    }
                }
            }
        }
    }
    protected function _beforeRateCalc($d = array()) {
		$errorKey = isset($d['shippingSameAsBilling']) && (bool) $d['shippingSameAsBilling'] ? 'billing_' : 'shipping_';
        $this->_params->order_price_free_shipping = (float)$this->_params->order_price_free_shipping;
        if(!empty($this->_params->country_to_ship) && is_array($this->_params->country_to_ship) && $this->_params->country_to_ship != array(0) /*For Not Selected value*/) {
            if(empty($d['shipping_country'])) {
                $order = frame::_()->getModule('order')->getCurrent();
                $shippingCountry = (int)$order['shipping_address']['country'];
            } else {
                $shippingCountry = (int)$d['shipping_country'];
            }
            if(!in_array($shippingCountry, $this->_params->country_to_ship)) {
                $this->pushError(lang::_('Your shipping country is unavailable for selected shipping method'), $errorKey. 'country');
                //$this->_errors['shipping_country'] = lang::_('Your shipping country is unavailable for selected shipping method');
                $this->_allowShipping = false;
            }
        }
        if(!empty($this->_params->state_to_ship) && $this->_params->state_to_ship != array(0) /*For Not Selected value*/) {
            if(empty($d['shipping_state'])) {
                $order = frame::_()->getModule('order')->getCurrent();
                $shippingState = $order['shipping_address']['state'];
            } else {
                $shippingState = $d['shipping_state'];
            }
            if(is_array($this->_params->state_to_ship)) {
                if(!in_array((int)$shippingState, $this->_params->state_to_ship)) 
                    $this->_allowShipping = false;
            } else {
                if(strtolower($shippingState) != strtolower($this->_params->state_to_ship)) 
                    $this->_allowShipping = false;
            }
            if(!$this->_allowShipping) {
                $this->pushError(lang::_('Your shipping state is unavailable for selected shipping method'), $errorKey. 'state');
                //$this->_errors['shipping_state'] = lang::_('Your shipping state is unavailable for selected shipping method');
            }
        }
        if(!$this->_allowShipping) return;
        if(!empty($this->_params->order_price_free_shipping)) {
            $orderData = frame::_()->getModule('checkout')->calculate(array('subtotal'));
            if((float)$orderData['subTotal'] >= (float)$this->_params->order_price_free_shipping) {
                $this->_freeShipping = true;
            }
        }
    }
	/**
	 * This will run validation for availability of shipping method by provided user address
	 */
	public function validateShippingForAddress($d = array()) {
		$this->_beforeRateCalc($d);
		return $this->allowShipping();
	}
	/**
	 * This is method that should be redeclared in child module class
	 */
    protected function _calcRate() {
        
    }
    public function isFree() {
        return $this->_freeShipping;
    }
    public function allowShipping() {
        return $this->_allowShipping;
    }
    public function pushError($error, $key = '') {
        parent::pushError($error, $key);
        $this->_allowShipping = false;
    }
	public function haveAdditionalOptions() {
		return !empty($this->_additionalOptions);
	}
	public function setAdditionalOptions($options) {
		$this->_additionalOptions = $options;
	}
	public function getAdditionalOptions() {
		return $this->_additionalOptions;
	}
	public function getAdditionalOptionsHtml() {
		$res = '';
		if($this->haveAdditionalOptions()) {
			$fieldsData = array();
			foreach($this->_additionalOptions as $opt) {
				$htmltype = isset($opt['htmltype']) ? $opt['htmltype'] : 'radiobutton';
				$field = toeCreateObj('field', array('shipping_module_options', $htmltype));
				$field->setValue($opt['code']);
				$optString = '<label>'. $field->drawHtml(). '&nbsp;'. $opt['label'];
				if(isset($opt['price'])) {
					$optString .= ' ['. frame::_()->getModule('currency')->display($opt['price']). ']';
				}
				$optString .= '</label>';
				$fieldsData[] = $optString;
			}
			$res = implode('<br />', $fieldsData);
			$res .= html::hidden('shipping_module_options_for_key', array('value' => $this->getOptionsKey()));
		}
		return $res;
	}
	public function getOptionsKey() {
		$key = '';
		$keysCollect = array('city', 'state', 'country', 'zip');
		if(!empty($this->_userData) && is_array($this->_userData)) {
			foreach($keysCollect as $k) {
				if(isset($this->_userData['billing']) && is_array($this->_userData['billing']) && isset($this->_userData['billing'][$k]))
					$key .= $this->_userData['billing'][$k];
				if(isset($this->_userData['shipping']) && is_array($this->_userData['shipping']) && isset($this->_userData['shipping'][$k]))
					$key .= $this->_userData['shipping'][$k];
			}
			if(isset($this->_userData['shipping_module']))
				$key .= $this->_userData['shipping_module'];
			$key = md5($key);
		}
		return $key;
	}
}
