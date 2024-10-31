<?php

class subscribersModel extends model {
    /**
     * Return the object with fields
     * @param array $d
     * @return array 
     */
    public function get($d = array()) {
        parent::get($d);
        $fields = NULL;
        if(is_numeric($d['id'])) {
            if($d['id']) {
                frame::_()->getTable('subscribers')->fillFromDB($d['id']);
            }
            $fields = frame::_()->getTable('subscribers')->getFields();
            $fields['active']->addHtmlParam('checked', (bool)$fields['active']->value);
        } elseif(!empty($d) && is_array($d)) {
            $fields = frame::_()->getTable('subscribers')->get('*', $d);
        } else {
            $fields = frame::_()->getTable('subscribers')->getAll();
            if(!empty($fields)) {
                for($i = 0; $i < count($fields); $i++) {
                    $fields[$i]['data'] = $fields[$i]['data'];
                }
            }
        }
        return $fields;
    }
    /**
     * Create new Subscriber
     * @param array $d
     * @return response 
     */
    public function post($d = array()) {
        $res = new response();
		$res->error = 1;
		if(empty($d['email'])) {
			$res->pushError(lang::_('Please enter email'));
            return $res;
		}
        $subscriber = $this->getByEmail($d['email']);
        if (is_array($subscriber) && !empty($subscriber)) {
			$res->pushError(lang::_('You are already subscribed'));
            return $res;
        }
        $user = get_user_by_email($d['email']);
        if ($user) {
            $d['user_id'] = $user->ID;
            $d['name'] = $user->display_name;
        } else {
            $d['user_id'] = 0;
            $d['name'] = '';
        }
        $d['created'] = date("Y-m-d H:i:s");
        $d['active'] = 1;
        $d['token'] = md5($d['email'].rand(1000,5000));
        $id = frame::_()->getTable('subscribers')->insert($d);
        if($id) {
			$username = empty($d['name']) ? frame::_()->getModule('user')->getModel('user')->getUsernameByEmail($d) : $d['name'];
			wp_create_user($username, $d['token'], $d['email']);
            $res->setHtml(lang::_('Thank you for subscribing for our newsletters.'));
        } else
			$res->pushError(lang::_('Subscription Failed'));
        return $res;
    }
    
    /**
     * Return the subscriber by id
     * @param int $id
     * @return array
     */
    public function getById($id) {
        if (!is_numeric($id)) {
            die('Invalid Subscriber');
        }
        $conditions = array('id' => $id);
        $subscriber = frame::_()->getTable('subscribers')->get('*', $conditions);
        return $subscriber[0];
    }
    /**
     * Return the subscriber by email
     * @param string $email
     * @return array
     */
    public function getByEmail($email) {
        if ($email == '') {
            die('Invalid Subscriber');
        }
        $conditions = array('email' => $email);
        $subscriber = frame::_()->getTable('subscribers')->get('*', $conditions);
        return $subscriber[0];
    }
    /**
     * Return the subscriber by user_id
     * @param int $user_id
     * @return array
     */
    public function getByUserId($user_id) {
        if (!is_numeric($user_id)) {
            die('Invalid Subscriber');
        }
        $conditions = array('user_id' => $user_id);
        $subscriber = frame::_()->getTable('subscribers')->get('*', $conditions);
        return $subscriber[0];
    }
}
?>