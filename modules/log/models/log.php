<?php
class logModel extends model {
    protected $_types = array(
        'email' => array('label' => 'Emails'),         //Let's it have an array as value to be able determine some params in future
		'order' => array('label' => 'Orders'),
    );
    public function post($d = array()) {
        if(frame::_()->getModule('options')->get('enable_log_actions')) {
            switch($d['type']) {
                case 'email':
                    if(is_array($d['data'])) {
                        foreach($d['data'] as $k => $v) {
                            $d['data'][$k] = stripslashes($d['data'][$k]);
                        }
                    }
                    $d['data'] = utils::serialize($d['data']);
                    break;
				case 'order':
					$d['data'] = stripslashes($d['data']);
					break;
            }
            $d['date_created'] = time();
			if(!isset($d['uid'])) {
				$d['uid'] = frame::_()->getModule('user')->getCurrentID();
			}
            return frame::_()->getTable('log')->insert($d);
        }
        return false;
    }
    public function get($d = array()) {
		global $wpdb;
		$logAl = frame::_()->getTable('log')->alias();
        frame::_()->getTable('log')->orderBy('id DESC');
		frame::_()->getTable('log')->arbitraryJoin('LEFT JOIN '. $wpdb->users. ' ON '. $wpdb->users. '.ID = '. $logAl. '.uid');
        $dataFromDb = frame::_()->getTable('log')->get($logAl. '.*, '. $wpdb->users. '.user_nicename', $d);
        if(!empty($dataFromDb)) {
            foreach($dataFromDb as $i => $d) {
                $dataFromDb[ $i ] = $this->_adaptFromDb( $dataFromDb[ $i ] );
            }
        }
        return $dataFromDb;
    }
    public function getSorted($d = array()) {
        $dataFromDb = $this->get($d);
        $sortedData = array();
        if(!empty($dataFromDb)) {
            foreach($dataFromDb as $d) {
                $sortedData[$d['type']][$d['id']]= $d;
            }
        }
        return $sortedData;
    }
    protected function _adaptFromDb($d = array()) {
        switch($d['type']) {
            case 'email':
                $d['data'] = utils::unserialize($d['data']);
                break;
        }
        return $d;
    }
    public function getTypes() {
        return $this->_types;
    }
}