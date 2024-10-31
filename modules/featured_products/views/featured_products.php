<?php
class featured_productsView extends view {
    public function display($instance) {
        global $wpdb;
        $conditions = array('featured' => 1);
        $productsTable = frame::_()->getTable('products');
        $productsTable->orderBy('id DESC');
        $productsTable->limit($instance['number_of_products']);
        
        $taxonomyIdCondition = array();
        if(!empty($instance['categories']))
            $taxonomyIdCondition = array_merge ($taxonomyIdCondition, $instance['categories']);
        if(!empty($instance['brands']))
            $taxonomyIdCondition = array_merge ($taxonomyIdCondition, $instance['brands']);
        if(!empty($taxonomyIdCondition)) {
            $productsTable->addJoin(array(
                'tbl' => $wpdb->term_relationships, 'a' => 'wp_tr', 'on' => 'object_id', 'joinOnID' => false, 'joinOn' => 'post_id'));
            $conditions['additionalCondition'] = ' wp_tr.term_taxonomy_id IN ('. implode(', ', $taxonomyIdCondition). ')';
        }
		$productsTable->groupBy('post_id');
		$productsTable->orderBy('sort_order');
        $items = $productsTable->get('price, post_id, quantity', $conditions);
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
        $brands = frame::_()->getModule('products')->getHelper()->getBrandsList();
        $this->assign('categories', frame::_()->getModule('products')->getHelper()->getCategoriesList());
        $this->assign('brands', frame::_()->getModule('products')->getHelper()->getBrandsList());
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