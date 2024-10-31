<?php
class checkoutController extends controller {
    /**
     * Current step of checkout
     */
    protected $_step = 0;
    public function getAllHtml() {
        $beforeCheckoutErrors = array('errors' => array(), 'redirectData' => array('mod' => '', 'action' => ''));
        if(!frame::_()->getModule('options')->get('allow_guest_checkout')) {    //if guests is not allowed to made checkout - check is user registered or not
			$checkoutSteps = frame::_()->getModule('checkout')->getSteps();
            if(!frame::_()->getModule('user')->isCustomer() && $checkoutSteps['registrationForm']->disable) {
                $beforeCheckoutErrors['errors'][] = lang::_('Please login before checkout');
                $beforeCheckoutErrors['redirectData']['mod'] = 'user';
                $beforeCheckoutErrors['redirectData']['action'] = 'getLoginForm';
            }
        }
        $beforeCheckoutErrors = dispatcher::applyFilters('beforeCheckoutErrors', $beforeCheckoutErrors);
        if(!empty($beforeCheckoutErrors['errors']) && !empty($beforeCheckoutErrors['redirectData'])) {
            redirect(frame::_()->getModule('pages')->getLink(array('mod' => $beforeCheckoutErrors['redirectData']['mod'], 'action' => $beforeCheckoutErrors['redirectData']['action'], 
                'data' => array(
                    'toeErrors' => $beforeCheckoutErrors['errors'],
                    'toeReturn' => frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml')),
                ))));
        }
        $step = req::getVar('step', 'all', 1);
        $step = intval($step);
        $this->_step = $step;
        switch($step) {
            case 4:         //Cancelled orders
                add_filter('the_content', array($this->getView('checkout'), 'getCancelledPage'));
                break;
            case 3:
                add_filter('the_content', array($this->getView('checkout'), 'getSuccessPage'));
                break;
            case 2:
                add_filter('the_content', array($this->getView('checkout'), 'getConfirnationPage'));
                break;
            case 1:
            default:
                add_filter('the_content', array($this->getView('checkout'), 'getAll'));
                break;
        }
    }
    public function getStep() {
        return $this->_step;
    }
    /**
     * Save checkout steps - blocks positions and it's setup - from admin area
     */
    public function saveStepsOption() {
        $res = new response();
        //At first we will update checkout boxes data
        $putOptData = array(
            'sortOrder' => explode(',', req::getVar('checkoutSortOrder')), 
            'displayName' => explode(',', addslashes(req::getVar('checkoutTittles'))), 
            'disable' => req::getVar('checkoutStepsDisable')
        );
        $putOptData = utils::jsonEncode($putOptData);
        $optModelRes = frame::_()->getModule('options')->getModel()->put(array('code' => 'checkout_steps', 'value' => $putOptData));
        if($optModelRes->error)
            $res->pushError($optModelRes->errors);
        else {
            //Adn now let's update terms and conditions
            $putOptData = array(
                'termsURL' => req::getVar('termsURL'), 
                'termsText' => req::getVar('termsText')
            );
            $putOptData = utils::jsonEncode($putOptData);
            $optModelRes = frame::_()->getModule('options')->getModel()->put(array('code' => 'terms', 'value' => $putOptData));
            if($optModelRes->error)
                $res->pushError($optModelRes->errors);
            else {
				//Adn now let's update cart data
				$putOptData = array(
					'sortOrder' => explode(',', req::getVar('cartSortOrder')), 
					'disable' => req::getVar('cartPartsDisable')
				);
				$putOptData = utils::jsonEncode($putOptData);
				$optModelRes = frame::_()->getModule('options')->getModel()->put(array('code' => 'cart_columns', 'value' => $putOptData));
				if($optModelRes->error)
					$res->pushError($optModelRes->errors);
				else {
					$optModelRes = frame::_()->getModule('options')->getModel()->put(array('code' => 'checkout_success_text', 'value' => req::getVar('checkout_success_text')));
					if($optModelRes->error)
						$res->pushError($optModelRes->errors);
					else
						$res->addMessage(lang::_('Checkout Updated'));
				}
            }
        }
        $res->ajaxExec();
    }
	public function getTotalHtml() {
		$res = new response();
		$reqData = req::get('post');
		$shippingModule = req::getVar('shipping_module');
		$address = frame::_()->getModule('order')->getAddresses($reqData);
		/*if($reqData['shippingSameAsBilling'])
			$address['shipping'] = $address['billing'];*/
		$tempOrderData = array(
			'shipping_address' => $address['shipping'],
			'billing_address' => $address['billing'],
			'shipping_module' => $shippingModule,
		);
		frame::_()->getModule('order')->setTempOrder($tempOrderData);
		if(!empty($shippingModule) 
			&& !frame::_()->getModule('order')->getModel()->validateShippingModule($reqData, $address)
		) {
			$res->pushError(frame::_()->getModule('order')->getModel()->getErrors());
			$res->addData('shipping_module_options', frame::_()->getModule('shipping')->getAdditionalOptionsHtml( $shippingModule ));
			$tempOrderData['shipping_module'] = 0;
			frame::_()->getModule('order')->setTempOrder($tempOrderData);
		}
		// Check all available shipping methods for current address configuration
		$res->addData('unavailable_shipping_method_ids', $this->getModule()->getInactiveShippingMethodIds(array_merge($reqData, $tempOrderData)));
		$res->setHtml(frame::_()->getModule('checkout')->getView()->getTotal());
		$res->ajaxExec();
	}
}
?>
