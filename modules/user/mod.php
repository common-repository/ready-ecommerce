<?php
class toecuser extends module {
    public function init() {
        //add_action('plugins_loaded', array($this, 'loadUserData'));
        add_action('show_user_profile', array($this, 'addProfileFieldsHtml'));
        add_action('edit_user_profile', array($this, 'addProfileFieldsHtml'));
        add_action('personal_options_update', array($this, 'storeUserProfileData'));
        add_action('edit_user_profile_update', array($this, 'storeUserProfileData'));
		// This functionality is not working for now, but it it required - it need to work in other way, not throught controller
        //dispatcher::add('onAfterLogin', array($this->getController()->getModel('cart'), 'restoreFromDB'));
        parent::init();
    }
    public function loadUserData() {
        //return $this->getCurrent();
    }
    public function addProfileFieldsHtml($user) {
        //if($this->isCustomer($user->ID)) {
            $this->getController()->getView('user')->displayAllMeta($user->ID, array('exclude' => 'email'));
        //}
    }
    public function isAdmin() {
        $currentUser = $this->getCurrent();
        if(!empty($currentUser)) {
            return (bool) $currentUser->isAdmin;
        }
        return false;
    }
	public function getCurrentUserPosition() {
		if($this->isAdmin())
			return S_ADMIN;
		else if($this->getCurrentID())
			return S_LOGGED;
		else 
			return S_GUEST;
	}
    public function getCurrent() {
        return $this->getController()->getModel('user')->get();
    }
    public function isCustomer($id = 0) {
        return $this->getController()->getModel('user')->isCustomer($id);
    }
    public function isGuest($id = 0) {
        
    }
    public function getCurrentID() {
        return $this->getController()->getModel()->getCurrentID();
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('User Fields'), $this->getCode());
        $tab->setView('userFieldsTab');
		$tab->setSortOrder(3);
		$tab->setParent('templates');
		$tab->setNestingLevel(1);
        $tabs[] = $tab;
        return $tabs;
    }
    public function getCurrentEmail($order = array()) {
		if(isset($order['email']))
			return $order['email'];
        $user = frame::_()->getModule('user')->getCurrent();
		$customerEmail = '';
		if(!empty($user) && is_object($user))
			$customerEmail = $user->user_email;
        if(empty($customerEmail) && empty($order)) 
            $order = frame::_()->getModule('order')->getCurrent();
        if(empty($customerEmail) && !empty($order)) {
			if(!empty($order['shipping_address']) && is_string($order['shipping_address'])) {
				$order['shipping_address'] = utils::jsonDecode($order['shipping_address']);
			}
            if(!empty($order['shipping_address'])) {
                foreach($order['shipping_address'] as $k => $v) {
                    if(preg_match('/^email/i', $k)) {
                        $customerEmail = $v;
                        break;
                    }
                }
            }
			if(!empty($order['billing_address']) && is_string($order['billing_address'])) {
				$order['billing_address'] = utils::jsonDecode($order['billing_address']);
			}
            if(empty($customerEmail) && !empty($order['billing_address'])) {
                foreach($order['billing_address'] as $k => $v) {
                    if(preg_match('/^email/i', $k)) {
                        $customerEmail = $v;
                        break;
                    }
                }
            }
        }
        return $customerEmail;
    }
    public function getCartItems($d = array()) {
        return $this->getController()->getModel('cart')->get($d);
    }
	public function getRoles() {
		global $wp_roles;

		$allRoles = $wp_roles->roles;
		$editableRoles = apply_filters('editable_roles', $allRoles);

		return $editableRoles;
	}
	public function getWordpressUsers($d = array()) {
		return get_users($d);
	}
}
