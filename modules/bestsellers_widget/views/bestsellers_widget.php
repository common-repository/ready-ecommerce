<?php
class bestsellers_widgetView extends view {
    public function display($instance) {
        $oItemsTable = frame::_()->getTable('orders_items');
        $prodTable = frame::_()->getTable('products');
        $oItemsTable->innerJoin($prodTable, 'product_id');
        $oItemsTable->groupBy($oItemsTable->alias(). '.'. 'product_id');
        $oItemsTable->orderBy('COUNT('. $oItemsTable->alias(). '.'. 'product_id) DESC');
        $oItemsTable->limit($instance['number_of_products']);
        $items = $oItemsTable->get();
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
                        'actionButtons' => $this->addToCart($post),
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
    public function addToCart($post) {
		// Ajax adding to cart
		$this->assign('post', $post);
		return dispatcher::applyFilters('prodActionButons', parent::getContent('addToCartBtn'), $post->ID, $post);
        //$output = '<a href="'.uri::mod('user', '', 'addToCart', array('pid' => $post->ID)).'">'.lang::_('Add').'</a>';	//Old non-ajax way add to cart action
        //return $output;
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