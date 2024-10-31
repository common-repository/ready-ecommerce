<?php
class userModel extends model {
    protected $_data = array();
    protected $_curentID = 0;
    protected $_meta = array(
        
    );
	protected $_currentWasLoaded = false;
    /**
     * This is standart wordpress user fields for registration
     */
    protected $_standartFields = array(
        'username' => array('type' => 'text', 'label' => 'Username', 'nameInWp' => 'user_login'),
        'email' => array('type' => 'text', 'label' => 'Email', 'nameInWp' => 'user_email'),
        'password' => array('type' => 'password', 'label' => 'Password', 'nameInWp' => 'user_pass'),
        're_password' => array('type' => 'password', 'label' => 'Repeat Password', 'nameInWp' => 'user_pass'),
    );
    public function __construct() {
        $allFields = frame::_()->getModule('options')->getModel('userfields')->get(array('activeOnly' => true));
        if($allFields) {
            foreach($allFields as $f) {
                $this->_meta[$f['code']] = new field($f['code'], $f['type'], 'other', '', $f['label']);
                $this->_meta[$f['code']]->id = $f['id'];
                $this->_meta[$f['code']]->mandatory = $f['mandatory'];
                $this->_meta[$f['code']]->destination = is_array($f['destination']) ? $f['destination'] : fieldAdapter::userFieldDestFromDB($f['destination']);
                $this->_meta[$f['code']]->default_value = $f['default_value'];
                if(in_array($f['type'], array('checkboxlist', 'selectbox', 'radiobuttons', 'selectlist'))) { //Checkboxes, Drop Down, Radio Buttons, List
                    $options = frame::_()->getModule('options')->getModel('extraoptions')->get(array('ef_id' => (int) $f['id']));
                    if(!empty($options) && is_array($options)) {
                        if(in_array($f['type'], array('checkboxlist'))) {
                            $this->_meta[$f['code']]->htmlParams['options'] = $options;
                        }
                        foreach($options as $i => $opt) {
                            if(in_array($f['type'], array('checkboxlist'))) {
                                $this->_meta[$f['code']]->htmlParams['options'][ $i ]['text'] = $opt['value'];
                            } else {
                                $this->_meta[$f['code']]->htmlParams['options'][ $opt['id'] ] = $opt['value'];
                            }
                        }
                    }
                } else {
                    $this->_meta[$f['code']]->htmlParams = utils::jsonDecode($f['params']);
                }
            }
        }
        $this->_getUserData();
    }
    public function getStandartFields() {
        return $this->_standartFields;
    }
    public function getStandartFieldsAsObjects($d = array('exclude' => array(), 'fillWithCurrentValues' => false)) {
        $standartFields = array();
        if($d['fillWithCurrentValues']) {
            $userData = $this->get();
        }
        foreach($this->getStandartFields() as $fKey => $fInfo) {
            if(in_array($fKey, $d['exclude'])) continue;
            $standartFields[$fKey] = toeCreateObj('field', array($fKey, $fInfo['type'], 'other', '', $fInfo['label']));
            if($d['fillWithCurrentValues']) {
                $standartFields[$fKey]->setValue($userData->{ $fInfo['nameInWp'] });
            }
        }
        return $standartFields;
    }
    public function get($id = 0) {
		if(empty($id))
			$this->_currentWasLoaded = true;
		$id = $this->_curentID;
        if(isset($this->_data[$id])) {
            if(isset($this->_data[$id])) {
                return $this->_data[$id];
            } elseif($id) {                 //Must be deprecated
                return $this->_getUserData($id);
            }
        } else
            return $this->_getUserData($id);
    }
    protected function _getUserData($id = 0) {
        $extraval = frame::_()->getModule('options')->getModel('extrafieldsvalue');
        $user = NULL;
        if(!$id) {
            if(empty($this->_curentID)) {
                if(!function_exists('wp_get_current_user')) frame::_()->loadPlugins();
                $user = wp_get_current_user();
                $user = $user->data;
				if(empty($user))
					$this->_curentID = 0;
				else
					$this->_curentID = $user->ID;
            } else {
                $user = get_userdata($this->_curentID);
            }
        } else
            $user = get_userdata($id);
        if($user) {
            $this->_data[$user->ID] = $user;
			$efIds = array();
			foreach($this->_meta as $f => $attr) {
				$efIds[] = $this->_meta[$f]->id;
			}
			$field_values = $extraval->get($user->ID, S_USER, $efIds);
			if(!$field_values)
				$field_values = array();
			else {
				$tmp_field_values = $field_values;
				$field_values = array();
				foreach($tmp_field_values as $fv) {
					if(!isset($fv['ef_id'])) continue;
					$field_values[ $fv['ef_id'] ] = $fv['value'];
				}
			}
            foreach($this->_meta as $f => $attr) {
				$this->_data[$user->ID]->$f = (isset($field_values[$this->_meta[$f]->id]) && !empty($field_values[$this->_meta[$f]->id])) 
					? $field_values[$this->_meta[$f]->id] 
					:  $this->_meta[$f]->default_value;
            }
            
            $this->_data[$user->ID]->toe_customer = get_user_meta($user->ID, 'toe_customer');
            if(is_super_admin($user->ID))
                $this->_data[$user->ID]->isAdmin = true;
            else
                $this->_data[$user->ID]->isAdmin = false;
            return $this->_data[$user->ID];
        }
        return NULL;
    }
    public function getMeta() {
        return $this->_meta;
    }
    public function getCurrentID() {
		if(!$this->_currentWasLoaded)
			$this->get();
        return intval($this->_curentID);
    }
    public function setCurrentID($id) {
        $id = (int) $id;
        if($id)
            $this->_curentID = $id;
    }
    public function getUserMeta($id = 0, $destination = 'all', $order = array()) {
        if(!$id) {
            if(!$this->_curentID)
                $this->_getUserData();
            $id = $this->_curentID;
        }
        if(!isset($this->_data[$id]) || !$this->_data[$id])
            $this->_getUserData($id);
        foreach($this->_meta as $f => $attr) {
            if(($destination == 'all') || in_array($destination, $attr->destination)) {
                $res[$f] = clone($attr);
                if(in_array($attr->html, array('textFieldsDynamicTable')) && !is_array($this->_data[$id]->$f)) {
                    $this->_data[$id]->$f = utils::jsonDecode( $this->_data[$id]->$f );
                }
                if(empty($order)) {
                    if($attr->html == 'checkboxlist') {
                        $options = $res[$f]->getHtmlParam('options');
                        if(!is_array($this->_data[$id]->$f))
                            $this->_data[$id]->$f = utils::jsonDecode($this->_data[$id]->$f);
                        if(empty($this->_data[$id]->$f))
                            $this->_data[$id]->$f = array();
                        elseif(!is_array($this->_data[$id]->$f))
                            $this->_data[$id]->$f = array($this->_data[$id]->$f);
                        if(!empty($options) && !empty($this->_data[$id]->$f)) {
                            foreach($options as $optI => $opt) {
                                if(in_array($opt['id'], $this->_data[$id]->$f)) {
                                    $options[ $optI ]['checked'] = true;
                                }
                            }
                            $res[$f]->setHtmlParam('options', $options);
                        }
                    } elseif($attr->html == 'selectlist') {
                        if(!is_array($this->_data[$id]->$f))
                            $this->_data[$id]->$f = utils::jsonDecode ($this->_data[$id]->$f);
                        if(empty($this->_data[$id]->$f))
                            $this->_data[$id]->$f = array();
                        $res[$f]->setValue($this->_data[$id]->$f);
                    } else {
                        $res[$f]->setValue(@$this->_data[$id]->$f);
                    }
                } else {
                    $res[$f]->setValue($order[$destination. '_address'][$f]);
                }
            }
        }
        return $res;
    }
    public function getMetaField($id, $metakey) {
        if(!$this->_data[$id])
            $id = $this->_curentID;
        if($this->_data[$id]) {
            if(!$this->_data[$id]->$metakey) {
                $this->_data[$id]->$metakey = get_usermeta($id, $metakey);
            }
            return $this->_data[$id]->$metakey;
        }
        return NULL;
    }
    public function isCustomer($id = 0) {
		if(!$this->_currentWasLoaded)
			$this->get();
        return empty($this->_curentID) ? false : true;
    }
    public function validate($d = array(), $for = array('username', 'password')) {
        $errors = array();
        if(empty($d['useFor']))
            $d['useFor'] = 'registration';
        $fieldsErrors = frame::_()->getModule('options')->getModel('userfields')->validate($d);
        if(!empty($fieldsErrors)) {
            $errors = array_merge($errors, $fieldsErrors);
        }
        return $errors;
    }
    public function validateUpdate($d = array()) {
        return $this->validate($d, array());
    }
    public function post($d = array(), $skipValidation = false) {
        $res = new response();
        if(!empty($d)) {
			$d = db::prepareHtml($d);
            if(!$skipValidation)
                $res->pushError( $this->validate($d) );
			if(isset($d['password']) && isset($d['re_password']) && $d['password'] != $d['re_password']) {
				$res->pushError(lang::_('Password do not match'));
			}
            if(empty($res->errors)) {
                $d['password'] = trim($d['password']);
                if(empty($d['password']))
                    $d['password'] = wp_generate_password(12, false);
				
                if(empty($d['username']) && !empty($d['email'])) {
                    $d['username'] = $this->getUsernameByEmail($d);
                }
                $user_id = wp_create_user($d['username'], $d['password'], $d['email']);
                if(is_object($user_id)) {   //WP Error generated
                    if($user_id->errors) {
                        foreach($user_id->errors as $e) {
							$res->pushError($e);
                        }
                    }
                } elseif(is_numeric($user_id)) {
                    $parent_type = empty($d['mod']) ? S_USER : $d['mod'];
                    foreach($this->_meta as $key => $f) {
                        //$ef_id = $d['extra_field'][$key];     //this was a bug, but not sure for this moment
                        if(is_array($d[$key]))
                            $d[$key] = utils::jsonEncode($d[$key]);
                        $data = array(
                                'parent_id' => $user_id,
                                'parent_type' => $parent_type,
                                'value' => $d[$key],
                                'ef_id' => $f->id,
                            );
                        frame::_()->getModule('options')->getModel('extrafieldsvalue')->insert($data);
                    }
                    add_user_meta($user_id, 'toe_customer', 'true');
                    if(!empty($d['email'])) {
                        frame::_()->getModule('messenger')->getController()->sendNotification($d['email'], 'user', 'registration', array_merge($d, array('store_name' => frame::_()->getModule('options')->get('store_name'))));
                    }
					$notify_admin = frame::_()->getModule('options')->get('notify_on_reg');
					$store_email = frame::_()->getModule('options')->get('store_email');
					if(($notify_admin)&&(!empty($store_email))) {
						frame::_()->getModule('messenger')->getController()->sendNotification($store_email, 'user', 'admin_notify', array_merge($d, array('store_name' => frame::_()->getModule('options')->get('store_name'))));
					}
                    $res->messages[] = lang::_('Registration Success');
                } else 
                    $res->pushError(lang::_('Error add user'));
            }
        } else 
            $res->pushError(lang::_('No data was specified'));
        if(!$res->error() && req::getVar('toeReturn')) {
            $res->addData('redirect', req::getVar('toeReturn'));
        }
        if(!$res->error() && (frame::_()->getModule('options')->get('login_after_register') || $d['forceLogin'])) { //forceLogin uses when we want to create user from code and make he logged in right after it
            $reqType = req::getVar('reqType');
            req::setVar('reqType', 'nonAjax', 'post');
			req::setVar('reqType', 'nonAjax', 'get');
            req::setVar('user_login', $d['username'], 'post');
            req::setVar('user_password', $d['password'], 'post');
            frame::_()->getModule('user')->getController()->postLogin();
            $this->_getUserData();
            req::setVar('reqType', $reqType, 'post');
			req::setVar('reqType', $reqType, 'get');
        }
		if(!$res->error()) {
			$this->_setAdditionalMetaFields($user_id, $d);
		}
        return $res;
    }
	/**
	 * This method was created to add additional meta data for users.
	 * For now - it is hardcoded, and used for our site readyshoppingcart.com only.
	 */
	private function _setAdditionalMetaFields($userId, $d = array()) {
		if(isset($d['meta_fields']) && !empty($d['meta_fields']) && is_array($d['meta_fields'])) {
			$availableMetaFields = array('toe_reg_for_tpl', 'toe_reg_for_affiliate');
			foreach($availableMetaFields as $metaKey) {
				if(isset($d['meta_fields'][$metaKey]) && !empty($d['meta_fields'][$metaKey])) {
					add_user_meta($userId, $metaKey, $d['meta_fields'][$metaKey]);
				}
			}
		}
	}
    public function getUsernameByEmail($d = array('email' => '')) {
        $nameDomain = explode('@', $d['email']);
        $username = $nameDomain[0];
        if(username_exists($username)) {
            $username .= mt_rand(1, 999999);
        }
        return $username;
    }
    /**
     * Update data in User Fields
     */
    public function updateStoreData($uid, $d = array()) {
		$d = db::prepareHtml($d);
        $metaFields = frame::_()->getModule('user')->getModel()->getUserMeta($uid, 'registration');
        frame::_()->getModule('options')->getModel('extrafieldsvalue')->saveExtraFields($d, $metaFields, $uid, S_USER);
    }
    /**
     * Update data in wordpress user tables
     */
    public function updateWpData($uid, $d = array()) {
        $wpKeyToData = array(   //relation wordpress user key to our store user key data
            'user_pass' => 'password',
            'user_email' => 'email',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
        ); 
        foreach($wpKeyToData as $wpKey => $storeKey) {
            if(isset($d[ $storeKey ]))
                $d[ $wpKey ] = $d[ $storeKey ];
        }
        $d['ID'] = $uid;
        wp_update_user($d);
    }
	public function passwordRecoverSendLink($d = array()) {
		if(!empty($d['email'])) {
			$user = get_user_by('email', $d['email']);
			if(!empty($user) && is_object($user) && isset($user->ID)) {
				frame::_()->getModule('messenger')->getController()->sendNotification(
						$d['email'], 
						'user', 
						'reset_password_request', 
						$this->passwordRecoverSendLinkEmailVars($user->ID));
				return true;
			}
		}
		$this->pushError(lang::_('Invalid email address'), 'email');
		return false;
	}
	public function passwordRecoverSendLinkEmailVars($uid = 0) {
		$res = array();
		if($uid) {
			$user = $this->get($uid);
			if(!empty($user) && is_object($user) && isset($user->data)) {
				$res = array(
					'store_name' => frame::_()->getModule('options')->get('store_name'),
					'first_name' => @$user->data->first_name,
					'reset_link' => frame::_()->getModule('pages')->getLink(array(
						'mod' => 'user', 
						'action' => 'getPasswordRecoverConfirm', 
						'data' => array(
							'uid' => $uid,
							'hash' => md5(SECURE_AUTH_KEY. $uid),
						))),
				);
			}
		}
		return $res;
	}
}
?>