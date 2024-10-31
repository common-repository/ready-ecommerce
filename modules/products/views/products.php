<?php
/**
 * Products view class
 */
class productsView extends view {
    protected $_whereImgFilterMethod = '';
	/**
	 * Products extra fields, singleton cache
	 */
	protected $_prodcExtraFields = array();
    /**
     * Render the template
     * 
     * @param string $tpl 
     */
    public function display($tpl = '') {
        parent::display($tpl);
    }
    /**
     * Render product view front-end
     * 
     * @global object $post
     * @return type 
     */
    public function getProductContent($d = array(), $showVariations = true) {
        global $post, $page, $pages;

        if(isset($d['productPost'])) {
            // substitute this current blog data for correct work all other functionality like get_the_content(), that take info from this global variables
            $currentWPData = array('post' => $post, 'pages' => $pages);
            $post = $d['productPost'];
            $pages[$page-1] = $post->post_content;
        }
		remove_filter('get_the_excerpt', 'wp_trim_excerpt');
		
		$parentId = $post->ID;
		$isVariation = ($post->post_type == S_VARIATION);
		$variationId = 0;
		if($isVariation) {
			$variationId = $post->ID;
			$parentId = $post->post_parent;
			$parentPost = get_post($parentId);
			$post->post_content = $parentPost->post_content;
			$post->post_excerpt = $parentPost->post_excerpt;
			$d['originalContent'] = $parentPost->post_content;
		}
		if($showVariations) {
			$this->assign('variationsSelect', $this->getVariationsSelect($variationId, $parentId/*, $parent_post*/));
		}
		
        $pData = frame::_()->getModule('products')->getController()->getModel('products')->get($post);
		if(isset($pData['mark_as_new']) && is_object($pData['mark_as_new']))
			$pData['mark_as_new']->hide = true;
        $pExtra = $this->getProductExtraContent($post);
        $additionalTabs = array();
		$customFields = array();
        if (isset($d['category_view']) && $d['category_view']) {
			$image = $this->getProductImage($parentId, true, false, 'all', 'catt');
		} else {
			$image = $this->getProductImage($parentId, true, false, 'all', 'desc');
            $images = $this->getProductImages($parentId, false, 'all', 'desc');
            $this->assign('images',$images);
            $additionalTabs = dispatcher::applyFilters('productDetailsTabs', $additionalTabs, $post);
            frame::_()->getTable('products')->update(array('views' => 'views+1'), array('post_id' => $post->ID));   //Increase it's views - we will use it in "Most Viewed widget" and analitics
			$customFields = frame::_()->getModule('products')->getModel()->getCustomFields(array('pid' => $post->ID));
		}
        $this->assign('post', $post);
        $this->assign('pData', $pData);
        $this->assign('pExtra', $pExtra);
        $this->assign('image',$image);
        $this->assign('additionalTabs', $additionalTabs);
		$this->assign('customFields', $customFields);
        $content = '';
        $content = dispatcher::applyFilters('productContentBefore', $content, $post, $pData, $pExtra, $image, $images);
        /**
         * Special Products addition
         */
        $this->assign('markAsSale', frame::_()->getModule('products')->markAsSale($post->ID));
        /**
         * Rating addition - this module is in paid version
         */
        $ratingBox = dispatcher::applyFilters('productRating', '', $post); //If rating module is active - here will be rating content
        $this->assign('ratingBox', $ratingBox);
		/**
		 * Geting price html, there are some additional manipulations with it, so it is better to separate it in template, not just show it's value
		 */
		$this->assign('priceHtml', $this->getPrice($post->ID, $pData['price']->value, $post));
        if (isset($d['category_view']) && $d['category_view']) {
            $view_options = isset($d['viewOptions']) && !empty($d['viewOptions']) 
				? $d['viewOptions'] 
				: frame::_()->getModule('products')->getView('productViewTab')->getProductCategoryViewOptions();
            $this->assign('viewOptions', $view_options);
            $this->assign('actionButtons', $this->getActionButtons($pData, true, $post, $d));
			if($view_options['full_descr']) {
				$this->assign('fullDescr', (isset($d['originalContent']) ? $d['originalContent'] : get_the_content()));
			}
            $content = parent::getContent('productCategoryContent');
        } else {
            $view_options = isset($d['viewOptions']) && !empty($d['viewOptions']) 
				? $d['viewOptions']
				: frame::_()->getModule('products')->getView('productViewTab')->getProductViewOptions();
            $this->assign('viewOptions', $view_options);
            $this->assign('actionButtons', $this->getActionButtons($pData, false, $post, $d));
			if($view_options['full_descr']) {
				$this->assign('fullDescr', (isset($d['originalContent']) ? $d['originalContent'] : get_the_content()));
			}
            $content = parent::getContent('productContent');
        }
		
        $content = dispatcher::applyFilters('productContentAfter', $content, $post, $pData, $pExtra, $image, $images);
        // return current blog data
        if(isset($currentWPData)) {
            $post = $currentWPData['post'];
            $pages[$page-1] = $currentWPData['pages'];
        }
        return $content;
    }
	public function getVariationsSelect($variationId, $parentId/*, $parent_post*/) {
		$par_var = array();
		$par_var[0]['post_id'] = 0;
		$par_var[0]['id'] = 0;
		
		$parentPost = get_post($parentId);
		$par_var[0]['var_name'] = mb_substr($parentPost->post_title, 0, 40, 'UTF-8'). '... ('. lang::_('parent product'). ')';
		$variations = frame::_()->getModule('products')->getController()->getModel('products')->getVariations($parentId);

		if(!empty($variations) && is_array($variations)) {
			array_unshift($variations, $par_var);
			for($i = 0; $i < count($variations); $i++)
				if($variations[$i][0]['post_id'] == $variationId)
					$variations[$i][0]['selected'] = 'selected';
			if(!empty($variations)) {
				$this->assign('variations', $variations);
				$this->assign('parent_id', $parentId);
				if($variationId == 0)
					$this->assign('current_id', $parentId);
				else
					$this->assign('current_id', $variationId);
				return parent::getContent('productVariations');
			}
		}
		return false;
	}
    protected function _assignExtraFields($post = NULL) {
        $extraFields = array();
        if(isset($post) && is_object($post)) {
			if(frame::_()->getModule('options')->get('use_new_products_fields')) {
				$extraFieldsData = frame::_()->getModule('products_fields')->getModel()->getForProduct(array('pid' => $post->ID, 'active' => 1));
				if(!empty($extraFieldsData)) {
					foreach($extraFieldsData as $fieldData) {
						$fieldName = in_array($fieldData['htmltype'], array('checkboxlist', 'selectlist')) 
							? 'options['. $fieldData['id']. '][]' 
							: 'options['. $fieldData['id']. ']';
						$field = toeCreateObj('field', array($fieldName, $fieldData['htmltype'], 'other', '', $fieldData['label']));
						$field->mandatory = $fieldData['mandatory'];
						if(isset($fieldData['options']) && !empty($fieldData['options'])) {
							$htmlOptions = array();
							foreach($fieldData['options'] as $opt) {
								if(in_array($fieldData['htmltype'], array('checkboxlist')))
									$htmlOptions[] = array(
										'id' => $opt['id'],
										'text' => $opt['label'],
									);
								else
								$htmlOptions[ $opt['id'] ] = $opt['label'];
							}
							$field->addHtmlParam('options', $htmlOptions);
							$field->addHtmlParam('attrs', 'class="toeProductOptions"');
						}
						if(isset($fieldData['default_value']) && !empty($fieldData['default_value'])) {
							$defaultValue = $fieldData['default_value'];
							if(in_array($fieldData['htmltype'], array('text', 'textarea'))) {
								$field->setHtml('block');
								$defaultValue = nl2br($defaultValue);
							}
							$field->setValue( $defaultValue );
						}
						$extraFields[] = $field;
					}
				}
			} else {
				$extraFields = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField($post, array('unsetEmptyFields' => true, 'where' => 'active = 1 AND '));
				foreach($extraFields as $f) {
					switch($f->getHtml()) {
						case 'checkboxlist':    case 'selectlist':
							$f->setName('options['. $f->getID(). '][]');
							break;
						default:
							$f->setName('options['. $f->getID(). ']');
							break;
					}
					if(in_array($f->getHtml(), array('checkboxlist', 'selectlist', 'selectbox'))) {
						$optionsNew = array();
						$optionsSorted = array();
						$options = $f->getHtmlParam('options');
						if(!empty($options)) {
							foreach($options as $id => $val) {
								$optionsSorted[$id] = (int) $f->data['opt_values'][$id]['sort_order'];
							}
						}
						asort($optionsSorted);
						if($f->getHtml() == 'selectbox')
						if (!$f->data['params']['options']['selonoff'][0]) {
							$optionsNew[0] = lang::_('Select');
						}
						foreach($optionsSorted as $id => $val) {
								$optionsNew[$id] = $options[$id];
						}
						$f->addHtmlParam('options', $optionsNew);
					}
					$f->addHtmlParam('attrs', 'class="toeProductOptions"');
					if(isset($f->default_value))
						$f->setValue($f->default_value);
					// For text and textarea - try to show static info
					if(frame::_()->getModule('options')->getModel('productfields')->useTextBlock($f->getHtml())) {
						$textValue = frame::_()->getModule('options')->getModel('productfields')->extractTextVal( $f );
						if(!empty($textValue)) {
							$f->setHtml('block');
						}
						$f->setValue( nl2br($textValue) );
					}
				}
			}
			$this->_prodcExtraFields[ $post->ID ] = $extraFields;
        }
        $this->assign('extraFields', $extraFields);
    }
	public function getAssignedExtraFields($pid) {
		return isset($this->_prodcExtraFields[$pid]) ? $this->_prodcExtraFields[$pid] : false;
	}
    /**
     * Action buttons: add to cart and buy now, or some other actions (depend on quantity)
     * @param array $d data for product
     * @return string html content for action buttons
     */
    public function getActionButtons($d = array(), $is_category = false, $post = NULL, $otherData = array()) {
        $tpl = '';
        $availableQty = frame::_()->getModule('products')->checkInStock(array('existQty' => $d['quantity']->value, 'buyQty' => 0));
		if(!empty($otherData) && isset($otherData['viewOptions'])) {
			$view_options = $otherData['viewOptions'];
		} elseif ($is_category) {
            $view_options = frame::_()->getModule('products')->getView('productViewTab')->getProductCategoryViewOptions();
        } else {
            $view_options = frame::_()->getModule('products')->getView('productViewTab')->getProductViewOptions();
        }
        $useFormOnButtonsTpl = utils::getCurrentWPThemeCode() == 'ready_ecommerce_theme' ? true : false;    //For this exact theme, that is old, and do not support new feature when all product content is form
        
        $this->_assignExtraFields($post);
        $this->assign('useFormOnButtonsTpl', $useFormOnButtonsTpl);
        $this->assign('viewOptions', $view_options);
        $this->assign('is_category', $is_category);

        if($availableQty !== false || frame::_()->getModule('options')->get('stock_allow_checkout')) {
            $this->assign('availableQty', $availableQty);
            $this->assign('stockCheck', frame::_()->getModule('options')->get('stock_check'));
            $tpl = 'buttonsBuyAddToCart';
        } else {
            $tpl = 'buttonsNotifyOnProduct';
        }
		return dispatcher::applyFilters('prodActionButons', parent::getContent($tpl), $this->post->ID);
    }
    /**
     * Function to get all the extra data for product
     * 
     * @param object $post 
     * @return array
     */
    public function getProductExtraContent($post) {
        $pExtra = frame::_()->getModule('products')->getModel('products')->getExtraContent($post);
        return $pExtra;
    }
    protected function _beforeImgGet($status = 'all') {
        $this->_whereImgFilterMethod = '';
        switch($status) {
            case 'catt':
                $this->_whereImgFilterMethod = 'whereCattProductsImages';
                break;
            case 'desc':
                $this->_whereImgFilterMethod = 'whereDescProductsImages';
                break;
			case 'catt_only':
				$this->_whereImgFilterMethod = 'whereCattOnlyProductsImages';
				break;
        }
        if($this->_whereImgFilterMethod)
            add_filter('posts_search', array(frame::_()->getModule('products'), $this->_whereImgFilterMethod));
		add_filter('posts_search', array(frame::_()->getModule('products'), 'whereProductsImages'));
    }
    protected function _afterImgGet($status = 'all') {
        if($this->_whereImgFilterMethod)
            remove_filter('posts_search', array(frame::_()->getModule('products'), $this->_whereImgFilterMethod));
        $this->_whereImgFilterMethod = '';
		remove_filter('posts_search', array(frame::_()->getModule('products'), 'whereProductsImages'));
    }
    /**
     * Returns the url to product image and product thumb
     * 
     * @param object $post
     * @return array 
     */
    public function getProductImage($post, $admin = false, $avoidReplacing = false, $for = 'all', $status = 'all', $size = 'product-display'){
        global $wpdb;
        $pid = is_numeric($post) ? $post : $post->ID;
		
		$parent_post = get_post($pid);
		if($parent_post->post_type == S_VARIATION)
			$pid = $parent_post->post_parent;
		
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => 1,
            'post_status' => null,
            'post_parent' => $pid,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );
        
