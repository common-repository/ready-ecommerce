<?php
/**
 * Products Module Class
 * Here we have all the filters, hooks and actions for
 * product post type and custom product categories
 */
class products extends module {
    const CATEGORIES = 'products_categories';
    const BRANDS = 'products_brands';
       
    public function init() {
        global $wpdb;
        // add styles and scripts to product page
        add_action( 'admin_print_scripts-post.php', array($this, 'loadProductPageScripts'));
        add_action( 'admin_print_scripts-post-new.php', array($this, 'loadProductPageScripts'));
        add_action( 'admin_print_styles-post.php', array($this, 'loadProductPageStyles'));
        add_action( 'admin_print_styles-post-new.php', array($this, 'loadProductPageStyles'));
        add_action( 'admin_print_scripts-media-upload-popup', array($this, 'loadMediaPageScripts'));
        add_action( 'admin_init', array($this, 'productViewOptionsInit' ));
		add_filter( 'wp_redirect', array($this, 'productViewOptionsSaveRedirect' ), 99, 2 );	// Dissallow redirect after options saving via ajax request
        add_action( 'wp_head', array($this, 'productViewLoadStyle'));
        // register scripts, styles and image size for category image upload function
        $category_thumb_w = is_numeric(frame::_()->getModule('options')->get('product_preview_width')) ? 
			frame::_()->getModule('options')->get('product_preview_width') :
			220;
		$category_thumb_h = is_numeric(frame::_()->getModule('options')->get('product_preview_height')) ? 
			frame::_()->getModule('options')->get('product_preview_height') :
			220;
		$category_thumb_crop = (bool) frame::_()->getModule('options')->get('product_preview_crop'); 

		$product_display_w = is_numeric(frame::_()->getModule('options')->get('product_display_width')) ? 
			frame::_()->getModule('options')->get('product_display_width') :
			400;
		$product_display_h = is_numeric(frame::_()->getModule('options')->get('product_display_height')) ? 
			frame::_()->getModule('options')->get('product_display_height') :
			400;
		$product_display_crop = (bool) frame::_()->getModule('options')->get('product_display_crop');
		
		$product_small_w = is_numeric(frame::_()->getModule('options')->get('product_small_width')) ? 
			frame::_()->getModule('options')->get('product_small_width') :
			70;
		$product_small_h = is_numeric(frame::_()->getModule('options')->get('product_small_height')) ? 
			frame::_()->getModule('options')->get('product_small_height') :
			70;
		$product_small_crop = (bool) frame::_()->getModule('options')->get('product_small_crop');

        add_image_size('category-thumb', $category_thumb_w, $category_thumb_h, $category_thumb_crop );
		add_image_size('product-display', $product_display_w, $product_display_h, $product_display_crop );
        add_image_size('product-preview', $product_small_w, $product_small_h, $product_small_crop );
        add_filter('image_size_names_choose', array($this,'toe_registerCategoriesImageSize'));
        // actions for category list view
        add_action('products_categories_edit_form_fields', array($this,'toe_editProductsCategories'), 10, 2);
        add_action('edited_products_categories', array($this,'toe_saveProductsCategories'), 10, 2);
        add_action('products_brands_edit_form_fields', array($this,'toe_editProductsBrands'), 10, 2);
        add_action('edited_products_brands', array($this,'toe_saveProductsBrands'), 10, 2);
        add_filter('manage_products_categories_custom_column', array($this,'toe_productsCategoriesColumnRow'), 10, 3 );
        add_filter('manage_products_brands_custom_column', array($this,'toe_productsBrandsColumnRow'), 10, 3 );        
        add_filter('manage_edit-products_categories_columns',array($this,'toe_productCategoriesColumns'), 10, 1);        
        add_filter('manage_edit-products_brands_columns',array($this,'toe_productBrandsColumns'), 10, 1);        
        // register actions and filters for product add/edit page and for product list
        // register product post type
        add_action('init', array($this, 'addProductPostType'));
        // add columns to product list view
        add_action('manage_posts_custom_column' , array($this, 'toe_customColumns') );
        add_filter('manage_edit-product_columns', array($this, 'toe_productColumns'));
        add_filter('manage_edit-product_sortable_columns', array($this,'toe_sortableColumns'));
        // add category and brand filters for product list view
        add_action('restrict_manage_posts', array($this, 'toe_productFilters'));
        // change the titles of the metaboxes 
        add_action('do_meta_boxes', array($this,'toe_alterMetaBoxTitles'), 0, 3);
        add_filter('the_content', array($this, 'printProductContent'), 11);
        add_filter('enter_title_here', array($this,'toe_enterProductTitle'));
        // shortcodes for products
        //add_shortcode('product', array($this, 'addShortCodes'));
        // shortcodes for categories
        //add_shortcode('category', array($this, 'addShortCodesCategory'));
        // add products field to main posts query
        add_filter('posts_join_request', array($this, 'joinProductsTable'));
        add_filter('posts_fields_request', array($this, 'fieldsProductsTable'));
		add_filter('posts_orderby_request', array($this, 'orderProducts'));
        
        $wpdb->products_categoriesmeta = $wpdb->prefix."products_categoriesmeta";
        $wpdb->products_brandsmeta = $wpdb->prefix."products_brandsmeta";
        
        $this->getView('admin_Products')->addMetaBoxes();
        add_filter('wp_insert_post_data', array($this->getController(), 'savePostData'), 99, 2);
        add_action('wp_insert_post', array($this->getController(), 'saveProduct'), 99, 2);
            
		add_action('add_attachment', array($this, 'addContentImg'));
		
		add_action('pre_get_posts', array($this, 'addPossibilityOfTagUsage'));
		add_action('the_posts', array($this, 'addDefaultProductsOrder'));
		
		add_filter('media_upload_tabs', array($this, 'removeDefaultMediaGalleryTab'));
		// Filter attachments url - replace http for https for example if needed
		add_filter('wp_get_attachment_url', array($this, 'filterMediaUrls'));
		// Filter for replace permalink of variation post to original product post
		add_filter('post_type_link', array($this, 'filterVariationsPermalinks'), 99, 4);
		if(frame::_()->getModule('options')->get('show_subcategories_if_exist')) {
			add_filter('term_link', array($this, 'setLinkToSubCategories'), 10, 3);
		}
		if(frame::_()->getModule('options')->get('show_qty_coming_soon')) {
			dispatcher::addFilter('prodActionButons', array($this, 'setComingSoonInsteadBuy'), 5, 3);
		}
        parent::init();
    }
	public function setComingSoonInsteadBuy($content, $postId, $post = null) {
		$qty = false;
		if($post && is_object($post)) {
			$qty = $post->quantity;
		} else {
			static $selectedPosts = array();
			if(!isset($selectedPosts[ $postId ])) {
				$selectedPosts[ $postId ] = $this->getProductsPost($postId);
			}
			if(isset($selectedPosts[ $postId ]) && is_object($selectedPosts[ $postId ])) {
				$qty = $selectedPosts[ $postId ]->quantity;
			}
		}
		if($qty === false || !empty($qty)) {	// No post were found for this product, or qty is not empty - return usual content, we can't do nothing else
			return $content;
		}
		return $this->getController()->getView()->getComingSoonProductButtons( $selectedPosts[ $postId ] );		
	}
	public function setLinkToSubCategories($termlink, $term, $taxonomy) {
		static $childrens = array();
		if(in_array($taxonomy, array(self::CATEGORIES, self::BRANDS))) {
			$keyForChildrens = $term->term_id. '_'. $taxonomy;
			if(!isset($childrens[ $keyForChildrens ])) {
				$childrens[ $keyForChildrens ] = get_term_children( $term->term_id, $taxonomy );
			}
			// Category have subcategories - replace link to the link of categories page
			if(!empty($childrens[ $keyForChildrens ])) {
				$pageAction = $taxonomy == self::CATEGORIES ? 'getCategoriesListHtml' : 'getBrandsListHtml';
				$termlink = frame::_()->getModule('pages')->getLink(array('mod' => 'products', 'action' => $pageAction, 'data' => array('parent_id' => $term->term_id)));
			}
			/*echo '<pre>';
			var_dump($childrens[ $keyForChildrens ]);
			echo '</pre>';*/
		}
		return $termlink;
	}
	public function filterVariationsPermalinks($post_link, $post, $leavename, $sample) {
		if($post && is_object($post) && $post->post_type == S_VARIATION) {
			$post_link = get_post_permalink($post->post_parent, $leavename, $sample);
			$post_link .= '#toeVariation_'. $post->ID;
		}
		return $post_link;
	}
	public function filterMediaUrls($url) {
		return uri::_($url);
	}
	/**
	 * Do not show Library tab at gallery for products
	 */
	public function removeDefaultMediaGalleryTab($tabs) {
		$postId = (int) req::getVar('post_id');
		$toeProdGalery = (int) req::getVar('toeProdGalery');
		if($postId && $toeProdGalery && get_post_type($postId) == S_PRODUCT) {
			unset($tabs['library']);
		}
		return $tabs;
	}
	
