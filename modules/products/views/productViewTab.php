<?php
/**
 * Class for product module tab at options page
 */
class productViewTabView extends view {
    /**
     * Get the content for product module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $options = $this->getProductViewOptions();
       $category_options = $this->getProductCategoryViewOptions();
       $this->assign('options', $options);
       $this->assign('category_options', $category_options);
       $output = parent::getContent('productViewTab');
       return $output;
   }
   /**
    * Return the array of single product view options
    * @return array 
    */
   public function getProductViewOptions() {
       $options = get_option('re_product_single');
       $default_options = $this->getDefaultProductView();
       foreach ($default_options as $key => $value) {
           if (!isset($options[$key])) {
               $options[$key] = $default_options[$key];
           }
       }
       return $options;
   }
   /**
    * Return the default settings for product view
    * @return array 
    */
   public function getDefaultProductView(){
       return $default_options = array(
           'full_image' => 1,
           'preview_images' => 1,
           'title' => 1,
           'price' => 1,
           'show_extra_fields' => 1,
           'sku' => 1,
           'details' => 1,
           'quantity' => 1,
           'show_twitter' => 1,
           'show_gplus' => 1,
           'show_facebook' => 1,
           'short_descr' => 1,
           'full_descr' => 1,
           'add_to_cart' => 1,
           'add_to_cart_text' => '#ffffff',
           'gallery_position' => 'left'
       );
   }
   /**
    * Return the array of category product view options
    * @return array 
    */
   public function getProductCategoryViewOptions() {
       $options = get_option('re_product_category');
       $default_options = $this->getDefaultProductCategoryView();
       foreach ($default_options as $key => $value) {
           if (!isset($options[$key])) {
               $options[$key] = $default_options[$key];
           }
       }
       return $options;  
   }
   /**
    * Return default product category view options
    * @return array
    */
   public function getDefaultProductCategoryView(){
       return $default_options = array(
           'catalog_view' => 'grid',
           
           'grid_preview_size'  => 176,
           'list_preview_size'  => 176,
           
           'grid_vert_distance' => 20,
           'list_vert_distance' => 20,
           'grid_hor_distance'  => 20,
           
           'shadow_border' => 1,
           'short_descr_size' => 3,
           
           'catalog_image' => 1,
           'title' => 1,
           'price' => 1,
           'more' => 1,
           'short_descr' => 1,
           'add_to_cart' => 1,
           
           'hover_item_bg' => '#ffffff',
           'short_descr_color' => '#9D9D9D',
           'title_color' => '#6f6f6f',
           'image_border_color' => '#e2e2e2',
           'price_color' => '#f58586'
       );
   }
   
   /**
    * Updating product view options
    */
   public function setThemeOptions($d = array(), $option_name) {
	   $changed = false;
       $options = get_option($option_name);
	   foreach ($d as $key => $value) {
           if (isset($options[$key])) {
               $options[$key] = $d[$key];
			   $changed = true;
           }
       }
	   if($changed)
		   update_option($option_name, $options);
   }
}

?>