        $this->_beforeImgGet($status);
        $thumb = get_posts($args);
        $this->_afterImgGet($status);
        
        $result = array();
		// For products without pictures - 0 valued objects to avoid missunderstanding in templates
		if(empty($thumb))
				$thumb = array( (object)array('ID' => 0) );
        if(in_array($for, array('all', 'thumb'))) {
            if ($admin) {
                $size = 'category-thumb';
            }
            $product_display = wp_get_attachment_image_src($thumb[0]->ID, $size);
            if (empty($product_display)) {
                $product_display = image_downsize($thumb[0]->ID, $size);
            }
            $result['thumb'] = $product_display;
            $result['thumb']['base'] = $thumb;
			if(!isset($result['thumb'][0]))
				$result['thumb'][0] = '';
        }
        if(in_array($for, array('all', 'big'))) {
            $result['big'] = wp_get_attachment_image_src($thumb[0]->ID, 'full');
            $result['big']['base'] = $thumb;
			if(!isset($result['big'][0]))
				$result['big'][0] = '';
        }
        if(!$avoidReplacing)
            $result = $this->_substituteProdImgSrc($result, $pid, $for, $thumb[0]);
        return $result;
        
    }
    /**
     * Return all product images
     * 
     * @param object $post
     * @return array 
     */
    public function getProductImages($post, $avoidReplacing = false, $for = 'all', $status = 'all') {
        $pid = is_numeric($post) ? $post : $post->ID;
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => 999,
            'post_status' => null,
            'post_parent' => $pid,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );
        
        $this->_beforeImgGet($status);
        $thumbs = get_posts($args);
        $this->_afterImgGet($status);
        $results = array();
        foreach ($thumbs as $thumb) {
            $result = array();
            if(in_array($for, array('all', 'big'))) {
                $result['big'] = wp_get_attachment_image_src($thumb->ID, 'full');
                $result['big']['base'] = $thumb;
				if(!isset($result['big'][0]))
					$result['big'][0] = '';
            }
            if(in_array($for, array('all', 'thumb'))) {
                $result['thumb'] = wp_get_attachment_image_src($thumb->ID, 'product-preview');
                $result['thumb']['base'] = $thumb;
				if(!isset($result['thumb'][0]))
					$result['thumb'][0] = '';
            }
			if(in_array($for, array('all', 'category'))) {
                $result['category'] = wp_get_attachment_image_src($thumb->ID, 'category-thumb');
                $result['category']['base'] = $thumb;
				if(!isset($result['category'][0]))
					$result['category'][0] = '';
            }
			if(!empty($result)) {
				foreach($result as $catt => $r) {
					$result[$catt]['thumb'] = $thumb;
				}
			}
            if(!$avoidReplacing)
                $result = $this->_substituteProdImgSrc($result, $pid, $for, $thumb);
            $results[] = $result;
        }
        return $results;
    }
    protected function _substituteProdImgSrc($images, $pid, $for = 'all', $thumb = NULL) {
        if(frame::_()->getModule('options')->get('img_preprocessing_type')) {
            if(!is_object($thumb))
                $thumb = new stdClass ();
            //Fix for Tiffany
            switch(frame::_()->getModule('options')->get('img_preprocessing_type')) {
                case 'timthumb':
                    if(in_array($for, array('all', 'big')))
                        $images['big'][0] = uri::_(array('baseUrl' => S_MODULES_PATH. 'timthumb.php', 'src' => $images['big'][0]));
                    if(in_array($for, array('all', 'thumb')))
                        $images['thumb'][0] = uri::_(array('baseUrl' => S_MODULES_PATH. 'timthumb.php', 'src' => $images['thumb'][0]));
                    break;
                case 'plugin':
                    if(in_array($for, array('all', 'big')))
                        $images['big'][0] = uri::_(array('baseUrl' => admin_url('admin-ajax.php'), 'mod' => 'img', 'action' => 'getProdImg', 'pid' => $pid, 'for' => 'big', 'imgId' => $thumb->ID));
                    if(in_array($for, array('all', 'thumb')))
                        $images['thumb'][0] = uri::_(array('baseUrl' => admin_url('admin-ajax.php'), 'mod' => 'img', 'action' => 'getProdImg', 'pid' => $pid, 'for' => 'thumb', 'imgId' => $thumb->ID));
                    break;
            }
        }
        return $images;
    }
    public function getPrice($pid, $price, $post = NULL) {
        $specials = NULL;
        if(frame::_()->getModule('special_products'))
            $specials = frame::_()->getModule('special_products')->getSpecialByPid($pid);
        $this->assign('saleTpl', NULL);
        $this->assign('oldPrice', NULL);
        $this->assign('specials', NULL);
        if($specials) {
            $oldPrice = $price;
            $showOldPrices = false;
            foreach($specials as $s) {
                if((int) $s['mark_as_sale']) {
                    $this->assign('saleTpl', parent::getContent('productSale'));
                }
                if((int) $s['show_old_prices']) {
                    $showOldPrices = true;
                }
            }
            $price = frame::_()->getModule('products')->getPrice($pid, $price);
            if($price != $oldPrice && $showOldPrices)
                $this->assign('oldPrice', $oldPrice);
            $this->assign('specials', $specials);
        }
		$showFromPriceLabel = NULL;		// if not empty value - price will be market as "From", and this means that product have options which depends on price
		if(frame::_()->getModule('options')->get('prod_show_from_label_if_opt_exist') && !empty($post) && is_object($post) && isset($post->ID) && isset($post->toePriceOptExist)) {
			$showFromPriceLabel = $post->toePriceOptExist;
		}
		$this->assign('showFromPriceLabel', $showFromPriceLabel);
        $this->assign('price', $price);
        return dispatcher::applyFilters('productPrice', parent::getContent('productPrice'));
    }
	public function getCategoriesListHtml($content) {
		$showSubcategoriesOnCategoriesPage = frame::_()->getModule('options')->get('show_subcategories_on_categories_page');
		if($showSubcategoriesOnCategoriesPage)
			$categories = frame::_()->getModule('products')->getCategories();
		else
			$categories = frame::_()->getModule('products')->getCategories(array('parent' => 0));
		if(empty($categories)) {
			return parent::getContent('noCategoriesFound');
		} else {
			if($showSubcategoriesOnCategoriesPage) {
				$newCatsData = array();
				foreach($categories as $c) {
					$newCatsData[ $c->term_id ] = $c;
				}
				return $this->_drawCategoriesList($newCatsData, 0);
			}
			return $this->categoriesList($categories);
		}
	}
	/**
	 * Will be draw categories with sub-categories in recursive way
	 */
	private function _drawCategoriesList($categories, $parent) {
		$res = '';
		$children = $this->_extractChildCategories($categories, $parent);
		if(!empty($children)) {
			foreach($children as $cid => $c) {
				$subChildren = $this->_extractChildCategories($categories, $cid);
				if(!empty($subChildren)) {
					$children[ $cid ]->sub_categories_html = $this->_drawCategoriesList($categories, $cid);
				}
			}
			$res = $this->categoriesList($children);
		} else {
			$drawCats = array();
			foreach($categories as $cid => $c) {
				if($c->category_parent == $parent)
					$drawCats[] = $c;
			}
			if(!empty($drawCats))
				$res = $this->categoriesList($drawCats);
		}
		return $res;
	}
	/**
	 * Extract sub-categories from category
	 * @param array $categories all categories array
	 * @param numeric $parent parent ID
	 * @return array data with children
	 */
	private function _extractChildCategories($categories, $parent) {
		$res = array();
		foreach($categories as $cid => $c) {
			if($c->category_parent == $parent)
				$res[ $cid ] = $c;
		}
		return $res;
	}
	public function categoriesList($categories) {
		$this->assign('categories', $categories);
		return parent::getContent('categoriesList');
	}
	public function getBrandsListHtml($content) {
		$brands = frame::_()->getModule('products')->getBrands(array('parent' => 0));
		if(empty($brands)) {
			$tpl = 'noBrandsFound';
		} else {
			$this->assign('brands', $brands);
			$tpl = 'brandsList';
		}
		return parent::getContent($tpl);
	}
	/**
	 * @param $content string - current page content from admin area
	 * @param $additionalParams array - additional params for selection
	 * @return string of html content
	 */
	public function getAllProductsListHtml($content, $additionalParams = array(), $dataToViewMethod = array()) {
		global $wp_query, $post;
		$tempWpQuery = clone($wp_query);
		if(is_object($post))
			$tempPost = clone($post);
		
		$wpQueryAttrs = array('post_type' => S_PRODUCT);
		$firstQuery = $tempWpQuery->query;
		if(!empty($firstQuery) && is_array($firstQuery) && isset($firstQuery['paged']))
			$wpQueryAttrs['paged'] = $firstQuery['paged'];
		if(!empty($additionalParams))
			$wpQueryAttrs = array_merge($wpQueryAttrs, $additionalParams);
		$wp_query = new WP_Query( $wpQueryAttrs );
		
		if(have_posts()) {
			$productsContentParts = array();
			$dataToViewMethod['category_view'] = isset($dataToViewMethod['category_view'])
				? $dataToViewMethod['category_view']
				: true;
			while (have_posts()) {
				the_post();
				$productsContentParts[] = $this->getProductContent($dataToViewMethod);
			}
			$this->assign('productsContentParts', $productsContentParts);
			$tpl = 'allProductsListHtml';
		} else {
			$tpl = 'noProductsFound';
		}
		$content = parent::getContent($tpl);
		
		$wp_query = $tempWpQuery;
		if(is_object($tempPost))
			$post = $tempPost;
		return $content;
	}
	public function getPostFullContent($d = array()) {
		$query = '';
		if(is_array($d))
			$query = http_build_query ($d);
		else
			$query = $d;
		if(!empty($query)) {
			query_posts($query);
			have_posts();
			the_post();
			return parent::getContent('postFullContent');
		}
		return false;
	}
	public function getComingSoonProductButtons($post) {
		return parent::getContent('comingSoonProductButtons');
	}
}