	public function orderProducts($orderby) {
		global $wp_query;
		$needSort = true;
		// If ordering is set in query variables
		if(is_object($wp_query) && isset($wp_query->query_vars) && isset($wp_query->query_vars['orderby']) && !empty($wp_query->query_vars['orderby'])) {
			$needSort = false;
		} else {
			$oldOrderby = $orderby;
			$pName = frame::_()->getTable('products')->getTable(true);
			$orderby = $pName. '.sort_order';
			if(!empty($oldOrderby))
				$orderby .= ', '. $oldOrderby;
		}
		return $orderby;
	}
	/**
	 * Allow to search in any post type if tag selected.
	 * This is because wp search only for post type "post" when tag is selected
	 */
	public function addPossibilityOfTagUsage($wpQuery) {
		if($wpQuery->is_tag && !$qObj->is_tax)
			$wpQuery->query_vars['post_type'] = 'any';
	}
	public function addDefaultProductsOrder($posts) {
		global $wp_query, $post;
		if(!empty($posts) && is_array($posts)) {
			$needSort = true;
			// If ordering is set in query variables
			if(is_object($wp_query) && isset($wp_query->query_vars) && isset($wp_query->query_vars['orderby']) && !empty($wp_query->query_vars['orderby'])) {
				$needSort = false;
			} else {	// Else let's check if there are at least one not-product post in results
				foreach($posts as $p) {
					if($p->post_type != S_PRODUCT) {
						$needSort = false;
						break;
					}
				}
			}
			if($needSort) {
				// Sort by sort_order field in ASK mode
				usort($posts, array($this, 'sortProductsCallback'));
			}
		}
		return $posts;
	}
	public function sortProductsCallback($prodA, $prodB) {
		if(is_object($prodA)
			&& is_object($prodB)
			&& isset($prodA->sort_order)
			&& isset($prodB->sort_order)
			&& ($prodA->sort_order != $prodB->sort_order)
		) {
			return ((int)$prodA->sort_order > (int)$prodB->sort_order ? 1 : -1);
		}
		return 0;
	}
    public function joinProductsTable($join) {
        global $wpdb;
        $pName = frame::_()->getTable('products')->getTable(true);
        $join .= ' LEFT JOIN '. $pName. ' ON '. $pName. '.post_id = '. $wpdb->posts. '.ID';
        return $join;
    }
    public function fieldsProductsTable($fields) {
		global $wpdb;
        if(!empty($fields))
            $fields .= ', ';
        $pName = frame::_()->getTable('products')->getTable(true);
        $useColumns = array(
			$pName. '.price', 
			$pName. '.weight', 
			$pName. '.sku',
			$pName. '.quantity',
			$pName. '.featured',
			$pName. '.mark_as_new',
			$pName. '.sort_order',
			'(SELECT id FROM '. frame::_()->getTable('extrafieldsvalue')->getTable(true). ' WHERE parent_id = '. $wpdb->posts. '.ID AND parent_type = "'. S_PRODUCT. '" AND price != 0 LIMIT 1) AS toePriceOptExist',
		);
        $addFields = array();
        foreach($useColumns as $c) {
            $addFields[] = $c;
        }
        $fields .= implode(', ', $addFields);
        return $fields;
    }
    public function joinProductsImages($join) {
        global $wpdb;
        $imgStatusName = frame::_()->getTable('img_status')->getTable(true);
        $join .= ' LEFT JOIN '. $imgStatusName. ' ON '. $imgStatusName. '.post_id = '. $wpdb->posts. '.ID';
        return $join;
    }
    public function whereCattProductsImages($where) {
        global $wpdb;
        $imgStatusName = frame::_()->getTable('img_status')->getTable(true);
        $where .= ' AND IF(
                            EXISTS(SELECT '. $imgStatusName. '.id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.parent_id = '. $wpdb->posts. '.post_parent AND '. $imgStatusName. '.status IN ("catt", "catt_only") LIMIT 1),
                            '. $wpdb->posts. '.ID IN (SELECT '. $imgStatusName. '.post_id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.parent_id = '. $wpdb->posts. '.post_parent AND '. $imgStatusName. '.status IN ("catt", "catt_only")),
                            1 = 1)';
        
