<?php
class mostviewed_widgetView extends view {
    public function orderByProducts($orderby) {
        $orderby = frame::_()->getTable('products')->getTable(true). '.views DESC';
        return $orderby;
    }
    public function fieldsProducts($fields) {
        $fields .= ', '. frame::_()->getTable('products')->getTable(true). '.price';
        return $fields;
    }
    public function display($instance) {
        $instance['number_of_products'] = (int) $instance['number_of_products'];
        $query = array('post_type' => S_PRODUCT);
        if($instance['number_of_products']) {
            $query['posts_per_page'] = $instance['number_of_products'];
        } else {
            $query['nopaging'] = 1;
        }
        add_filter('posts_orderby_request', array($this, 'orderByProducts'));
        add_filter('posts_fields_request', array($this, 'fieldsProducts'));
        $queryObj = new WP_Query($query);
        $posts = $queryObj->get_posts();
        remove_filter('posts_orderby_request', array($this, 'orderByProducts'));
        remove_filter('posts_fields_request', array($this, 'fieldsProducts'));
        foreach ($posts as $post) {
            $product = array(
                'price' => frame::_()->getModule('products')->getView()->getPrice($item['post_id'], $post->price),
                'description' => $post->post_excerpt,
                'title' => $post->post_title,
                'image' => frame::_()->getModule('products')->getView()->getProductImage($post, true, false, 'all', 'catt'),
                'productID' => $post->ID,
                'guid' => get_permalink($post),
                'actionButtons' => $this->addToCart($post),
                'productContentAfter' => dispatcher::applyFilters('productContentAfter', '', $post),
                'ratingBox' => dispatcher::applyFilters('productRating', '', $post),
				'sku' => $post->sku,
				'post' => $post,
            );
            $products[] = $product;
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
}
?>