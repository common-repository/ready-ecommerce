<?php
/**
 * Class to handle user downloads methods
 */
class user_downloadsModel extends model {
    /**
     * Return the list of user downloads
     * 
     * @param int $user_id
     * @return array 
     */
    public function getUserDownloads($user_id) {
        return $this->get(array('user_id' => $user_id));
    }
    public function getOrderDownloads($order_id) {
        return $this->get(array('order_id' => $order_id));
    }
    public function get($d = array()) {
        if(empty($d)) return false; //Do not allow to show downloads for outsiders
        $user_files = frame::_()->getTable('user_files');
        $conditions = '';
        if(!empty($d['user_id']))
            $conditions .= $user_files->alias().'.uid ='. (int)$d['user_id']. ' AND ';
        if(!empty($d['order_id']))
            $conditions .= $user_files->alias().'.order_id ='. (int)$d['order_id']. ' AND ';

        $conditions .= $user_files->alias().'.downloads > 0 AND '.
                      '('. $user_files->alias().'.expires >= CURDATE() OR '. $user_files->alias().'.expires = "1970-01-01 00:00:00")';
        $product_files = frame::_()->getTable('product_files');
        $user_files->innerJoin($product_files,'fid');
        $items = $user_files->get("*,".$user_files->alias().".id as 'file_id'",$conditions);

        return $items;
    }
    /**
     * Insert information about files available for user
     * 
     * @param array $data
     * @return boolean 
     */
    public function post($data=array()){
        if (!empty($data)) {
			$data['downloads'] = isset($data['downloads']) ? (int)$data['downloads'] : 0;
			// If 0 was set when file was uploaded - this will mean that it can be uploaded infinity times
			if(empty($data['downloads'])) {
				$data['downloads'] = S_MYSQL_MAX_INT;
			}
            $id = frame::_()->getTable('user_files')->insert($data);
            if ($id){
                return $id;
            }
        }
		return false;
    }
}