        return $where;
    }
	public function whereCattOnlyProductsImages($where) {
        global $wpdb;
        $imgStatusName = frame::_()->getTable('img_status')->getTable(true);
        $where .= ' AND IF(
                            EXISTS(SELECT '. $imgStatusName. '.id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.parent_id = '. $wpdb->posts. '.post_parent AND '. $imgStatusName. '.status IN ("catt_only") LIMIT 1),
                            '. $wpdb->posts. '.ID IN (SELECT '. $imgStatusName. '.post_id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.parent_id = '. $wpdb->posts. '.post_parent AND '. $imgStatusName. '.status IN ("catt_only")),
                            1 = 1)';
        
        return $where;
    }
    public function whereDescProductsImages($where) {
        global $wpdb;
        $imgStatusName = frame::_()->getTable('img_status')->getTable(true);
        $where .= ' AND IF(
                            EXISTS(SELECT '. $imgStatusName. '.id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.post_id = '. $wpdb->posts. '.ID AND '. $imgStatusName. '.status IN ("catt_only") LIMIT 1),
                            '. $wpdb->posts. '.ID NOT IN (SELECT '. $imgStatusName. '.post_id FROM '. $imgStatusName. ' WHERE '. $imgStatusName. '.post_id = '. $wpdb->posts. '.ID AND '. $imgStatusName. '.status IN ("catt_only")),
                            1 = 1)';
        return $where;
    }
	public function whereProductsImages($where) {
		global $wpdb;
		$contentImgName = frame::_()->getTable('content_img')->getTable(true);
		$where .= ' AND !EXISTS(SELECT img_id FROM '. $contentImgName. ' WHERE '. $contentImgName. '.img_id = '. $wpdb->posts. '.ID)';
		return $where;
	}
    public function markAsSale($pid) {
        $specials = NULL;
        $res = false;
        if(frame::_()->getModule('special_products'))
            $specials = frame::_()->getModule('special_products')->getSpecialByPid($pid);
        if($specials) {
            foreach($specials as $s) {
                if((int) $s['mark_as_sale']) {
                    $res = true;
                    break;
                }
            }
        }
        return $res;
    }
	public function addContentImg($pid) {
		$pid = (int) $pid;
		if(!$pid)
			return;
		$imgPost = get_post($pid);
		if(!empty($imgPost) && strpos($_SERVER['HTTP_REFERER'], 'toeProdGalery=1') === false) {
			frame::_()->getTable('content_img')->insert(array('img_id' => $pid, 'pid' => $imgPost->post_parent));
		}
	}
    public function loadMediaPageScripts(){
       $get = req::get('get');
       if (!is_numeric($get['post_id'])) {
           return;
       }
	   if(!isset($get['toeProdGalery']) || empty($get['toeProdGalery'])) {	//Use those scripts only for our products galery, not for usual content images manipulations
		   return;
	   }
       $post_id = $get['post_id'];
       $type = get_post_type($post_id);
       if (S_PRODUCT == $type){
           wp_enqueue_script('jquery-form');
           frame::_()->addScript('productPage', S_JS_PATH.'productMediaPage.js');
           frame::_()->addStyle('lightboxCss', S_CSS_PATH. 'lightbox.css');
           frame::_()->addScript('lightbox', S_JS_PATH. 'lightbox.js');
       }
    }
    /**
     * Load Product page scripts
     * @global type $typenow 
     */
    function loadProductPageScripts(){
        if (S_PRODUCT == get_post_type()){
            frame::_()->addScript('productPage', S_JS_PATH.'productPage.js');
            $lang = array(
                'manage_categories' => lang::_('Manage Categories'),
                'manage_brands' => lang::_('Manage Brands'),
                'allow_reviews' => lang::_('Allow Reviews'),
            );
            $links = array(
                'manage_categories' => admin_url('edit-tags.php?taxonomy=products_categories'),
                'manage_brands' => admin_url('edit-tags.php?taxonomy=products_brands'),
            );
            frame::_()->addJSVar('productPage', 'TOE_PRODUCT_LANG', $lang);
            frame::_()->addJSVar('productPage', 'TOE_LINKS', $links);
        }
    }
    /**
     * Load Product Page Styles
     */
    function loadProductPageStyles(){ 
        if (S_PRODUCT == get_post_type()){
            wp_enqueue_style('productPageCss', S_CSS_PATH.'productPage.css');
            //frame::_()->addStyle('productPageCss', S_CSS_PATH.'productPage.css');
        }
    }
    
    /**
     * Function to display product front-end
     * 
     * @param string $content
     * @return string 
     */
    public function printProductContent($content) {
		$postType = get_post_type();
        if(in_array($postType, array(S_PRODUCT, S_VARIATION))) {	// For product and products variations - load plugin content
            if (is_single()) {
                $content = $this->getController()->getView('products')->getProductContent(array('originalContent' => $content));
            } else {
                $content = $this->getController()->getView('products')->getProductContent(array('originalContent' => $content, 'category_view' => 1));
            }
		}
        return $content;
    }
    /**
     * Gets the list of product categories
     * @param array $d
     * @return array 
     */
    public function getCategories($d = array()) {
		global $wpdb;
        if(isset($d['pid'])) {
            $categories = wp_get_post_terms($d['pid'], self::CATEGORIES);
        } else {
			$opts = array(
                'taxonomy' => self::CATEGORIES,
                'hide_empty' => 0,
                'pad_counts' => true);
			if(is_array($d) && !empty($d))
				$opts = array_merge($opts, $d);
            $categories = get_categories($opts);
        }
		if(!empty($categories)) {
			if(isset($d['orderByParents'])) {
				usort($categories, array($this, 'sortObjectsByParents'));
			} else {
				$cids = array();
				foreach($categories as $c) {
					$cids[] = $c->term_id;
				}
				if(!empty($cids)) {
					$sortOrderFromDb = db::get('SELECT * FROM '. $wpdb->products_categoriesmeta. ' 
						WHERE products_categories_id IN ('. implode(', ', $cids). ') AND meta_key = "sort_order"');
					if(!empty($sortOrderFromDb)) {
						$sortOrder = array();
						foreach($sortOrderFromDb as $sort) {
							$sortOrder[ $sort['products_categories_id'] ] = $sort;
						}
						foreach($categories as $i => $c) {
							if(isset($sortOrder[ $c->term_id ])) {
								$categories[ $i ]->sort_order = (int) $sortOrder[ $c->term_id ]['meta_value'];
							} else {
								$categories[ $i ]->sort_order = 0;
							}
						}
						usort($categories, array($this, 'sortObjectsBySortOrder'));
					}
				}
			}
		}
		return $categories;
    }
	public function sortObjectsByParents($a, $b) {
		if($a->parent > $b->parent)
			return 1;
		if($a->parent < $b->parent)
			return -1;
		return 0;
	}
	public function sortObjectsBySortOrder($a, $b) {
		if(!isset($a->sort_order))
			$a->sort_order = 0;
		if(!isset($b->sort_order))
			$b->sort_order = 0;
		if($a->sort_order < $b->sort_order)
			return -1;
		if($a->sort_order > $b->sort_order)
			return 1;
		return 0;
	}
    public function getProductsPosts($d = array()) {
        $d['post_type'] = S_PRODUCT;
        $d['suppress_filters'] = false;
        if(!isset($d['numberposts']))
            $d['numberposts'] = -1;
        return get_posts($d);
    }
	public function getProductsPost($pid) {
        return get_post($pid);
    }
    /**
     * Gets the list of product brands
     * 
     * @param array $d
     * @return array 
     */
    public function getBrands($d = array()) {
        if(isset($d['pid'])) {
            return wp_get_post_terms($d['pid'], self::BRANDS);
        } else {
			$opts = array(
                'taxonomy' => self::BRANDS,
				'hide_empty' => 0,
				'pad_counts' => true);
			if(is_array($d) && !empty($d))
				$opts = array_merge($opts, $d);
			return get_categories($opts);
		}
    }
    /**
     * Registering product post type
     */
    public function addProductPostType() {
         $labels = array(
            'name' => lang::_('Products'),
            'singular_name' => lang::_('Product'),
            'add_new' => lang::_('Add New'),
            'add_new_item' => lang::_(array('Add New', 'Product')),
            'edit_item' => lang::_(array('Edit', 'Product')),
            'new_item' => lang::_(array('New', 'Product')),
            'all_items' => lang::_('Products'),
            'view_item' => lang::_(array('View', 'Product')),
            'search_items' => lang::_(array('Search', 'Products')),
            'not_found' =>  lang::_('No Products Found'),
            'not_found_in_trash' => lang::_(array('No Products Found', 'In Trash')), 
            'parent_item_colon' => '',
            'menu_name' => lang::_('Products')
        );
        register_post_type(S_PRODUCT, array(
                'labels' => $labels,
                'description' => lang::_('Products post type'),
                'show_ui' => true,
                'supports' => array(
                        'title', 
                        'editor',
                        //'author',
                        'thumbnail', 
                        'excerpt',
                        //'trackbacks',
                        'custom-fields',
                        'comments' ,
                        'revisions' ,
                        //'page-attributes' ,
                        //'post-formats'
                     ),
                'public' => true,
                'show_in_menu' => false,    //Let's show this menu item manualy in adminmenu mod
                //'show_in_menu' => frame::_()->getModule('adminmenu')->getController()->getView('adminmenu')->getFile(),
                'exclude_from_search' => false,
                'rewrite' => array('slug' => S_PRODUCT),
                'taxonomies' => array('post_tag'),
             )
        );
        $labels = array(
            'name' => lang::_('Product Categories'),
            'singular_name' => lang::_('Product Category'),
            'search_items' => lang::_(array('Search', 'Product Categories')),
            'all_items' => lang::_('All Product Categories'),
            'parent_item' => lang::_('Product Parent Category'),
            'parent_item_colon' => lang::_(array('Product Parent Category', ':')),
            'edit_item' => lang::_(array('Edit', 'Product Category')), 
            'update_item' => lang::_(array('Update', 'Product Category')),
            'add_new_item' => lang::_(array('Add New', 'Product Category')),
            'new_item_name' => lang::_('New Product Category Name'),
            'menu_name' => lang::_('Product Categories'),
        ); 	
        // registering product categories
        register_taxonomy(self::CATEGORIES, S_PRODUCT, array(
            'hierarchical' => true,
            'labels' => $labels,
            'label' => lang::_('Product Categories'),
            'show_ui' => true,
            'query_var' => true,
            'show_in_nav_menus' => true,
            'rewrite' => array('slug' => self::CATEGORIES),
        ));
        
        $labels = array(
            'name' => lang::_('Brands'),
            'singular_name' => lang::_('Brand'),
            'search_items' => lang::_(array('Search', 'Brands')),
            'all_items' => lang::_('All Brands'),
            'edit_item' => lang::_(array('Edit', 'Brand')), 
            'update_item' => lang::_(array('Update', 'Brand')),
            'add_new_item' => lang::_(array('Add New', 'Brand')),
            'new_item_name' => lang::_('New Brand Name'),
            'menu_name' => lang::_('Brands'),
        );
        // registering product brands
        register_taxonomy(self::BRANDS, S_PRODUCT, array(
            'hierarchical' => true,
            'labels' => $labels,
            'label' => lang::_('Product Brands'),
            'show_ui' => true,
            'query_var' => true,
            'show_in_nav_menus' => true,
            'rewrite' => array('slug' => self::BRANDS),
        ));
        register_taxonomy_for_object_type('products_categories', S_PRODUCT);
        register_taxonomy_for_object_type('products_brands', S_PRODUCT);
    }
    /**
     * Add custom columns to the product view
     * 
     * @param array $columns
     * @return array 
     */
    public function toe_productColumns($columns) {
        return  array('cb' => '<input type="checkbox" />',
                      'title' => lang::_('Name'),
                      'description' => lang::_('Description'),
                      'price' => lang::_('Price'),
                      //'cost' => lang::_('Cost'),
                      'quantity' => lang::_('Quant.'),
                      'thumb' =>lang::_('Image'),
                      'product_categories' =>lang::_('Categories'),
                      //'tags' =>lang::_('Tags'),
					  'sort_order' =>lang::_('Sort Order'). '&nbsp;'. html::button(array('value' => lang::_('Save'), 'attrs' => 'onclick="toeSaveProductsSortOrder(this); return false;" class="button-secondary"')). '<span class="toeProductsSortOrderMsg"></span>',
                      'date' =>lang::_('Date'),
                      'comments' =>lang::_('Reviews'),
					  
                );
    }
    /**
     * Fill in the custom columns with values
     * 
     * @global object $post
     * @param array $column 
     */
    public function toe_customColumns( $column ) {
        global $post;
        $fields = frame::_()->getModule('products')->getController()->getModel('products')->get($post);
        switch ( $column ) {
			case 'description':
				if(!empty($post) && isset($post->post_excerpt))
					echo $post->post_excerpt;
				//echo get_the_excerpt();	// very sad bag.....
			break;
			case 'price':
				echo $fields['price']->value;
				break;
			/* case 'cost':
				echo $fields['cost']->value;
				break;*/
			case 'quantity':
				echo $fields['quantity']->value;
				break;
			case 'thumb':
				$src = frame::_()->getModule('products')->getController()->getView('products')->getProductImage($post, false, false, 'thumb', 'catt_only', 'category-thumb');
				echo '<img 
					src="'.$src['thumb'][0].'" alt="'.$post->post_title.'" 
					style="
						max-width: '. frame::_()->getModule('options')->get('product_small_width'). 'px; 
						max-height: '. frame::_()->getModule('options')->get('product_small_height'). 'px"/>';
				break;
			case 'product_categories':
				$_taxonomy = 'products_categories';
				$categories = get_the_terms( $post->ID, $_taxonomy );
				if ( !empty( $categories ) ) {
					$out = array();
					foreach ( $categories as $c )
						$out[] = "<a href='edit.php?product_category=$c->slug&post_type=product'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
					echo join( ', ', $out );
				}
				else {
					lang::_e('Uncategorized');
				}
			   break;
			case 'sort_order':
				echo html::text('sort_order['. $post->ID. ']', array('value' => $post->sort_order, 'attrs' => 'size="10" class="toeProdSortOrderText"'));
				break;
		}
    }
    /**
     * Make these columns sortable
     * @return array
     */
    public function toe_sortableColumns() {
      return array(
        'title'    => 'title',
        'date'     => 'date',
        'price'    => 'price',
        //'cost'     => 'cost',
        'quantity' => 'quantity',
        'comments' => 'comments',
      );
    }
    /**
     * Adding categories filter to the product list
     * 
     * @global type $typenow 
     */
    public function toe_productFilters() {
        global $typenow;
        $filters = array(self::CATEGORIES, self::BRANDS);
        if( $typenow == S_PRODUCT){
                foreach ($filters as $tax_slug) {
                        $tax_obj = get_taxonomy($tax_slug);
                        $tax_name = $tax_obj->labels->name;
                        $terms = get_terms($tax_slug);
                        echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
                        echo "<option value=''>Show All $tax_name</option>";
                        foreach ($terms as $term) { 
                            echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>
                                ' . $term->name .' (' . $term->count .')
                                </option>'; 
                            
                            }
                        echo "</select>";
                }
        }
    }
    /**
     * Changes "Enter title here" text at add product page
     * @param string $title
     * @return string 
     */
    public function toe_enterProductTitle($title){
         $screen = get_current_screen();
 
         if  ( S_PRODUCT == $screen->post_type ) {
              $title = lang::_('Enter Product Name');
         }

         return $title;
    }
    /**
     * Change meta boxes titles
     * 
     * @global string $wp_meta_boxes
     * @param string $post_type
     * @param type $priority
     * @param object $post
     * @return string 
     */
    public function toe_alterMetaBoxTitles($post_type, $priority, $post){
        global $wp_meta_boxes;
        if ($post_type == S_PRODUCT) {
            $wp_meta_boxes[S_PRODUCT]['normal']['core']['postexcerpt']['title'] = lang::_('Short Description');
            $wp_meta_boxes[S_PRODUCT]['normal']['core']['commentstatusdiv']['title'] = lang::_('Review Configuration');
            if(!empty($wp_meta_boxes[S_PRODUCT]['normal']['core']['commentsdiv']))
                $wp_meta_boxes[S_PRODUCT]['normal']['core']['commentsdiv']['title'] = lang::_('Reviews');
            $wp_meta_boxes[S_PRODUCT]['normal']['core']['slugdiv']['title'] = lang::_('Product Friendly URL');
        }
        return $wp_meta_boxes;
    }
    /**
     * Adds new resize template
     * 
     * @param array $sizes
     * @return array 
     */
    public function toe_registerCategoriesImageSize($sizes) {
        $addsizes = array(
            'category-thumb' => lang::_('Product Category Icon'),
			'product-display' => lang::_('Product Big Image'),
			'product-preview' => lang::_('Product Preview Image'),
        );
        $newsizes = array_merge($sizes, $addsizes);
        return $newsizes;
    }
    
    /**
     * Adds meta fields to custom taxonomy product categories
     * 
     * @param object $tag
     * @param string $taxonomy 
     */
    public function toe_editProductsCategories($tag, $taxonomy){
		echo $this->getController()->getView('admin_Products')->editProductsCategories($tag, $taxonomy);
    }
    private function createMenuFromTerm($term_id, $parent_id, $taxonomy, $menu_id) {
        $term = get_term($term_id, $taxonomy);
        $itemData = array(
            'menu-item-object-id' => $term->term_id,
            'menu-item-object' => $taxonomy,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $parent_id,
            'menu-item-attr-title' => $term->name,
            'menu-item-description' => $term->description,
            'menu-item-title' => $term->name,
            'menu-item-target' => '',
            'menu-item-classes' => 'termid-'.$term->term_id.' parentid-'.$parent_id,
            'menu-item-xfn' => '',
        );
        if ($parent_id) {
            $itemData['menu-item-menu-item-parent'] = $parent_id;
        }
        $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $itemData);
        return $menu_item_id;
    }
	private function createMenusRecursively($termId, $parent_id, $taxonomy, $menuId) {
		$childrenTaxonomies = $this->getCategories(array('parent' => $termId));
		$newItemId = $this->createMenuFromTerm($termId, $parent_id, $taxonomy, $menuId);
		if (!empty($childrenTaxonomies)) {
			foreach ($childrenTaxonomies as $tag) {
				if(is_object($tag)) {
					$this->createMenusRecursively($tag->term_id, $newItemId, $taxonomy, $menuId);
				}
			}
		}
		return $newItemId;
	}
    /**
     * Save custom product categories fields
     * 
     * @param type $term_id
     * @param type $tt_id
     * @return type 
     */
    public function toe_saveProductsCategories($term_id, $tt_id = 0, $d = array()){
        if (!$term_id) return;
		if(empty($d))
			$d = $_POST;
        if (isset($d['product_category_thumb_url'])) {
            if ($d['product_category_thumb_url']!='') {
                update_metadata($d['taxonomy'], $term_id, 'category_thumb',$d['product_category_thumb_url']);
            } else {
                delete_metadata($d['taxonomy'], $term_id, 'category_thumb');
            }
        }
		if (isset($d['sort_order'])) {
            if ($d['sort_order']!='') {
                update_metadata($d['taxonomy'], $term_id, 'sort_order',$d['sort_order']);
            } else {
                delete_metadata($d['taxonomy'], $term_id, 'sort_order');
            }
        }
        if (isset($d['product_category_menu']) && is_numeric($d['product_category_menu'])) {
            $menu_id = 0;
            if (isset($d['choose_menu']) && is_numeric($d['choose_menu'])) {
                $menu_id = $d['choose_menu'];
            }
            if (!$menu_id) return;
            $menu_type = $d['product_category_menu'];
			$newItemId = 0;
            switch ($menu_type) {
                case 0:
                    $newItemId = $this->createMenuFromTerm($term_id, 0, self::CATEGORIES, $menu_id);
                    break;
                case 1:
					$newItemId = $this->createMenusRecursively($term_id, 0, self::CATEGORIES, $menu_id);
                    /*$childrenTaxonomies = get_term_children($term_id, self::CATEGORIES);
                    $parent_id = $this->createMenuFromTerm($term_id, 0, self::CATEGORIES, $menu_id);
                    if (!empty($childrenTaxonomies)) {
                        foreach ($childrenTaxonomies as $key=>$value) {
                            $this->createMenuFromTerm($value, $parent_id, self::CATEGORIES, $menu_id);
                        }
					}*/
                    break;
            }
            update_metadata($d['taxonomy'], $term_id, 'category_menu', array('menuName' => $d['name'], 'menuItemId' => $newItemId));
        }
		//delete_metadata($_POST['taxonomy'], $term_id, 'category_menu');
    }
    
    /**
     * Add custom columns to product categories list
     * 
     * @param array $columns 
     */
    public function toe_productCategoriesColumns($columns) {
         $columns_local = array();
         $columns_local['category_thumb'] = lang::_('Icon');
		 $columns_local['category_id'] = lang::_('ID');
         if (isset($columns['posts']))
                $columns['posts'] = lang::_('Products');
         return array_merge($columns,$columns_local);
    }
    /**
     * Returns the category image
     * 
     * @param type $row_content
     * @param type $column_name
     * @param type $term_id 
     */
    public function toe_productsCategoriesColumnRow( $row_content, $column_name, $term_id ){
        switch($column_name)
        {
            case 'category_thumb':
                $category_thumb = get_metadata('products_categories', $term_id, 'category_thumb', true);
                if ($category_thumb!='')
                    $row_content .= '<img src="'.$category_thumb.'" />';
                break;
			case 'category_id':
				$row_content .= $term_id;
				break;
            default:
                break;
        }
        return $row_content;
    }

    /**
     * Adds meta fields to custom taxonomy brands categories
     * 
     * @param object $tag
     * @param string $taxonomy 
     */
    public function toe_editProductsBrands($tag, $taxonomy){
		echo $this->getController()->getView('admin_Products')->editProductsBrands($tag, $taxonomy);
    }
    
    /**
     * Save custom product categories fields
     * 
     * @param type $term_id
     * @param type $tt_id
     * @return type 
     */
    public function toe_saveProductsBrands($term_id, $tt_id = 0, $d = array()){
        if (!$term_id) return;
		if(empty($d))
			$d = $_POST;
        if (isset($d['product_brand_thumb_url'])) {
            if ($d['product_brand_thumb_url']!='') {
                update_metadata($d['taxonomy'], $term_id, 'brand_thumb',$d['product_brand_thumb_url']);
            } else {
                delete_metadata($d['taxonomy'], $term_id, 'brand_thumb');
            }
        }
        if (isset($d['brand_url'])) {
            if ($d['brand_url']!='') {
                update_metadata($d['taxonomy'], $term_id, 'brand_url',$d['brand_url']);
            } else {
                delete_metadata($d['taxonomy'], $term_id, 'brand_url');
            }  
        }
    }
    
    /**
     * Add custom columns to product categories list
     * 
     * @param array $columns 
     */
    public function toe_productBrandsColumns($columns) {
         $columns_local = array();
         $columns_local['brand_thumb'] = lang::_('Icon');
		 $columns_local['brand_id'] = lang::_('ID');
         if (isset($columns['posts']))
                $columns['posts'] = lang::_('Products');
         return array_merge($columns,$columns_local);
    }
    /**
     * Returns the category image
     * 
     * @param type $row_content
     * @param type $column_name
     * @param type $term_id 
     */
    public function toe_productsBrandsColumnRow( $row_content, $column_name, $term_id ){
        switch($column_name)
        {
            case 'brand_thumb':
                $brand_thumb = get_metadata('products_brands', $term_id, 'brand_thumb', true);
                if ($brand_thumb!='')
                    $row_content .= '<img width="120px" src="'.$brand_thumb.'" />';
                break;
			case 'brand_id':
				$row_content .= $term_id;
				break;
            default:
                break;
        }
        return $row_content;
    }
    /**
     * Return link to product
     * 
     * @param numeric $pid ID of a product (post)
     * @return string link to product
     */
    public function getLink($pid) {
        return get_permalink($pid);
    }
    /**
     * Return link to category
     * 
     * @param numeric $cid ID of a Category
     * @return string link to category
     */
    public function getLinkToCategory($tax, $slug = '') {
		if(is_numeric($tax) && empty($slug)) {
			$tax = get_term_by('term_id', $tax, self::CATEGORIES);
		}
        return  get_term_link($tax, $slug);
    }
	 /**
     * Return link to brand
     * 
     * @param numeric $cid ID of a brand
     * @return string link to brand
     */
    public function getLinkToBrand($tax, $slug = '') {
		if(is_numeric($tax) && empty($slug)) {
			$tax = get_term_by('term_id', $tax, self::BRANDS);
		}
        return  get_term_link($tax, $slug);
    }
    /**
     * Return the url of the category image
     * @param type $term_id
     * @return string 
     */
    public function getCategoryImage($term) {
		$category_thumb = '';
		if (is_object($term)) {
			$term_id = $term->term_id;
		} elseif (is_int($term)) {
			$term_id = $term;
		} else {
			$category_thumb = lang::_('No image');
		}
		if(empty($category_thumb))
			$category_thumb = get_metadata(self::CATEGORIES, $term_id, 'category_thumb', true);
		return dispatcher::applyFilters('imgSrc', $category_thumb, 'categoryImg');
    }
	/**
     * Return the url of the brand image
     * @param type $term_id
     * @return string 
     */
	public function getBrandImage($term) {
		$category_thumb = '';
		if (is_object($term)) {
			$term_id = $term->term_id;
		} elseif (is_int($term)) {
			$term_id = $term;
		} else {
			$category_thumb = lang::_('No image');
		}
		if(empty($category_thumb))
			$category_thumb = get_metadata(self::BRANDS, $term_id, 'brand_thumb', true);
		return dispatcher::applyFilters('imgSrc', $category_thumb, 'brandImg');
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
		if(!frame::_()->getModule('options')->get('use_new_products_fields')) {
			$tab = new tab(lang::_('Product Fields'), $this->getCode());
			$tab->setView('productFieldsTab');
			$tab->setSortOrder(4);
			$tab->setParent('templates');
			$tab->setNestingLevel(1);
			$tabs[] = $tab;
		}
        $tab = new tab(lang::_('Product Views'), $this->getCode());
        $tab->setView('productViewTab');
		$tab->setSortOrder(5);
		$tab->setParent('templates');
		$tab->setNestingLevel(1);
        $tabs[] = $tab;
        return $tabs;
    }
    /**
     * Init the product view options
     */
    public function productViewOptionsInit() {
        register_setting( 're_product_single', 're_product_single', array($this,'singleProductViewValidate' ));
        register_setting( 're_product_category', 're_product_category', array($this,'categoryProductViewValidate'));
    }
    /**
     * Return the validation results
     * @param array $input
     * @return array 
     */
    public function singleProductViewValidate($input) {
        $default_options = $this->getView('productViewTab')->getDefaultProductView();
        $checkboxes = array('full_image', 'preview_images', 'title', 'price', 'sku', 'short_descr', 'full_descr', 'details','add_to_cart', 'quantity', 'show_facebook', 'show_gplus', 'show_twitter');
        foreach ($default_options as $key=>$value) {
            if (in_array($key, $checkboxes)) {
                if (!isset( $input[$key]))
                    $input[$key] = 0;
            } 
        }
        return $input;
    }
    
    public function productViewLoadStyle(){
        $viewOptions = $this->getView('productViewTab')->getProductCategoryViewOptions();
        $total_width = 100 - 3;
        $width = floor($total_width/3);
        echo '<style type="text/css">';
        echo '.tax-products_categories .product {width: '.$width.'%;}';
		if ($viewOptions['add_comments_link'] == 0) {
			echo '.tax-products_categories .comments-link {display: none;}';
		}
        echo '</style>';
    }
    /**
     * Return the validation results
     * @param array $input
     * @return array 
     */
    public function categoryProductViewValidate($input) {
        $default_options = $this->getView('productViewTab')->getDefaultProductCategoryView();
        $checkboxes = array('title', 'price', 'more', 'add_to_cart', 'add_comments_link', 'shadow_border', 'short_descr', 'catalog_image');
        foreach ($default_options as $key=>$value) {
            if (in_array($key, $checkboxes)) {
                if (!isset( $input[$key]))
                    $input[$key] = 0;
            } 
        }
        return $input;
    }
    /**
     * Check if product is in stock
     * @param numeric $d['buyQty'] product quantity to check, it is quantity that you try to buy
     * @param numeric $d['existQty'] product quantity that exist, it is quantity of product in database
     * @param numeric $d['pid'] productID - if empty existQty - than we will select it from DB here
     * @return numeric quantity that you can buy for this product, if 0 - product is out of stock
     */
    public function checkInStock($d = array()) {
        $remainAfter = 0;
		$d['dontUseCart'] = isset($d['dontUseCart']) ? $d['dontUseCart'] : false;
        if(!isset($d['existQty']) && isset($d['pid'])) {
            $d['existQty'] = frame::_()->getTable('products')->get('quantity', array('post_id' => $d['pid']), '', 'one');
        }
        if(isset($d['existQty']) && isset($d['buyQty'])) {
            $remainAfter = (int)$d['existQty'] - (int)$d['buyQty'];
        }
		if($remainAfter >= 0 && !$d['dontUseCart'] && isset($d['pid'])) {	// Check if this product is already in cart
			$prodInCart = frame::_()->getModule('user')->getModel('cart')->get(array('pid' => $d['pid']));
			if($prodInCart && isset($prodInCart['qty'])) {
				$remainAfter -= (int) $prodInCart['qty'];
			}
		}
		if($remainAfter < 0)
			$remainAfter = false;
        return $remainAfter;
    }
    /**
     * Update quantity in database after checkout
     * @param array $d[] = array with products from cart module. @see modules/user/models/cart::get()
     */
    public function updateStock($d = array()) {
        if(!empty($d)) {
            foreach($d as $prod) {
				db::query('UPDATE @__products SET quantity = IF(quantity <= 0 || (CAST(quantity AS SIGNED) - '. $prod['qty']. ' < 0), 0, quantity - '. $prod['qty']. ')
							WHERE post_id = "'. (int)$prod['pid']. '" LIMIT 1');
            }
        }
    }
    public function getPrice($pid, $price) {
        if(frame::_()->getModule('special_products')) {
            return frame::_()->getModule('special_products')->getPrice($pid, $price);
        }
        return $price;
    }
    /**
     * Hardcode for special products mod as it is used very offen
     */
    public function getSpecialModData($pid, $price) {
        $res = array(
            'mark_as_sale' => false,
            'show_old_prices' => false,
            'oldPrice' => $price,
            'newPrice' => 0,
            'price' => $price,
            'is_special' => false,
        );
        if(frame::_()->getModule('special_products')) {
            $specials = frame::_()->getModule('special_products')->getSpecialByPid($pid);
            if(!empty($specials)) {
                $res['is_special'] = true;
                $res['newPrice'] = $res['price'] = frame::_()->getModule('special_products')->getPrice($pid, $price);
                foreach($specials as $s) {
                    if((int) $s['mark_as_sale']) {
                        $res['mark_as_sale'] = true;
                    }
                    if((int) $s['show_old_prices']) {
                        $res['show_old_prices'] = true;
                    }
                }
            }
        }
        return $res;
    }
    public function getWeight($pid, $weight, $qty, $params = array('options' => array())) {
        $weight = (float) $weight * (float) $qty;
        if(!empty($params['options'])) {
            $addForOptions = 0;
            foreach($params['options'] as $o) {
                $selectedValues = array();
                if(is_array($o['selected'])) 
                    $selectedValues = $o['selected'];
                 else 
                    $selectedValues = array($o['selected']);
                foreach($selectedValues as $selected) {
                    if(!empty($o['opt_values'][ $selected ]['data']['weight'])) {
                        $addForOptions += (float) $o['opt_values'][ $selected ]['data']['weight'] * $weight / 100;   //% of total price
                    }
                }
             }
            $weight += $addForOptions;
        }
        return $weight;
    }
    public function getSizes($pid, $sizes = array('width' => 0, 'height' => 0, 'length' => 0), $qty = 1, $params = array('options' => array())) {
        $sizes['width'] = (float) $sizes['width'] * (float) $qty;
        $sizes['height'] = (float) $sizes['height'] * (float) $qty;
        $sizes['length'] = (float) $sizes['length'] * (float) $qty;
        return $sizes;
    }
	/**
	 * After save Product Views we should avoid redirecting to complete ajax request
	 */
	public function productViewOptionsSaveRedirect($location, $status) {
		$urlData = parse_url($location);
		$reqType = req::getVar('reqType');
		if($status == 302 
			&& $reqType == 'ajax'
			&& isset($urlData['path'])
			&& isset($urlData['query'])
			&& (strpos($urlData['path'], 'wp-admin') !== false)
			&& ($urlData['query'] == 'page=toeoptions&settings-updated=true')
		) {
			return false;
		}
		return $location;
	}
}