<?php
class single_productView extends view {
    public function display($instance) {
        $conditions = array('post_id' => $instance['product_id']);
        $productsTable = frame::_()->getTable('products');
        $productsTable->limit(1);
        $items = $productsTable->get('price, post_id, quantity', $conditions);
        $products = array();
        $small = false;
        if ($instance['image_view'] == 0) {
            $small = true;
        }
        if (!empty($items)) {
            $post = get_post($items[0]['post_id']);
            $product = array(
                'price' => frame::_()->getModule('products')->getView()->getPrice($item['post_id'], $items[0]['price']),
                'description' => $post->post_excerpt,
                'title' => $post->post_title,
                'image' => frame::_()->getModule('products')->getView()->getProductImage($post, $small),
                'images' => frame::_()->getModule('products')->getView()->getProductImages($post),
                'guid' => get_permalink($post),
                'actionButtons' => $this->addToCart($post, $instance),
                'ratingBox' => dispatcher::applyFilters('productRating', '', $post),
				'sku' => $post->sku,
				'post' => $post,
            );
        }
        $this->assign('imgWidth', frame::_()->getModule('options')->get('product_single_width'));
        $this->assign('product', $product);
        $this->assign('uniqID', mt_rand(1, 99999));
        $this->assign('params', $instance);
        parent::display('single');
    }
    public function displayForm($data, $widget) {
        $products = frame::_()->getModule('products')->getHelper()->getProducts();
        $this->assign('products', $products);
        $this->displayWidgetForm($data, $widget);
    }
	/**
	 * Generate add to cart button html code
	 * @param object $post - product post
	 * @param array $instance - params for widget instance
	 * @return string - html code for add to cart button
	 */
    public function addToCart($post, $instance) {
		// Ajax adding to cart
		$this->assign('instance', $instance);
		$this->assign('post', $post);
		return dispatcher::applyFilters('prodActionButons', parent::getContent('addToCartBtn'), $post->ID, $post);
        //$output = '<a href="'.uri::mod('user', '', 'addToCart', array('pid' => $post->ID)).'">'.lang::_('Add').'</a>';	//Old non-ajax way add to cart action
        //return $output;
    }
}
?>