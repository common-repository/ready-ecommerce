<?php
class userController extends controller {
    public function getLoginForm() {
		$isLogged = frame::_()->getModule('user')->getCurrent();
		if($isLogged)	// For logged in users - show account page
			add_filter('the_content', array($this->getView(), 'getAccountSummary'));
		else
			add_filter('the_content', array($this->getView('login'), 'getContent'));
    }
    public function getRegisterForm() {
		$isLogged = frame::_()->getModule('user')->getCurrent();
		$reqType = req::getVar('reqType');
		if($reqType == 'ajax') {
			$res = new response();
			$res->setHtml($this->getView('register')->getContent());
			$res->ajaxExec();
		} else {
			if($isLogged)	// For logged in users - show account page
				add_filter('the_content', array($this->getView(), 'getAccountSummary'));
			else
				add_filter('the_content', array($this->getView('register'), 'getContent'));
		}
    }
    public function getShoppingCart() {
        add_filter('the_content', array($this->getView('cart'), 'displayCart'));
    }
    public function getShoppingCartHtmlAjax() {
        $res = new response();
        $res->setHtml(frame::_()->getModule('cartwidget')->getView()->getContentHtml());
        $res->ajaxExec();
    }
	public function getShoppingCartHtmlForCheckoutAjax() {
		$res = new response();
        $res->setHtml(frame::_()->getModule('user')->getView('cart')->getCartContent(false));
        $res->ajaxExec();
	}
    public function postRegisterLoad() {
        frame::_()->loadPlugins();
        $this->postRegister();
    }
    public function postRegister() {
        $res = $this->getModel('user')->post(req::get('post'));
        $res->ajaxExec();
    }
    public function addToCart() {
        $res = new response();
		$redirect = '';
		$reqType = req::getVar('reqType');
		$goto = req::getVar('goto');
        if($this->getModel('cart')->put(array(
            'pid' => req::getVar('pid'), 
            'qty' => req::getVar('qty'), 
            'addQty' => req::getVar('addQty'),
            'options' => req::getVar('options'),
        ))) {
            $res->addMessage(lang::_('Product was added to cart'));
            switch($goto) {
                    case 'checkout':
						switch(frame::_()->getModule('options')->get('buy_now_redirect')) {
							case 'shopping_cart':
								$redirect = frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getShoppingCart'));
								break;
							case 'checkout':
							default:
								$redirect = frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'));
								break;
						}
                        break;
                    default:     //For non-ajax - just go to shopping cart page
                        if($reqType != 'ajax') {
                            redirect(frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getShoppingCart')));
                        }
                        break;
            }
            $res->setHtml(frame::_()->getModule('user')->getView('cart')->afterProductAddMsg());
        } else {
			$gotoOnError = req::getVar('gotoOnError');
			$errors = $this->getModel('cart')->getErrors();
			// We need redirect for case if we have where to go and have errors only from certain codes
			if(!empty($gotoOnError) && in_array_array($this->getModel('cart')->getErrorCodes(), array('product_field'))) {
				switch($gotoOnError) {
					case 'product':
						$pid = (int) req::getVar('pid');
						if($pid) {
							$redirect = uri::_(array('baseUrl' => frame::_()->getModule('products')->getLink($pid), S_ERRORS => $errors));
						}
						break;
				}
			}
            $res->pushError($errors);
        }
		if(!empty($redirect)) {
			if($reqType == 'ajax')
				$res->addData('redirect', $redirect);
			else
				redirect($redirect);
		}
        $res->ajaxExec();
    }
    public function updateCart() {
        $res = new response();
        $pid = req::getVar('pid');
        $qty = req::getVar('qty');
        $inCartId = req::getVar('inCartId');
        $inCartId = is_numeric($inCartId) ? (int) $inCartId : false;
        if($pid || $inCartId !== false) {
            if($qty) {
                    if($this->getModel('cart')->put(array(
                    /** @deprecated @see inCartId*/
                    'pid' => $pid,
                    'qty' => $qty,
                    'inCartId' => $inCartId,
                ))) {
                    $product = $this->getModel('cart')->get($inCartId);
                    $res->data['cart'] = array(
                        /** @deprecated @see inCartId*/
                        'pid' => $pid,
                        'inCartId' => $inCartId,
                        'qty' => $product['qty'],
                        'total' => frame::_()->getModule('currency')->displayTotal($product['price'], $product['qty'], $pid));
                    $res->messages[] = lang::_('Updated');
                } else {
                    if($modelErrors = $this->getModel('cart')->getErrors())
                        $res->addError($modelErrors);
                    else
                        $res->addError(lang::_('Update Error'));
                }
            } else {
                $this->getModel('cart')->delete($inCartId);
                $res->data['cart'] = array(
                        'inCartId' => $inCartId,
                        /** @deprecated @see inCartId*/
                        'pid' => $pid,
                        'deleted' => true);
                $res->messages[] = lang::_('Deleted');
            }
			$res = dispatcher::applyFilters('afterCartUpdateAction', $res);
			$res->data['newCartData'] = $this->getModel('cart')->get();
            $res->data['totalHtml'] = frame::_()->getModule('checkout')->getView()->getTotal();
        } else
            $res->addError(lang::_('Invalid product ID specified'));
        return $res->ajaxExec();
    }
    public function clearCart() {
        $res = new response();
        $this->getModel('cart')->delete();
        $res->addMessage(lang::_('Done'));
        return $res->ajaxExec();
    }
    public function getAccountSummaryHtml() {
        add_filter('the_content', array($this->getView('user'), 'getAccountSummary'));
    }
    public function getProfileHtml() {
        add_filter('the_content', array($this->getView('user'), 'getProfileEdit'));
    }
	public function getPasswordRecoverConfirm() {
		$uid = (int) req::getVar('uid');
		$hash = req::getVar('hash');
		if($uid && md5(SECURE_AUTH_KEY. $uid) == $hash) {
			$this->resetPassword($uid);
			$this->getView('user')->setPasswordResetSuccess(true);	//Set this war in view to display success message
		}
		add_filter('the_content', array($this->getView('user'), 'getPasswordRecoverConfirm'));
	}
    public function postLogin() {
        $res = new response();
        $log = req::getVar('user_login', 'post');
        $pwd = req::getVar('user_password', 'post');
        if(empty($log))
            $res->addError(lang::_('Empty Username'), 'user_login');
        if(empty($pwd))
            $res->addError(lang::_('Empty Password'), 'user_password');
        $loginResult = wp_signon(req::get('post'), false);
        if(is_wp_error($loginResult))  
            $res->addError($loginResult->get_error_messages());
        else {
            frame::_()->getModule('user')->getController()->getModel()->setCurrentID($loginResult->ID);
            frame::_()->getModule('user')->getController()->getModel()->get($loginResult->ID);  //Load current user data
            dispatcher::exec('onAfterLogin', array('user_id' => $loginResult->data->ID));
            $redirect = req::getVar('toeReturn') ? req::getVar('toeReturn') : frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getAccountSummaryHtml'));
            $res->addData('redirect', $redirect);
            $res->addMessage(lang::_('Welcome!'));
        }
        if($res->error)
            $res->addError(lang::_('Can not login'));
        return $res->ajaxExec();
    }
    public function putProfile() {
        $res = new response();
        $uid = frame::_()->getModule('user')->getModel()->getCurrentID();
        $data = req::get('post');
        $data['ID'] = $uid;
        $res->addError( $this->getModel()->validateUpdate($data) );
        if(!$res->error) {
            $this->getModel()->updateStoreData($uid, $data);
            $this->getModel()->updateWpData($uid, $data);
            $res->addMessage(lang::_('Updated'));
        }
        return $res->ajaxExec();
    }
    private function _getResetPasswordEmailVars($d = array()) {
        $user = frame::_()->getModule('user')->getCurrent();
        $user = toeObjectToArray($user);
        return array(
            'first_name' => @$user->first_name,
            'store_name' => frame::_()->getModule('options')->get('store_name'),
            'password' => $d['password']
        );
    }
    public function resetPassword($uid = 0) {
        $res = new response();
		$internalReset = false;
		if($uid) {
			$internalReset = true;
		} else {
			$uid = (int) req::getVar('uid');
			if(!$uid) 
				$uid = frame::_()->getModule('user')->getModel()->getCurrentID();
		}
        
        if(current_user_can('edit_user', $uid) || $internalReset) {
            $data = array('password' => wp_generate_password(12, false));
            $this->getModel()->updateStoreData($uid, $data);
            $this->getModel()->updateWpData($uid, $data);
            $userData = frame::_()->getModule('user')->getModel()->get($uid);
            if(!empty($userData) && is_object($userData) && isset($userData->data)) {
                $userEmail = $userData->data->user_email;
            }
            if(!empty($userEmail)) {
                frame::_()->getModule('messenger')->getController()->sendNotification($userEmail, 'user', 'reset_password', $this->_getResetPasswordEmailVars(array('password' => $data['password'])));
            }
            $res->addMessage(lang::_('Password was changed. Email with new password was sent to '. $userEmail. '. Check your email for new password. If you did not receive email - please contact store administrator.'));
        } else 
            $res->pushError(lang::_('You have no permisions'));
        return $res->ajaxExec();
    }
    public function getOrdersList() {
        add_filter('the_content', array($this->getView('user'), 'getOrdersList'));
    }
    /**
     * Store extra user fields to database
     * @param int $uid
     * @param array $d
     * @return void 
     */
    public function storeUserProfileData($uid, $d = array()) {
        if (!current_user_can( 'edit_user', $uid ))
            return false;
        /* update store metadata */
        $this->getModel()->updateStoreData($uid, $d);
    }
	public function getPasswordForgotFormHtml() {
		$res = new response();
		if(!frame::_()->getModule('user')->getCurrentID()) {
			$res->setHtml( $this->getView('login')->getPasswordForgotFormHtml() );
		} else
			$res->pushError (lang::_('You are logged in now'));
		return $res->ajaxExec();
	}
	public function passwordRecoverSendLink() {
		$res = new response();
		if(recapcha::_()->check()) {
			if($this->getModel()->passwordRecoverSendLink(req::get('post'))) {
				$res->addMessage(lang::_('Email for password reset confirmation was sent to requested email address.'));
			} else
				$res->pushError ($this->getModel()->getErrors());
		} else
			$res->pushError(lang::_('Invalid capcha'));
		return $res->ajaxExec();
	}
}