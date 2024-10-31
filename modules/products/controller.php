<?php
	class productsController extends controller {
		protected $_currentPostArr = array();
		public function saveProduct($post_ID, $post) {
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
			if(!current_user_can('edit_post', $post_ID)) return false;
			if(!$post_ID) return false;
			if(empty($this->_currentPostArr)) return false;     //nothing to update
			if($post->post_type != S_PRODUCT) return false;
			if(isset($this->_currentPostArr['action']) && $this->_currentPostArr['action'] == 'inline-save') return false;	// For quick update wordpress feature
			if(frame::_()->getTable('products')->exists($post_ID))
				$method = 'put';
			else
				$method = 'post';
			if(empty($this->_currentPostArr['ID']))
				$this->_currentPostArr['ID'] = $post_ID;
			if(empty($this->_currentPostArr['post_id']))
				$this->_currentPostArr['post_id'] = $post_ID;
			//$this->_currentPostArr['views']; //for now
			$this->getModel('products')->$method( $this->_currentPostArr );
			$this->saveProductExtraField( $this->_currentPostArr );
			$this->_currentPostArr = array();   //Ready for next cycle - if need
			return true;
		}
		public function savePostData($data , $postarr) {
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $data;
			if($data['post_type'] != S_PRODUCT)  return $data;
			if(empty($this->_currentPostArr)) {     //Only one data may pass through cycle of this two methods - savePostData() and saveProduct()
				$this->_currentPostArr = $postarr;
			}
			return $data;
		}
		public function getVariationInfo() {

			$res = new response();
			$pid = req::getVar("var_id");
			$where = array();
			$where['post_id'] = $pid;
			$where['ID'] = $pid;

			$fields = frame::_()->getModule('products')->getController()->getModel('products')->getVariation($where);
			if(frame::_()->getModule('options')->get('use_new_products_fields')) {
				$extraFieldsHtml = frame::_()->getModule('products_fields')->getView()->getTabContent(array('pid' => $pid));
			} else {
				$extraFields = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField($pid);
				$extraFieldsMultiple = array();
				$exFieldsDisable = array();
				$exFieldsEfVals = array();
				if(!empty($extraFields)) {
					$exIds = array();
					foreach($extraFields as $k => $f) {
						$exIds[ $f->getID() ] = 1;
						if($f->getHtml() == 'text') {
							$f->setHtml('checkbox');    //You will be able just to disable it
						} else {
							$f->setHtml('selectlist');  //You will be able select list of params for this product
							$f->setDescription(lang::_('You can drag each option to change sort order. Don\'t forget to save your changes.'));
							$f->addHtmlParam('multiple', false);	// Disable posibility to select multiple options
						}
					}
					$exFieldsDesc = frame::_()->getModule('options')->getModel('productfields')->getFieldsDesc(array('options' => $exIds, 'pid' => $pid, 'includeDisabled' => true));

					foreach($extraFields as $k => $f) {
						$f->setName('exOptions['. $f->getID(). ']');
						if($f->getHtml() == 'checkbox') {
							$checked = false;
							$option = end($exFieldsDesc[ $f->getID() ]['opt_values']);
							if(isset($option['excludePids']) && 
									is_array($option['excludePids']) && 
										   in_array($pid, $option['excludePids'])) {

								$checked = true;
							}
							$f->addHtmlParam('checked', $checked);
							$f->setValue($option['id']);
						} else {
							if(isset($exFieldsDesc[ $f->getID() ])) {
								if(!empty($exFieldsDesc[ $f->getID() ]['opt_values'])) {
									$selectListOptions = array();
									$selectListValues = array();
									foreach($exFieldsDesc[ $f->getID() ]['opt_values'] as $optVal) {
										$selectListOptions[ $optVal['id'] ] = $optVal['value'];

										$exFieldsEfVals[ $optVal['id'] ]['price'] = toeCreateObj('field', array('exVal['. $optVal['id']. '][price]', 'hidden'));
										$exFieldsEfVals[ $optVal['id'] ]['price']->setValue($optVal['price']);
										$exFieldsEfVals[ $optVal['id'] ]['price_absolute'] = toeCreateObj('field', array('exVal['. $optVal['id']. '][price_absolute]', 'hidden'));
										$exFieldsEfVals[ $optVal['id'] ]['price_absolute']->setValue($optVal['price_absolute']);
										$exFieldsEfVals[ $optVal['id'] ]['disabled'] = toeCreateObj('field', array('exVal['. $optVal['id']. '][disabled]', 'hidden'));
										$exFieldsEfVals[ $optVal['id'] ]['disabled']->setValue($optVal['disabled']);

										$exFieldsEfVals[ $optVal['id'] ]['sort_order'] = toeCreateObj('field', array('exVal['. $optVal['id']. '][sort_order]', 'hidden'));
										$exFieldsEfVals[ $optVal['id'] ]['sort_order']->setValue($optVal['sort_order']);

										$exFieldsEfVals[ $optVal['id'] ]['opt_val_id'] = toeCreateObj('field', array('exVal['. $optVal['id']. '][opt_val_id]', 'hidden'));
										$exFieldsEfVals[ $optVal['id'] ]['opt_val_id']->setValue($optVal['opt_val_id']);

										if(!empty($optVal['excludePids']) && is_array($optVal['excludePids']) && in_array($pid, $optVal['excludePids'])) {
											$selectListValues[] = $optVal['id'];
										}
									}
									$f->addHtmlParam('options', $selectListOptions);
									$f->addHtmlParam('attrs', 'class="toeExtraFieldsSelectList"');
									//$f->setValue($selectListValues);
								}
							}
						}
						$exFieldsDisable[ $f->getID() ] = toeCreateObj('field', array('exFieldsDisable['. $f->getID(). ']'));
					}
				}
				$res->addData('extraFieldsMultiple', $extraFields);
				$extraFieldsHtml = frame::_()->getModule('products')->getView('admin_Products')->generateFormExtraFields($extraFields,$exFieldsEfVals);
			}
			$downloads = frame::_()->getModule('digital_product')->getView('productDownloads')->displayVar('downloads',$pid);

			$res->addData('extraFields', $extraFieldsHtml);
			$res->addData('downloads', $downloads);
			$res->addData('variation', $fields);
			$res->ajaxExec();
		}
		public function deleteVariation(){
			$res = new response();
			$post_array = req::get("post");
			$pid = $post_array['postid'];
			$d = array();
			$d['pid']= $pid;
			$variations = frame::_()->getTable('variations');
			$variations->delete("variation_id = '".$pid."'");
			frame::_()->getModule('products')->getController()->getModel('products')->removeProduct($d);
			//$res->addData('variation', $fields);
			$res->addMessage(lang::_('Deleted'));
			$res->ajaxExec();
		}

		public function postVariation() {
			$res = new response();
			$post_array = req::get('post');
			$pid = $post_array['pid'];
			if($pid == -1)
				return;
			$post_id = $post_array['ID'];
			$new_post = array();
			foreach($post_array as $key => $field)
				$new_post[$key] = $field;
			unset($new_post['action']);
			unset($new_post['page']);
			unset($new_post['reqType']);
			unset($new_post['extra_field']);
			$method = '';
			if($pid == 0)
				$method = 'post';
			else {
				if(frame::_()->getTable('products')->exists($pid))
					$method = 'put';
				else
					$method = 'post';
			}
			if(empty($post_array['var_name']))
				$res->pushError(lang::_(array('Please enter', 'variation', 'name')), 'var_name');
			if(!$res->error()) {
				if($method=='post') {
					unset($new_post['ID']);
					$post = array(  
					  'post_status' => 'publish',
					  'post_title' => $new_post['var_name'], 
					  'post_parent' => $post_id,
					  'post_type' => S_VARIATION
					);  
					$inserted_id = wp_insert_post( $post );
					$variations_table = frame::_()->getTable('variations');
					$dbData = array(
						'product_id' => $post_id,
						'variation_id' => $inserted_id,
						'var_name' => $new_post['var_name'],
						'var_sort_order' => $new_post['var_sort_order'],
					);
					$variations_table->insert($dbData);
					$new_post['ID'] = $inserted_id;
					$new_post['post_id'] = $inserted_id;
					$this->getModel('products')->post( $new_post );
					if(frame::_()->getModule('options')->get('use_new_products_fields')) {
						frame::_()->getModule('products_fields')->getModel()->duplicateFromProduct($post_id, $inserted_id);
					} else {
						$extrafields = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField($post_id);
						if(!empty($extrafields)) {
							foreach($extrafields as $e_field) {
								$e_field->data['destination']['pids'][] = "".$inserted_id;
								$e_field->data['destination']['pids'] = array_unique($e_field->data['destination']['pids']);
								frame::_()->getModule('options')->getController()->putProductfieldVar($e_field->data);
							}
						}
					}
					$res->addData('var_id', $inserted_id);
					$res->addData('method', 'add');
				} elseif($method=='put') {
					unset($new_post['ID']);
					$new_post['ID'] = $pid;
					$variation_post = array();  
					$variation_post['ID'] = $new_post['ID'];  
					$variation_post['post_title'] = $new_post['var_name'];  
					wp_update_post( $variation_post );  
					$this->getModel('products')->put( $new_post );
					unset($new_post['ID']);
					$new_post['ID'] = $pid;
					$new_post['post_ID'] = $pid;
					$this->saveProductExtraField( $new_post );
					frame::_()->getTable('variations')->update(
						array(
							'var_name' => $new_post['var_name'], 
							'var_sort_order' => $new_post['var_sort_order']
						), 
						array('variation_id' => $pid));
					$res->addData('var_id', $pid);
					$res->addData('method', 'edit');
				}
				$res->addMessage(lang::_('Done'));
			}
			$res->ajaxExec();
		}
		public function getVariationPage() {
			$res = new response();
			$res->html =  frame::_()->getModule('products')->getView('admin_Products')->getVariationView();
			$res->ajaxExec();
		}
	   /**
		* Saves Extra Fields to Database
		* @param array $post 
		*/
		public function saveProductExtraField($post_data, $exPostId = NULL) {
			global $post;
			// Don't use this old method for new productfields
			if(frame::_()->getModule('options')->get('use_new_products_fields')) 
				return;
			if(empty($post) && !empty($exPostId))
				$post = $exPostId;
			//I don't know what this code do, realy, but leave it
			$metaFields = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField($post);
			//frame::_()->getModule('options')->getModel('extrafieldsvalue')->saveExtraFields($post_data, $metaFields, $post_data['post_ID'], 'products');
			//Below we will save exclude products for ex. options
			if(isset($post_data['exVal']) && !empty($post_data['exVal']) && is_array($post_data['exVal'])) {
				//frame::_()->getModule('options')->getModel('extraoptions')->saveExtraFields(array(), , $post_data['post_ID'], S_PRODUCT);
				//$deleteOpts = implode(',', array_keys($post_data['exVal']));
				//frame::_()->getTable('extrafieldsvalue')->delete('parent_id = '. $post_data['post_ID']. ' AND parent_type = "'. S_PRODUCT. '" AND opt_id IN ('. $deleteOpts. ')');
				foreach($post_data['exVal'] as $optId => $optData) {
					$dbData = array(
						'parent_id' => $post_data['post_ID'],
						'parent_type' => S_PRODUCT,
						'ef_id' => $post_data['exValuesToFields'][ $optId ],
						'opt_id' => $optId,
						'price' => $optData['price'],
						'price_absolute' => $optData['price_absolute'],
						'sort_order' => $optData['sort_order'],
						'disabled' => $optData['disabled'],
						'value' => '',  //can't be NULL
					);
					$optValId = (int)$optData['opt_val_id'];
					if($optValId)
						frame::_()->getTable('extrafieldsvalue')->update($dbData, array('id' => $optValId));
					else
						frame::_()->getTable('extrafieldsvalue')->insert($dbData);
				}
			}
			// Update text and textarea fields
			if(isset($post_data['exOptions']) 
				&& !empty($post_data['exOptions']) 
				&& is_array($post_data['exOptions'])
				&& !empty($metaFields)
			) {
				foreach($post_data['exOptions'] as $fId => $fValue) {
					$valueDbData = array(
						'ef_id' => $fId, 
						'value' => $fValue,
					);
					$dbOptData = array(
						'parent_id' => $post_data['post_ID'],
						'parent_type' => S_PRODUCT,
						'ef_id' => $fId,
						'price' => '0',
						'price_absolute' => '0',
						'sort_order' => '0',
						'disabled' => (int)(isset($post_data['exOptionsDisabled'][ $fId ]) && $post_data['exOptionsDisabled'][ $fId ]),
						'value' => '',  //can't be NULL
					);
					$optVal = frame::_()->getTable('extrafieldsvalue')->get('*', array(
						'ef_id' => $fId, 
						'parent_type' => S_PRODUCT,
						'parent_id' => $post_data['post_ID'],
						), '', 'row');
					if(!empty($optVal)) {
						if(!empty($optVal['opt_id'])) {
							frame::_()->getTable('extraoptions')->update($valueDbData, array('id' => $optVal['opt_id']));
							$dbOptData['opt_id'] = $optVal['opt_id'];
						} else {
							$dbOptData['opt_id'] = frame::_()->getTable('extraoptions')->insert($valueDbData);
						}
						frame::_()->getTable('extrafieldsvalue')->update($dbOptData, array('id' => $optVal['id']));
					} else {
						$dbOptData['opt_id'] = frame::_()->getTable('extraoptions')->insert($valueDbData);
						frame::_()->getTable('extrafieldsvalue')->insert($dbOptData);
					}
				}
			}
		}
		/**
		 * Add the fields that are specific to added category
		 */
		public function addCategoryFields() {
			$res = new response();
			$post = req::get('post');
			$id = $post['id'];
			$cats = array_unique($post['cats']);
			if (!is_numeric($id)) {
				$res->html = new errors('There is no such category in the system','Invalid category');
			}
			$fields = frame::_()->getModule('options')->getModel('productfields')->get();
			$result = '';
			if (!empty($fields)) {
				foreach ($fields as $f) {
					$destination = utils::jsonDecode($f['destination']);
					if (empty($destination)) {
						continue;
					}
					$common_cats = array_intersect($destination, $cats);
					$intersect = false;
					if (count($common_cats) == 1 and $common_cats[0] == $id) {
						$intersect = true;
					}
					if (in_array($id, $destination) && !in_array(0, $destination) && $intersect) {
						$item = new field($f['code'], $f['type'], 'other', '', $f['label']);
						$item->id = $f['id'];
						$item->mandatory = $f['mandatory'];
						$item->destination = $destination;
						$item->htmlParams = (array)json_decode($f['params']);
						$item->default_value = $f['default_value'];
						$output = '<div class="product_extra"><label for="'.$item->name.'">'.$item->label.':</label>';
						$result .= $output.'<div class="product_field">'.$item->viewField($f['code']).'</div><br clear="all" /></div>';
					}
				} 
			}
			$res->html = $result;
			$res->ajaxExec();
		}
		/**
		 * Delete the fields of a specific category
		 */
		public function deleteCategoryFields(){
		   $res = new response();
		   $post = req::get('post');
		   $id = $post['id']; 
		   if (!is_numeric($id)) {
				$res->html = new errors('There is no such category in the system','Invalid category');
		   }
		   $fields = frame::_()->getModule('options')->getModel('productfields')->get();
		   $result = array();
			if (!empty($fields)) {
				foreach ($fields as $f) {
					$destination = utils::jsonDecode($f['destination']);
					if (empty($destination)) {
						continue;
					}
					if (count($destination) == 1 && $destination[0] == $id) {
						$result[] = $f['code'];
					}
				}  
			}
		   $res->data = $result;
		   $res->ajaxExec();
		}

		public function updateProductMedia() {
		   $res = new response();
		   $post = req::get('post');
		   $id = $post['id']; 
		   if (!is_numeric($id)) {
				$res->html = new errors('There is no such category in the system','Invalid category');
		   }
		   $result = frame::_()->getModule('products')->getView('admin_Products')->updateMediaFiles($id);
		   $res->html = $result;
		   $res->ajaxExec();
		}
		public function showPrice($pid, $price) {
			return $this->getView()->getPrice($pid, $price);
		}
		public function setImgStatus() {
			$res = new response();
			$parentId = (int) req::getVar('parent_id');
			$postId = (int) req::getVar('post_id');
			$status = req::getVar('status');
			if($parentId && $postId && $status) {
				frame::_()->getTable('img_status')->delete(array(
					'parent_id' => $parentId,
					'post_id' => $postId,
				));
				if($status != 'all') {      //Just do nothing - it will not be in table, all is default value
					frame::_()->getTable('img_status')->insert(array(
						'parent_id' => $parentId,
						'post_id' => $postId,
						'status' => $status,
					));
				}
			} else 
				$res->pushError(lang::_('Invalid data was specified'));

			return $res->ajaxExec();
		}
		public function saveImagesSortOrder() {
			global $wpdb;
			$res = new response();
			$newSortOrder = req::getVar('newSortOrder');
			if(!empty($newSortOrder)) {
				$newSortOrder = explode(',', $newSortOrder);
				if(is_array($newSortOrder)) {
					foreach($newSortOrder as $i => $imgId) {
						db::query('UPDATE '. $wpdb->posts. ' SET menu_order = '. (int)$i. ' WHERE ID = '. (int)$imgId. ' LIMIT 1');
					}
				}
			}
			return $res->ajaxExec();
		}
		public function getCategoriesListHtml() {
			add_filter('the_content', array($this->getView(), 'getCategoriesListHtml'));
		}
		public function getBrandsListHtml() {
			add_filter('the_content', array($this->getView(), 'getBrandsListHtml'));
		}
		public function getAllProductsListHtml() {
			add_filter('the_content', array($this->getView(), 'getAllProductsListHtml'));
		}
		public function saveSortOrder() {
			$res = new response();
			$sortOrderData = req::getVar('sort_order');
			if(!empty($sortOrderData) && is_array($sortOrderData)) {
				foreach($sortOrderData as $pid => $order) {
					if(is_numeric($order) && is_numeric($pid)) {
						frame::_()->getTable('products')->update(array('sort_order' => (int)$order), array('post_id' => (int)$pid));
					} else
						$res->pushError (lang::_('Invalid sort order number for pid'). ' '. $pid);
				}
			} else
				$res->pushError (lang::_('Nothing to sort'));
			return $res->ajaxExec();
		}

		public function addNewCopy() {
			$pid = (int) req::getVar('post_id');
			$res = new response();
			
			if($pid) {
				$fields = frame::_()->getModule('products')->getController()->getModel('products')->get($pid);
				$post_prev = get_post($pid);
				$post = array(
					'post_title' => $post_prev->post_title.' Copy',
					'post_status' => 'publish',
					'post_content' => $post_prev->post_content,
					'post_excerpt' => $post_prev->post_excerpt,
					'post_type' => 'product',
					'post_author' => $post_prev->post_author,
					'categories' => frame::_()->getModule('products')->getModel()->getCategories($pid)
				);
				foreach($fields as $f) {
					if(in_array($f->name, array('post_id', 'ID', 'id'))) continue;	// Don't allow to copy post ID to new product
					$post[$f->name] = $f->value;
				}

				$post_id = wp_insert_post( $post );

				$productcategories = wp_get_object_terms($pid, 'products_categories');  
				foreach($productcategories as $pc) {
					wp_set_object_terms($post_id,$pc->name,'products_categories',true);
				}

				$products_brands = wp_get_object_terms($pid, 'products_brands');  
				foreach($products_brands as $pb) {
					wp_set_object_terms($post_id,$pb->name,'products_brands',true);
				}

				$post_tag = wp_get_object_terms($pid, 'post_tag');  
				foreach($post_tag as $pt) {
					wp_set_object_terms($post_id,$pt->name,'post_tag',true);
				}
				// Copy all images
				$post_images = $this->getView()->getProductImages($pid);
				if(!empty($post_images)) {
					foreach($post_images as $img_by_types) {
						if(!empty($img_by_types)) {
							foreach($img_by_types as $img) {
								if(isset($img['thumb']) && is_object($img['thumb'])) {
									$img_post_data = (array) $img['thumb'];
									$img_post_data['post_parent'] = $post_id;
									wp_insert_post( $img_post_data );
								}
							}
						}
					}
				}
				if(frame::_()->getModule('options')->get('use_new_products_fields')) {
					frame::_()->getModule('products_fields')->getModel()->duplicateFromProduct($pid, $post_id);
				}
				$res->addData("copypost_id", $post_id);
			} else {
				$res->pushError(lang::_('Invalid product ID'));
			}
			return $res->ajaxExec();
		}

		public function getPostVariation() {
			global $post;
			$res = new response();
			$variationId = (int) req::getVar('variation_id');
			$parentId = (int) req::getVar('parent_id');
			if($variationId)	// If variation - load content for variation
				$res->setHtml( $this->getView()->getPostFullContent(array('p' => $variationId, 'post_type' => S_VARIATION)) );
			else				// If product - load content for product
				$res->setHtml( $this->getView()->getPostFullContent(array('p' => $parentId, 'post_type' => S_PRODUCT)) );
			return $res->ajaxExec();
		}
		
		public function getAllProducts(){
			$res = new response();
			$products = $this->getModel()->getAllProducts();
			if(empty($products)){
				$res->addMessage(lang::_('There are no products'));
			}else{
				$res->addData("products",$products);
			}
			return $res->ajaxExec();
		}
	} 
