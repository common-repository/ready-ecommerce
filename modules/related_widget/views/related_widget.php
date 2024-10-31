<?php
class related_widgetView extends view {
    public function display($instance) {
		global $post;
		$idd = $post->ID;
		//var_dump($post->ID);
        $oItemsTable = frame::_()->getTable('related_widget');
        $prodTable = frame::_()->getTable('products');
        $oItemsTable->innerJoin($prodTable, 'rel_to');
        $oItemsTable->groupBy($oItemsTable->alias(). '.'. 'rel_to');
        $oItemsTable->orderBy('RAND()');
        $oItemsTable->limit($instance['number_of_products']);
		$where = $oItemsTable->alias().".rel_from = ".$idd;
        $items = $oItemsTable->get('*',$where,'','all','yoky');
		//var_dump($items);
        $products = array();
        if (!empty($items)) {
            $query = array('post__in' => array(), 'post_type' => S_PRODUCT, 'nopaging' => 1);
            foreach ($items as $item) {
                $query['post__in'][] = $item['post_id'];
            }
            $queryObj = new WP_Query($query);
            $posts = $queryObj->get_posts();
            foreach ($items as $item) {
                if($post = $this->_extractPost($posts, $item['post_id'])) {
                    $product = array(
                        'price' => frame::_()->getModule('products')->getView()->getPrice($item['post_id'], $item['price']),
                        'description' => $post->post_excerpt,
                        'title' => $post->post_title,
                        'image' => frame::_()->getModule('products')->getView()->getProductImage($post, true, false, 'all', 'catt'),
                        'productID' => $item['post_id'],
                        'guid' => get_permalink($post),
                        'productContentAfter' => dispatcher::applyFilters('productContentAfter', '', $post),
                        'ratingBox' => dispatcher::applyFilters('productRating', '', $post),
						'sku' => $post->sku,
						'post' => $post,
                    );
                    $products[] = $product;
                }
            }
        }
        $this->assign('products', $products);
        $this->assign('uniqID', mt_rand(1, 99999));
        $this->assign('params', $instance);
        parent::display('list');
    }
    public function displayForm($data, $widget) {
        $this->displayWidgetForm($data, $widget);
    }
    protected function _extractPost($posts, $pid) {
        foreach($posts as $p) {
            if($p->ID == $pid)
                return $p;
        }
        return false;
    }
}
?>