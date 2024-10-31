<?php
class checkoutView extends view {
    public function getAll() {
        $cart = frame::_()->getModule('user')->getModel('cart')->get();
        if(empty($cart)) {
            return frame::_()->getModule('user')->getView('cart')->getCartContent();
        } else {
            frame::_()->getModule('checkout')->calculate(array('all'));
            $order = frame::_()->getModule('order')->getCurrent();
            $this->assign('order', $order);
            $this->assign('cart', $cart);
            
            $blokSteps = frame::_()->getModule('checkout')->getSteps();
            if(frame::_()->getModule('user')->getCurrentID()) {    //User is logged in - he do not need those blocks
                unset($blokSteps['loginForm']);
                unset($blokSteps['registrationForm']);
            }
            foreach($blokSteps as $sKey => $sInfo) {
                if($sInfo['disable']) continue;
                $data = '';
                switch($sKey) {
                    case 'loginForm':
                        $data = frame::_()->getModule('user')->getView('login')->getContent('', array('fieldsOnly' => true, 'hideRegistrationLink' => true, 'toeReturn' => frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'))));
                        break;
                    case 'registrationForm':
                        $data = frame::_()->getModule('user')->getView('register')->getContent('', array('fieldsOnly' => true, 'fieldsSpace' => 'registration'),'registrationFormCheckout');
                        break;
                    case 'shippingAddress':
                        $data = $this->_getAddress('shipping');
                        break;
                    case 'billingAddress':
                        $data = $this->_getAddress('billing');
                        break;
                    case 'shippingModules':
						// Add shipping box only if there are not downloadable products
						if(frame::_()->getModule('user')->getModel('cart')->haveShippingItems())
							$data = $this->_getShippingModulesList(). $this->_hideUnavailableShipping();
                        //$data = $this->_modulesList('shipping');  //Old method for shipping modules
                        break;
                    case 'paymentModules':
                        $data = $this->_modulesList('payment');
                        break;
                    case 'summaryBlock':
                        $data = $this->getSummary();
                        break;
					case 'terms':
                        $data = $this->getTerms();
                        break;
                    case 'totalBlock':
                        $data = $this->getTotal(array('subtotal', 'shipping', 'total', 'taxrate', 'gifts'), true);
                        break;
                    case 'comments':
                        $data = $this->_getComments((isset($order['comments']) ? $order['comments'] : ''));
                        break;
                }
                if(!empty($data)) {
                    $this->assign($sKey, $data);
                }
            }
			$blokSteps = dispatcher::applyFilters('blockStepsCheckoutStep1', $blokSteps);
            $this->assign('blokSteps', $blokSteps);
        }
        $this->assign('checkoutSkipConfirmStep', (int) frame::_()->getModule('options')->get('checkout_skip_confirm_step'));
        return $this->getNavigation(). 
                parent::getContent('all');
    }
    protected function _getComments($value = '') {
        $this->assign('commentsValue', $value);
        return parent::getContent('commentsBlock');
        
    }
    protected function _getAddress($type, $editable = true) {
        if(empty($this->{$type. 'UserMeta'})) {
            $nededUserMeta = array();
            $order = frame::_()->getModule('order')->getCurrent();
            if($type == 'registration') {
                $userMeta = frame::_()->getModule('user')->getModel()->getUserMeta(0, $type);
            } else
                $userMeta = frame::_()->getModule('user')->getModel()->getUserMeta(0, $type, $order);
            if(empty($userMeta)) {
                return false;
            } else {
                foreach($userMeta as $f) {
                    if(in_array($f->name, array('password', 're_password'))) continue;   //Do not show password
                    $nededUserMeta[$f->name] = clone($f);
                    $nededUserMeta[$f->name]->setName($type. '_'. $f->name);
                    $nededUserMeta[$f->name]->setErrorEl(true);
                    
                    if(!empty( $order[$type. '_address'][$f->name] )) 
                       $nededUserMeta[$f->name]->setValue( $order[$type. '_address'][$f->name] );
					if($f->name == 'country' && !frame::_()->getModule('options')->isEmpty('use_only_one_country')) {
						$nededUserMeta[$f->name]->setValue( frame::_()->getModule('options')->get('use_only_one_country') );
						$nededUserMeta[$f->name]->setHtml( 'hidden' );
						$nededUserMeta[$f->name]->addHtmlParam('attrs', 'useFor="country"');
					}
                }
            }
            $this->assign('displayMethod', $editable ? 'display' : 'showValue');     //We will use this methot to display input fields and it's values
            $this->assign ($type. 'UserMeta', $nededUserMeta);
        }
        //Let's check if address for another type is available to see should we display "Same As ..." button or not
        $showSameAsButton = false;
        $blokSteps = frame::_()->getModule('checkout')->getSteps();
        $checkOtherAddrFields = $type == 'shipping' ? 'billingAddress' : 'shippingAddress';
        if(isset($blokSteps[ $checkOtherAddrFields ]) && !$blokSteps[ $checkOtherAddrFields ]['disable'] && $editable)
            $showSameAsButton = true;
        $this->assign('showSameAsButton', $showSameAsButton);
        return parent::getContent($type. 'Address');
    }
    public function getSummary() {
        return frame::_()->getModule('user')->getView('cart')->getCartContent(false);
    }
	public function getTerms() {
		$fromDb = frame::_()->getModule('options')->get('terms');
        $fromDb = utils::jsonDecode($fromDb);
        $this->assign('termsURL', $fromDb['termsURL']);
        $this->assign('termsText', $fromDb['termsText']);
        $this->assign('mimi',"yoyo");
        return parent::getContent('terms');
    }
    public function getTotal($params = array('all', 'subtotal', 'total', 'shipping', 'taxrate', 'gifts'), $ignoreFilters = false) {
        $this->assign('costs', frame::_()->getModule('checkout')->calculate($params));
        $afterCartPrint = $ignoreFilters ? '' : dispatcher::applyFilters('afterCartPrint', '');
        $checkoutSubtotalDisplay = $ignoreFilters ? '' : dispatcher::applyFilters('checkoutSubtotalDisplay', '');
		$addTotalRows = dispatcher::applyFilters('addTotalRows', '');
		$this->assign('total', $this->costs['total']);	// Was calculated and assigned in first row of this method
        $this->assign('filterAfterCartPrint', $afterCartPrint);
        $this->assign('additionalSubtotalData', $checkoutSubtotalDisplay);
		$this->assign('addTotalRows', $addTotalRows);
        return parent::getContent('total');
    }
    protected function _getShippingModulesList() {
        $modules = frame::_()->getModule('shipping')->getModel()->get(array('active' => 1));
        $tpl = '';
        if(!empty($modules)) {
            if(!isset($this->order))
                $this->assign('order', frame::_()->getModule('order')->getCurrent());
            $this->assign('modType', 'shipping_module');
            $this->assign('modules', $modules);
            $tpl = 'shippingModulesList';
        } else
            return false;
        return parent::getContent($tpl);
    }
	/**
	 * Hide shipping methods, that are unavailable for provided user address in order
	 */
	protected function _hideUnavailableShipping() {
		$order = frame::_()->getModule('order')->getCurrent();
		if(!empty($order) && isset($order['id'])) {
			$unavailableShipingIds = $this->getModule()->getInactiveShippingMethodIds($order);
			if(!empty($unavailableShipingIds)) {
				$this->assign('unavailableShipingIds', $unavailableShipingIds);
				return parent::getContent('hideUnavailableShipping');
			}
		}
		return '';
	}
    protected function _modulesList($type) {
        $modules = frame::_()->getModules(array('type' => $type));
        $tpl = '';
        if($modules) {
            if(!isset($this->order))
                $this->assign('order', frame::_()->getModule('order')->getCurrent());
            $this->assign('modType', $type. '_module');
            $this->assign('modules', $modules);
            $tpl = 'modulesList';
        } else
            return false;
       
        return parent::getContent($tpl);
    }
    public function getConfirnationPage() {
        $order = frame::_()->getModule('order')->getCurrent();
        if($order) {
            $blokSteps = frame::_()->getModule('checkout')->getSteps();
            unset($blokSteps['loginForm']);                         //We don't need login form on confirnation page
            unset($blokSteps['summaryBlock']);
            if(frame::_()->getModule('user')->getCurrentID() && !req::getVar('loggedOnCheckoutRegister', 'session')) {    //User is logged in, and if he didn't register on prev. step - he do not need those blocks, @see orderController::addFromCheckout()
                unset($blokSteps['registrationForm']);
            }
            foreach($blokSteps as $sKey => $sInfo) {
                if($sInfo['disable']) continue;
                $data = '';
                switch($sKey) {
                    case 'registrationForm':
                        $data = $this->_getAddress('registration', false);
                        break;
                    case 'shippingAddress':
                        $data = $this->_getAddress('shipping', false);
                        break;
                    case 'billingAddress':
                        $data = $this->_getAddress('billing', false);
                        break;
                    case 'shippingModules':
                        if($order['shipping_module']) {
                            if(is_numeric($order['shipping_module']))
                                $modData = frame::_()->getModule('shipping')->getModel()->get( $order['shipping_module'] );
                            elseif(is_array($order['shipping_module']))
                                $modData = $order['shipping_module'];
                            $data = $modData['label'];
                            //$data = frame::_()->getModule( $order['shipping_module'] )->getLabel();   //Old usage - before multiple shipping modules was realized
                        }
                        break;
                    case 'paymentModules':
                        if($order['payment_module'])
                            $data = frame::_()->getModule( $order['payment_module'] )->getLabel();
                        break;
                    /*case 'summaryBlock':
                        $data = $this->getSummary();
                        break;*/
                    case 'totalBlock':
                        $data = $this->getTotal(array('all'));
                        break;
                    case 'comments':
                        if(!empty($order['comments']))
                            $data = nl2br($order['comments']);
                        break;
                }
                if(!empty($data)) {
                    $this->assign($sKey, $data);
                }
            }
            $processHtml = '';
            $paymentModule = frame::_()->getModule( $order['payment_module'] );
            if($paymentModule) {
                if($paymentModule->haveProcessHtml())
                    $processHtml = $paymentModule->getProcessHtml();
            }
            if(empty($processHtml))
                $processHtml = $this->getProcessHtml();

            $this->assign('processHtml', $processHtml);
            $this->assign('order', $order);
            $this->assign('blokSteps', $blokSteps);
        } else
            return frame::_()->getModule('order')->getView('order')->getNotFound();
            
        return parent::getContent('orderConfirm');
    }
    public function getProcessHtml() {
        return parent::getContent('processHtml');
    }
    public function getSuccessPage() {
        $order = frame::_()->getModule('order')->getCurrent();
        $order = frame::_()->getModule('order')->getModel()->get(
                frame::_()->getModule('order')->getLastID()
        );
        if(!empty($order['payment_module']) && 
                frame::_()->getModule($order['payment_module']) &&
                frame::_()->getModule($order['payment_module'])->haveSuccessHtml()) {
            
            $this->assign('paymentSuccessHtml', frame::_()->getModule($order['payment_module'])->getSuccessHtml());
        }
        $checkout_success_text = frame::_()->getModule('options')->get('checkout_success_text');
        if(!empty($checkout_success_text)) {
            $checkout_success_text = utils::makeVariablesReplacement(
                $checkout_success_text, 
                frame::_()->getModule('order')->getModel()->getSuccessVariables(array('order' => $order))
            );
        }
		$checkout_success_text = dispatcher::applyFilters('checkout_success_text', $checkout_success_text);
        $this->assign('checkout_success_text', $checkout_success_text);
        return parent::getContent('success');
    }
    public function getCancelledPage() {
        return parent::getContent('cancelled');
    }
    public function getNavigation() {
        $steps = array(
            'cart' => array('selected' => 0, 'label' => lang::_('Shopping Cart')), 
            'checkout' => array('selected' => 0, 'label' => lang::_('Checkout')), 
            'confirnation' => array('selected' => 0, 'label' => lang::_('Confirmation')), 
            'success' => array('selected' => 0, 'label' => lang::_('Success'))
        );
        $page = frame::_()->getModule('pages')->getCurrent();
        if(!empty($page)) {
            switch($page->action) {
                case 'getShoppingCart':
                    $steps['cart']['selected'] = 1;
                    break;
                case 'getAllHtml':
                    switch(frame::_()->getModule('checkout')->getStep()) {
                        case 1:
                            $steps['checkout']['selected'] = 1;
                            break;
                        case 2:
                            $steps['confirnation']['selected'] = 1;
                            break;
                        case 3:
                            $steps['success']['selected'] = 1;
                            break;
                    }
                    break;
            }
        }
        if(frame::_()->getModule('options')->get('checkout_skip_confirm_step'))
            unset($steps['confirnation']);
        $this->assign('steps', $steps);
        return parent::getContent('navigation');
    }
}
?>