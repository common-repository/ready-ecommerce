<?php
class recent_products_widgetView extends view {
    public function display($instance) {
        global $wpdb;
		$instance['number_of_products'] = (int) $instance['number_of_products'];
		$query = array(
			'post_type' => S_PRODUCT, 
			'order' => 'DESC', 
			'orderby' => 'date');
		if(empty($instance['number_of_products']))
			$query['nopaging'] = 1;			// Show all products
		else
			$query['posts_per_page'] = $instance['number_of_products'];	// Show numbe r of products, that was entered in widget options
            $queryObj = new WP_Query($query);
            $posts = $queryObj->get_posts();
		foreach ($posts as $post) {
                    $product = array(
						'price' => frame::_()->getModule('products')->getView()->getPrice($post->ID, $post->price),
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
    protected function _extractPost($posts, $pid) {
        foreach($posts as $p) {
            if($p->ID == $pid)
                return $p;
        }
        return false;
    }
}
?>
