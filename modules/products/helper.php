<?php
/**
 * optionsHelper class for field's elements
 */
class productsHelper extends toechelper {
    /**
     * Get all the products from database
     * @return array
     */
    public function getProducts($getFullInfo=false) {
        $products = array();
        $args = array(
            'post_type' => S_PRODUCT,
            'numberposts' => 99999,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $items = get_posts($args);
        if($getFullInfo){
			return $items;
		}	
        foreach ($items as $item) {
            $products[$item->ID] = $item->post_title;
        }
        return $products;
    }
    /**
     * Symple method to extract categories as an array
     * @return array keys == category ID, values == category name
     */
    public function getCategoriesList($d = array()) {
        static $cOptions = array();;
        if(empty($cOptions)) {
            $categories = frame::_()->getModule('products')->getCategories($d);
            if(!empty($categories)) {
                foreach($categories as $c) {
                    $cOptions[$c->term_taxonomy_id] = $c->cat_name;
                }
            }
        }
        return $cOptions;
    }
    /**
     * Symple method to extract brands as an array
     * @return array keys == brand ID, values == brand name
     */
    public function getBrandsList($d = array()) {
        static $bOptions = array();;
        if(empty($bOptions)) {
            $brands = frame::_()->getModule('products')->getBrands();
            if(!empty($brands)) {
                foreach($brands as $b) {
                    $bOptions[$b->term_taxonomy_id] = $b->cat_name;
                }
            }
        }
        return $bOptions;
    }
    /**
     * Create limit string for using in post_limits filter. This uses now for categories shortcodes
     * @param numeric $num number of posts that must be returned
     * @return string like 'LIMIT 0, $num'
     */
    static public function getPostLimitString($limit, $num = 0) {
        
    }
}