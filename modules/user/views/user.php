<?php
class userView extends view {
	private $_passwordResetSuccess = false;	//This will set to true in password reset success case in userController
	public function setPasswordResetSuccess($val) {
		$this->_passwordResetSuccess = $val;
	}
    public function displayAllMeta($uid = 0, $params = array()) {
        if(!$uid)
            $uid = frame::_()->getModule('user')->getModel()->getCurrentID();
        $metaFields = frame::_()->getModule('user')->getModel()->getUserMeta($uid, 'registration');
		if(isset($params['exclude']) && !empty($params['exclude'])) {
			if(!is_array($params['exclude']))
				$params['exclude'] = array($params['exclude']);
			foreach($params['exclude'] as $excludeKey) {
				if(isset($metaFields[ $excludeKey ]))
					unset($metaFields[ $excludeKey ]);
			}
		}

        $showPassword = false;
        $currentUserData = frame::_()->getModule('user')->getModel()->get();
        if($currentUserData->data->isAdmin && is_admin()) {
            $showPassword = true;
        }
		$haveOrders = frame::_()->getModule('user')->isAdmin() && frame::_()->getModule('order')->getModel()->userHaveOrders( $uid );
        $this->assign('showPassword', $showPassword);
        $this->assign('uid', $uid);
        $this->assign('metaFields', $metaFields);
		$this->assign('haveOrders', $haveOrders);
        parent::display('metaFields');
    }
    public function getAccountSummary() {
        return $this->getContent('accountSummary');
    }
    public function getProfileEdit() {
        $userData = frame::_()->getModule('user')->getModel()->get();
        $this->assign('userData', $userData);
        return $this->getContent('profile');
    }
    public function getOrdersList($uid = 0) {
        $user = frame::_()->getModule('user')->getCurrent();
        $searchCriteria = array();
        if(!$user->isAdmin) {
            if(!$uid || !is_numeric($uid))  //!is_numeric($uid) is becouse WP add some first parametr when adding the_content hook
                $uid = $user->ID;
            $searchCriteria['user_id'] = $uid;
        }
        frame::_()->getModule('order')->getView()->getAllOrders( $searchCriteria );
    }
	public function getPasswordRecoverConfirm() {
		$errors = array();
		if(!$this->_passwordResetSuccess) {
			$errors[] = lang::_('Password Reset Error');
		}
		$this->_passwordResetSuccess = false;
		$this->assign('errors', $errors);
		return parent::getContent('passwordRecoverConfirm');
	}
}
