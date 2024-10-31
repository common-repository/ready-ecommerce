<?php
@ini_set('max_execution_time', 300);
class dummy_dataModel extends model {
	private $_installedTaxesIds = array();
	public function installProducts() {

		$filePath = frame::_()->getModule('dummy_data')->getDummyFilePath();
		/**
			@install defalt dummy data products
		*/
		if(!file_exists($filePath)){
			$filePath = frame::_()->getModule('dummy_data')->getDefaultDummyDataFile();
		}
		if(is_file($filePath)) {
			$data = XML2Array::createArray(file_get_contents($filePath));
			if(!empty($data) 
				&& isset($data['products']) 
				&& isset($data['products']['product']) 
				&& !empty($data['products']['product'])
				&& is_array($data['products']['product'])
			) {
				$this->removeProducts();
				if(isset($data['products']['category'])) {
					if(!isset($data['products']['category'][0]))
						$data['products']['category'] = array($data['products']['category']);
					
					foreach($data['products']['category'] as $cat) {
						$this->_installCategory($cat);
					}
				}
				if(isset($data['products']['brand'])) {
					if(!isset($data['products']['brand'][0]))
						$data['products']['brand'] = array($data['products']['brand']);
					foreach($data['products']['brand'] as $cat) {
						$this->_installBrand($cat);
					}
				}
				// Collect all categories and brands IDs here and save it to proper remove in future
				if(!empty($this->_installedTaxesIds)) {
					update_option('installedTaxesIds', $this->_installedTaxesIds);
				}
				$installedDummyProducts = get_option('installedDummyProducts', array());

				$products = $data['products']['product'];	// All product tags from xml document
				if(!isset($products[0])) {	// When only one element (product) present
					$products = array($products);
				}
				foreach($products as $p) {

					$addProdData = $p;
					if(isset($addProdData['parameters']) && !empty($addProdData['parameters']) && isset($addProdData['parameters']['parameter'])) {
						$addProdData['parameters'] = $this->_prepareParametersData($addProdData['parameters']['parameter']);
					}
					if(isset($addProdData['imgs']) && !empty($addProdData['imgs']) && isset($addProdData['imgs']['img'])) {
						$addProdData['imgs'] = $this->_prepareImgData($addProdData['imgs']['img']);
					}

					$insert = true;
					$currentProductKey = md5(json_encode($addProdData));
					if(!empty($installedDummyProducts)){
						foreach($installedDummyProducts as $item){
							if($currentProductKey==$item['prod_unique_key']){
								$insert=false;
								break;
							}
						}						
					}
					$addProdData['post_content']=htmlspecialchars($addProdData['post_content']);
					$addProdData['post_title']=htmlspecialchars($addProdData['post_title']);
					$addProdData['post_name']=htmlspecialchars($addProdData['post_name']);
					$addProdData['tags_input']=htmlspecialchars($addProdData['tags_input']);
					$addProdData['post_excerpt']=htmlspecialchars($addProdData['post_excerpt']);
					if($insert){
							$newPid = frame::_()->getModule('products')->getModel()->addProduct($addProdData);
					}
					
					if(@$newPid) {
						$installedDummyProducts[] = array('id'=>$newPid,'prod_unique_key'=>md5(json_encode($addProdData)));
					}
				}
				update_option('installedDummyProducts', $installedDummyProducts);
				update_option('wasDummyProductsInstalled', true);
				return true;

			} else
				$this->pushError(lang::_('Empty or invalid XML file'));
		} else
			$this->pushError(lang::_('Dummy File was not found in this theme'));
		return false;
	}
	public function removeProducts() {

		$installedDummyProducts = get_option('installedDummyProducts', array());
		if(!empty($installedDummyProducts) && is_array($installedDummyProducts)) {
			foreach($installedDummyProducts as $pid) {
				frame::_()->getModule('products')->removeProduct(array('pid' => $pid['id']));
			}

			$installedTaxesIds = get_option('installedTaxesIds', array());
			if(!empty($installedTaxesIds) && is_array($installedTaxesIds)) {
				foreach($installedTaxesIds as $taxonomy => $term_ids) {
					if(!empty($term_ids) && is_array($term_ids)) {
						foreach($term_ids as $term_id) {
							frame::_()->getModule('products')->removeTax(array('term_id' => $term_id, 'taxonomy' => $taxonomy));
						}
					}
				}
				update_option('installedTaxesIds', array());
			}
			update_option('wasDummyProductsInstalled', false);
			return true;
		} else
			$this->pushError(lang::_('No dummy products was found'));
		return false;
	}
	public function wasDummyProductsInstalled() {
		return get_option('wasDummyProductsInstalled', false);
	}
	private function _prepareParametersData($parameters) {
		if(!isset($parameters[0]))
			$parameters = array($parameters);
		$paramsCount = count($parameters);
		for($i = 0; $i < $paramsCount; $i++) {
			if(isset($parameters[$i]['options']) && isset($parameters[$i]['options']['option'])) {
				$parameters[$i]['options'] = $parameters[$i]['options']['option'];
				if(!isset($parameters[$i]['options'][0]))
					$parameters[$i]['options'] = array($parameters[$i]['options']);
			}
		}
		return $parameters;
	}
	private function _prepareImgData($imgs) {
		if(!isset($imgs[0]))
			$imgs = array($imgs);
		if(!empty($imgs)) {
			$imgCount = count($imgs);
			for($i = 0; $i < $imgCount; $i++) {
				if(isset($imgs[$i]['filename']))
					$imgs[$i]['filename'] = frame::_()->getModule('dummy_data')->getDummyDataTemplatePath(). 'img'.DS.$imgs[$i]['filename'];
			}
		}
		return $imgs;
	}
	public function reactivateWidgets() {
		update_option('sidebars_widgets', '');
		addWidgetToSidebar('cart', 'toeshoppingcartwidget', 0);
		addWidgetToSidebar('currency', 'toecurrencywidget', 0);
		addWidgetToSidebar('newproducts', 'toerecentproductswidget', 0, array('title' => 'New Products', 'number_of_products' => '6', 'show_price' => '1', 'show_add_to_cart' => '1'));
		addWidgetToSidebar('breadcrumbs', 'toebrcwidget', 0, array('home_title' => 'Home'));
		addWidgetToSidebar('bestsellers', 'toebestsellerswidget', 0, array('title' => 'Bestsellers', 'number_of_products' => '6', 'show_price' => '1', 'show_add_to_cart' => '1'));
		addWidgetToSidebar('brands', 'toebcwidget', 0, array('title' => 'Brands', 'list' => 'products_brands', 'view' => '1'));
		addWidgetToSidebar('sidebarproducts', 'toebcwidget', 1, array('title' => 'Categories', 'list' => 'products_categories', 'view' => '0'));
		addWidgetToSidebar('sidebarproducts', 'toebcwidget', 2, array('title' => 'Brands', 'list' => 'products_brands', 'view' => '0'));
		addWidgetToSidebar('sidebar', 'categories', 0, array('title' => 'Categories'));
		addWidgetToSidebar('footercontact', 'text', 0, array('title' => 'Contact Us', 'text' => '<p>+7 (905) 123-45-67<br />+7 (960) 456-78-90<br />+7 (123) 444-55-66</p><p>changeMe@gmail.com<br />changeMe2@gmail.com</p>'));
		addWidgetToSidebar('footertwitter', 'toetwitterwidget', 0, array('title' => 'Latest Tweets', 'username' => 'ReadyEcommerceW', 'count' => '2'));

		update_option('posts_per_page', 12);
		return true;
	}
	public function removeDummyDataInstallNotice() {
		update_option('hideDummyDataInstallNotice', true);
		return true;
	}
	public function hideDummyDataInstallNotice() {
		return get_option('hideDummyDataInstallNotice', false);
	}
	private function _installCategory($d = array()) {
		$d['taxonomy'] = frame::_()->getModule('products')->getConstant('CATEGORIES');
		return $this->_installTax($d);
	}
	private function _installBrand($d = array()) {
		$d['taxonomy'] = frame::_()->getModule('products')->getConstant('BRANDS');
		return $this->_installTax($d);
	}
	/**
	 * Recursively function to install all categories or brands with children categories
	 */
	private function _installTax($d = array()) {
		if($d['taxonomy'] == frame::_()->getModule('products')->getConstant('CATEGORIES'))
			$taxAlias = 'category';
		else
			$taxAlias = 'brand';
		// Look for image
		if(isset($d['img']) && !empty($d['img'])) {
			// Try to create wordpress attachment
			if($attachId = frame::_()->getModule('products')->getModel()->addImg(array(
				'filename' => frame::_()->getModule('dummy_data')->getDummyImgDir(). $d['img'],
				'pid' => 0,
			))
			) {
				// Get image data by attach id
				$imgData = wp_get_attachment_image_src($attachId);
				if($imgData 
					&& is_array($imgData) 
					&& isset($imgData[0]) 
					&& !empty($imgData[0])
				) {
					switch($d['taxonomy']) {
						case frame::_()->getModule('products')->getConstant('CATEGORIES'):
							$d['product_category_thumb_url'] = $imgData[0];
							break;
						case frame::_()->getModule('products')->getConstant('BRANDS'):
							$d['product_brand_thumb_url'] = $imgData[0];
							break;
					}
				}
			}
		}
		$newTermId = frame::_()->getModule('products')->getModel()->addTax($d);
		if($newTermId) {
			if(!isset($this->_installedTaxesIds[$d['taxonomy']]))
				$this->_installedTaxesIds[$d['taxonomy']] = array();
			$this->_installedTaxesIds[$d['taxonomy']][] = $newTermId;
		}
		if(isset($d['children']) 
			&& is_array($d['children']) 
			&& !empty($d['children'])
			&& isset($d['children'][$taxAlias])
			&& is_array($d['children'][$taxAlias])
			&& !empty($d['children'][$taxAlias])
			&& $newTermId
		) {
			if(!isset($d['children'][$taxAlias][0]))
				$d['children'][$taxAlias] = array($d['children'][$taxAlias]);
			foreach($d['children'][$taxAlias] as $subCat) {
				if(!is_array($subCat)){
					$subCat = array('taxonomy'=>$subCat);
				}

				$subCatData = array_merge($subCat, array('parent' => $newTermId));
				switch($d['taxonomy']) {
					case frame::_()->getModule('products')->getConstant('CATEGORIES'):
						$this->_installCategory($subCatData);
						break;
					case frame::_()->getModule('products')->getConstant('BRANDS'):
						$this->_installBrand($subCatData);
						break;
				}
			}
		}
	}
}
