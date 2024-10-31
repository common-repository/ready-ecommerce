<?php

class downloadsModel extends model {
    public function getProductDownloads($post) {
        if (is_object($post)) {
            $post_id = $post->ID;
        } elseif (is_numeric($post)) {
            $post_id = $post;
        } else {
            return array();
        }
        $conditions = array(
            'pid' => $post_id,
            'type_id' => 1, //selling file type
        );
        $items = frame::_()->getTable('product_files')->get('*',$conditions);
        return $items;
    }
}
?>
