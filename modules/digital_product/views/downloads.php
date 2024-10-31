<?php

class downloadsView extends view {
    public function getAccountDownloads() {
        return $this->getDownloads(array('current_user' => 1));
    }
    public function getDownloads($d = array()) {
        if(empty($d)) return false; //Do not allow to show downloads for outsiders
        if(isset($d['current_user'])) {
            $user = wp_get_current_user();
            $items = frame::_()->getModule('digital_product')->getModel('user_downloads')->getUserDownloads($user->ID);
        } elseif(isset($d['user_id'])) {
            $items = frame::_()->getModule('digital_product')->getModel('user_downloads')->getUserDownloads($d['user_id']);
        } elseif(isset($d['order_id'])) {
            $items = frame::_()->getModule('digital_product')->getModel('user_downloads')->getOrderDownloads($d['order_id']);
        }
        $user_files = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $size = $this->formatBytes($item['size']);
                $product = get_post($item['pid']);
                $expire = strtotime($item['expires']);
                $expire = $expire ? date(S_DATE_FORMAT_HIS, $expire) : 0;
                $user_files[] = array(
                    'link' => admin_url('admin-ajax.php?action=digital_file_download&id='.$item['file_id'].'&token='.$item['token']),
                    'title' => $item['description'],
                    'expires' => $expire,
                    'downloads' => $item['downloads'],
                    'size' => $size,
                    'product' => $product->post_title,
					'file_id' => $item['file_id'],
                );
            }
        }
        $this->assign('user_files', $user_files);
        return $this->getContent('my_downloads');
    }
    
    /**
     * Formats the file size 
     * @param int $b
     * @param int $p
     * @return string 
     */
    private function formatBytes($b,$p = null) {
    /**
     *
     * @author Martin Sweeny
     * @version 2010.0617
     *
     * returns formatted number of bytes.
     * two parameters: the bytes and the precision (optional).
     * if no precision is set, function will determine clean
     * result automatically.
     *
     **/
    if ($b != 0){
        $units = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
        if(!$p && $p !== 0) {
            foreach($units as $k => $u) {
                if(($b / pow(1024,$k)) >= 1) {
                    $r["bytes"] = $b / pow(1024,$k);
                    $r["units"] = $u;
                }
            }
            return number_format($r["bytes"],2) . " " . $r["units"];
        } else {
            return number_format($b / pow(1024,$p)) . " " . $units[$p];
        }
    } else {
        return 0;
    }
}
}
?>
