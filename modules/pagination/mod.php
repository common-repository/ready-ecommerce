<?php
/**
 * Use it in tamplate like:
 * 
 * <?php 
    if(class_exists('frame') && frame::_()->getModule('pagination')) {
        frame::_()->getModule('pagination')->getView()->display(array('nav_id' => 'nav-below', 'show' => array('navigation', 'perPage', 'ordering')));
    }?>
 * element 'show' can be empty - to display all pagination pannel
 */
class pagination extends module {
	/**
	 * Use sort by price in current posts query
	 */
	private $_usePriceSort = '';
    public function init() {
        parent::init();
        add_action('pre_get_posts', array($this, 'preGetPosts'));
        add_filter('posts_orderby_request', array($this, 'getPostsOrderBy'));
		add_filter('posts_results', array($this, 'orderPostsAfterExtract'));
    }
    public function preGetPosts($wpQuery) {
        $posts_per_page = req::getVar('posts_per_page');
        if($posts_per_page == 'all')    //Let's display all pages
            $posts_per_page = -1;
        $posts_per_page = (int) $posts_per_page;
        if($posts_per_page && empty($wpQuery->query_vars['posts_per_page'])) {
            $wpQuery->query_vars['posts_per_page'] = $posts_per_page;
        }
    }
    public function getPostsOrderBy($orderQuery) {
        $orderby = req::getVar('orderby');
        $order = req::getVar('order');
        //$allowedKeysOrderBy - allowed values for search, like: array('column_name' => 'table_name')
        $allowedKeysOrderBy = array('price' => frame::_()->getTable('products')->getTable(true));
        $allowedKeysOrder = array('ASC', 'DESC');
        if(!empty($orderby) && array_key_exists($orderby, $allowedKeysOrderBy) && !empty($order) && in_array(strtoupper($order), $allowedKeysOrder)) {
			if($orderby == 'price')
				$this->_usePriceSort = $order;
            $firstOrderQuery = $orderQuery;
            $orderQuery = $allowedKeysOrderBy[$orderby]. '.'. $orderby. ' '. $order;
            if(!empty($firstOrderQuery))
                $orderQuery .= ','. $firstOrderQuery;
        }
        return $orderQuery;
    }
	public function orderPostsAfterExtract($posts) {
		$orderby = req::getVar('orderby');
        $order = req::getVar('order');
		
		if(!empty($this->_usePriceSort)
			&& !empty($posts) 
			&& is_array($posts) 
			&& count($posts) > 1 
			&& frame::_()->getModule('special_products')
			&& $this->_allResultsIsProducts($posts)
		) {
			usort($posts, array($this, 'sortPostsByPriceCallback'));
		}
		return $posts;
	}
	/**
	 * Check that all posts in result - is products
	 */
	private function _allResultsIsProducts($posts) {
		foreach($posts as $p) {
			if($p && $p->post_type && $p->post_type != S_PRODUCT) {
				return false;
			}
		}
		return true;
	}
	public function sortPostsByPriceCallback($a, $b) {
		if(!empty($this->_usePriceSort)
			&& isset($a->post_type) 
			&& isset($b->post_type) 
			&& isset($a->price)
			&& isset($b->price)
			&& ($a->post_type == S_PRODUCT)
			&& ($b->post_type == S_PRODUCT)
		) {
			$priceA = frame::_()->getModule('products')->getPrice($a->ID, $a->price);
			$priceB = frame::_()->getModule('products')->getPrice($b->ID, $b->price);
			if($priceA > $priceB)
				return $this->_usePriceSort == 'ASC' ? 1 : -1;
			if($priceA < $priceB)
				return $this->_usePriceSort == 'ASC' ? -1 : 1;
		}
		return 0;
	}
}

?>
