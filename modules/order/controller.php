<?php
class orderController extends controller {
    /**
     * Add or update order from checkout page
     */
    public function addFromCheckout() {
        $url = '';
        $post = req::get('post');
        $res = $this->validateStep1($post);
		//if registration from checkout is enabled and unregistered users don't allow to checkout
        if(isset($post['registration']) && !frame::_()->getModule('options')->get('allow_guest_checkout')) {  
            $userAddErrors = frame::_()->getModule('user')->getModel()->validate($post['registration']);
            if(!empty($userAddErrors)) {
                $regErrors = array();
                foreach($userAddErrors as $key => $error)
                    $regErrors['registration['. $key. ']'] = lang::_('Registration: '). $error;     //To touch only registration fields
                $res->pushError( $regErrors );
            }
        }
        if(!$res->error) {
            if(!frame::_()->getModule('user')->getCurrentID()) {
                if(isset($post['registration']) && frame::_()->getModule('options')->get('checkout_force_create_user')) {
                    if(empty($post['registration']['password'])) {
                        $post['registration']['password'] = wp_generate_password(12, false);
                        $post['registration']['re_password'] = $post['registration']['password'];
					}
                    if(empty($post['registration']['re_password'])) {
                        $post['registration']['re_password'] = $post['registration']['password'];
					}
                    $post['registration']['email'] = frame::_()->getModule('user')->getCurrentEmail();
                    if(empty($post['registration']['email'])) {
                        $post['registration']['username'] = utils::getRandStr();
                        $post['registration']['email'] = $post['registration']['username']. '@temp.com';
                    } else {
                        $userNameArr = explode('@', $post['registration']['email']);
                        $post['registration']['username'] = $userNameArr[0];
                    }
                }
                if(isset($post['registration'])) {                      //if registration from checkout is enabled
				
					$post['registration']['forceLogin'] = true;         //make he logged right affter his add
					if(!isset($post['guestCheckout']))
					{
						if(isset($post['registration']['password'])&&empty($post['registration']['password']))
							$res->pushError(lang::_('Password cannot be empty'));
						if(isset($post['registration']['re_password'])&&empty($post['registration']['re_password']))
							$res->pushError(lang::_('Repassword cannot be empty'));
						if(!$res->error) {
							$user_res = frame::_()->getModule('user')->getModel()->post( $post['registration'], true /*This will skip validation*/ );
							if($user_res->error) {
								$res->errors = array_merge($res->errors,$user_res->errors);
								$res->error = true;
							} else {
								req::setVar('loggedOnCheckoutRegister', true, 'session'); //to know that we logged him, @see checkoutView::getConfirnationPage()
							}
						}
					}
                }
            }
        }
		if(!$res->error) {
				if($id = $this->getModel()->post($post)) {
					$res->setHtml( frame::_()->getModule('checkout')->getView('checkout')->getConfirnationPage() );
				} else {
					errors::push(lang::_('Can not add Order Record'));
					$res->addError( errors::get() );
				}
		}
        return $res->ajaxExec();
    }
    public function validateStep1($d = array()) {
        return frame::_()->getModule('order')->getModel()->prePostValidate($d);
    }
    public function addSuccess() {
        $id = frame::_()->getModule('order')->getCurrentID();
        $order = frame::_()->getModule('order')->getCurrent();
        $res = new response();
        if($id && is_numeric($id)) {
            $from = req::getVar('from');
            $status = ($from == 'payments') ? 'paid' : 'pending';
            if($from == 'paymentFail')
                $status = 'cancelled';
            //Payment module validation
            if(frame::_()->getModule($order['payment_module']) && !frame::_()->getModule($order['payment_module'])->validateResponce()) {
                $status = 'cancelled';
            }
			if($status != 'cancelled' && frame::_()->getModule($order['payment_module'])) {
				$paymentModOrderStatus = frame::_()->getModule($order['payment_module'])->getParam('success_order_status');
				if(!empty($paymentModOrderStatus))
					$status = $paymentModOrderStatus;
			}
            $otherValidation = dispatcher::applyFilters('validateOrderSuccess', true, $id, $from);
            if($otherValidation) {
                frame::_()->getModule('order')->getModel('order')->put(array(
                    'id' => $id,
                    'status' => $status,
                    'user_id' => frame::_()->getModule('user')->getCurrentID(),     //If user was logged on checkout
                ));
                if($status != 'cancelled') {
                    $order = frame::_()->getModule('order')->getModel('order')->get($id);
                    if(frame::_()->getModule('options')->get('stock_substract')) {
                        frame::_()->getModule('products')->updateStock(
                            frame::_()->getModule('user')->getModel('cart')->get()
                        );
                    }
					//exit();
                    frame::_()->getModule('user')->getModel('cart')->delete();
                    frame::_()->getModule('order')->unsetCurrentID();
                    req::clearVar('loggedOnCheckoutRegister', 'session');
                }
                $reqType = req::getVar('reqType');
                if($reqType == 'ajax') {
                    if($status == 'cancelled')
                        $res->setHtml( frame::_()->getModule('checkout')->getView('checkout')->getCancelledPage() );
                    else
                        $res->setHtml( frame::_()->getModule('checkout')->getView('checkout')->getSuccessPage() );
                }
                if($status == 'cancelled') {
                    req::setVar('redirect', frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml', 'data' => array('step' => 4))), 'get');
                }
                dispatcher::exec('onSuccessOrder', $order);
                dispatcher::doAction('onSuccessOrder', $order);
                $this->sendNotification($order);
            }
        } else
            $res->addError(lang::_('Invalid Order Num'));
        return $res->ajaxExec();
    }
    public function showOne(){
        $res = new response();
        $id = req::getVar('id');
        if($id) {
            $reqType = req::getVar('reqType');
            $html = $this->getView()->showOne($id);
            if($reqType == 'ajax')
                $res->setHtml( $html );
            else
                return $html;
        } else 
            $res->addError(lang::_('No Order ID were specified'));
        return $res->ajaxExec();
    }
	public function showOneFull() {
		$res = new response();
        $id = req::getVar('id');
        if($id) {
            $reqType = req::getVar('reqType');
            $html = $this->getView()->showOneFull($id);
            if($reqType == 'ajax')
                $res->setHtml( $html );
            else
                return $html;
        } else 
            $res->addError(lang::_('No Order ID were specified'));
        return $res->ajaxExec();
	}
	
	
	public function removeProductFromOrder(){
		$data=req::get('post');
		$res = new response();
		if(!isset($data['productID']) || !isset($data['orderID'])){
			$res->pushError(lang::_('Nothing to delete'));
			return $res->ajaxExec();
		}
		$result = $this->getModel()->removeProductFromOrder($data);
		if($result){
			$res->addMessage($result);
		}else{
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	
	public function addProductsToOrder(){
		$data = req::get('post');
		$res = new response();
		if(empty($data['params']) || empty($data['params']['products']) || empty($data['params']['orderId'])){
			$res->pushError(lang::_('Empty Data'));
			return $res->ajaxExec();
		}
		if($this->getModel()->addProductsToOrder($data['params'])){
			$res->addMessage(lang::_('Updated'));
		}else{
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
    public function updateOrder() {
        $res = new response();
        if($this->getModel()->put(req::get('post'))) {
            $res->addMessage (lang::_('Updated'));
			$res->data = $this->getModel()->get( req::getVar('id') );
			if(isset($res->data['total']))
				$res->data['total'] = frame::_()->getModule('currency')->display($res->data['total'], $res->data['currency']);	//we will use it to update data in orders table
        } else
            $res->addError (lang::_('Update Error'));
        return $res->ajaxExec();
    }
    /**
     * Sends the notification on success order placement
     * 
     * @param array $order 
     */
    public function sendNotification($order) {
        $variables = $this->getModel()->getSuccessVariables(array('order' => $order));
        $customerEmail = frame::_()->getModule('user')->getCurrentEmail($order);
		$storeEmail = frame::_()->getModule('options')->get('store_email');
        if(!empty($customerEmail))
            frame::_()->getModule('messenger')->getController()->sendNotification($customerEmail, 'order', 'success_placement', $variables);
		if(!empty($storeEmail))
			frame::_()->getModule('messenger')->getController()->sendNotification($storeEmail, 'order', 'success_placement', $variables);
        return true;
    }
	public function getAuditHtml() {
		$res = new response();
		$oid = (int) req::getVar('oid');
		if($oid) {
			$res->setHtml( $this->getView()->showAudit($oid) );
		} else
			$res->pushError (lang::_('Invalid Order ID'));
		$res->ajaxExec();
	}
    public function remove() {
		$res = new response();
		if($id = $this->getModel()->remove(req::get('post'))) {
			$res->addMessage(lang::_('Done'));
			$res->addData('id', $id);
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			S_USERLEVELS => array(
				S_ADMIN => array('remove'),
				//S_LOGGED => array('updateOrder', 'getAuditHtml'),
			),
		);
	}
}
?>
