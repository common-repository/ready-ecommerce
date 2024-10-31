<?php
class productsModel extends model {
	protected $_availableProdFields = array(
		/**
         * WP post data
         */
		'tax_input_categories' => array('colName' => 'Categories'),
        'tax_input_brands' => array('colName' => 'Brands'),
        'post_content' => array('colName' => 'Description'),
        'post_excerpt' => array('colName' => 'Short Description'),
        'post_name' => array('colName' => 'Alias'),
        'post_title' => array('colName' => 'Title'),
        'tags_input' => array('colName' => 'Tags'),
		'post_status' => array('colName' => 'Post Status'),
        /**
         * TOE post data
         */
        'price' => array('colName' => 'Price'),
        'weight' => array('colName' => 'Weight'),
        'sku' => array('colName' => 'SKU'),
        'quantity' => array('colName' => 'Quantity'),
        'featured' => array('colName' => 'Featured'),
		'width'  => array('colName' => 'Width'),
		'height'  => array('colName' => 'Height'),
		'length'  => array('colName' => 'Length'),
        
        'parameters' => array('colName' => 'Parameter:[Parameter name]'),
	);
	
	protected $_standardAddProdDataDelimiters = array(
		'categoriesDelimiter' => '|',
		'brandsDelimiter' => '|',
		'tagsDelimiter' => '|',
	);
	
	public function getVariation($d = array()) {
        $post_id = 0;
		$pid = 0;
		$post_id = $d['post_id'];
		
		$products_table = frame::_()->getTable('products');
		$variations_table = frame::_()->getTable('variations');
		
		$where_prod = array();
		$where_prod['post_id'] = $post_id;
		
		$where_var = array();
		$where_var['variation_id'] = $post_id;
		
		
		$fields_prod = $products_table->get('*', $where_prod);
		$fields_var  = $variations_table->get('*', $where_var);
		
		foreach($fields_prod as $key => $f) {
			if(!isset($fields_var[$key]['var_name']) || ($fields_var[$key]['var_name'] == ''))
				$fields_prod[$key]['var_name'] = 'Noname variation';
			else
				$fields_prod[$key]['var_name'] = $fields_var[$key]['var_name'];
			if(isset($fields_var[$key]['var_sort_order']))
				$fields_prod[$key]['var_sort_order'] = $fields_var[$key]['var_sort_order'];
			else
				$fields_prod[$key]['var_sort_order'] = 0;
		}
		return $fields_prod[0];
    }
    public function getVariations($d = array()) {
        $post_id = 0;
        if(is_numeric($d))
            $post_id = $d;
        elseif(is_object($d))
            $post_id = $d->ID;
		$products_table = frame::_()->getTable('products');
		$variations_table = frame::_()->getTable('variations');
		$where = array();
		$where['product_id'] = $post_id;
		$fields = $variations_table->orderBy('var_sort_order')->get('*', $where);
		$result_fields = array();
		foreach($fields as $f) {
			$var_id = $f['variation_id'];
			$where = array();
			$where['post_id'] = $var_id;
			$variation = $products_table->get("id,post_id",$where);
			foreach($variation as $key => $v) {
				if(!isset($f['var_name']) || ($f['var_name'] == ''))
					$variation[$key]['var_name'] = 'Noname variation';
				else
					$variation[$key]['var_name'] = $f['var_name'];
				if(isset($f[$key]['var_sort_order']))
					$variation[$key]['var_sort_order'] = $f[$key]['var_sort_order'];
				else
					$variation[$key]['var_sort_order'] = 0;
			}
			$result_fields[] = $variation;
		}
		return $result_fields;
    }
    public function get($d = array()) {
        $post_id = 0;
        $where = '';
        if(is_numeric($d))
            $post_id = $d;
        elseif(is_object($d))
            $post_id = $d->ID;
        elseif(isset($d['ids']) && is_array($d['ids']))
            $where = 'post_id IN ('. implode(',', $d['ids']). ')';
        if($post_id || $where) {
            frame::_()->getTable('products')->fillFromDB($post_id, $where);
        } else {
            $getFields = (is_array($d) && isset($d['getFields'])) ? $d['getFields'] : '*';
			$where = $d;
			if(!is_array($where))
				$where = array();
			if(isset($where['additionalCondition']) && !empty($where['additionalCondition']))
				$where['additionalCondition'] .= ' AND ';
			else
				$where['additionalCondition'] = '';
			$where['additionalCondition'] .= 'post.post_type = "'. S_PRODUCT. '"';
			if($where['activeOnly']) {
				$where['additionalCondition'] .= ' AND post.post_status != "trash"';
			}
			if(isset($d['orderBy']) && $d['orderBy'] && is_array($d))
				frame::_()->getTable('products')->orderBy($d['orderBy']);
            return frame::_()->getTable('products')->
                    addJoin(array('tbl' => '#__posts', 'a' => 'post', 'on' => 'ID'))->
                    get($getFields, $where);
        }
        $item = frame::_()->getTable('products')->getFields();
        if (empty($item['sku']->value)) {
            $generate = frame::_()->getModule('options')->getModel()->get(array('code'=>'autofill_sku'));
            if ($generate) {
                $item['sku']->value = $post_id;
            }
        }
        return $item;
    }
    public function put($d = array()) {
        $data = array();
        $table = frame::_()->getTable('products');
        $fields = $table->getFields();
        foreach($fields as $f) {    /*Here should be all validations, maybe deliver this in abstract part? Thoughts?*/
            if(isset($d[$f->name]))
                $data[$f->name] = $d[$f->name];
        }
        $checkboxes = array('featured', 'mark_as_new');
		foreach($checkboxes as $cKey) {
			if(isset($data[$cKey]))
				$data[$cKey] = 1;
			else
				$data[$cKey] = 0;
		}

        if(empty($data['views']))
            $data['views'] = 0;
        $table->update($data, array('post_id' => $d['ID']));
    }
    public function post($d = array()) {
        $data = array(
            'post_id' => $d['ID'],
        );
        $table = frame::_()->getTable('products');
        $fields = $table->getFields();
        foreach($fields as $f) {    /*Here should be all validations, maybe deliver this in abstract part? Thoughts?*/
            if(isset($d[$f->name]))
                $data[$f->name] = $d[$f->name];
        }
		$checkboxes = array('featured', 'mark_as_new');
		foreach($checkboxes as $cKey) {
			if(isset($data[$cKey]))
				$data[$cKey] = 1;
			else
				$data[$cKey] = 0;
		}
        if(!isset($data['views']) || empty($data['views']))
            $data['views'] = 0;
        if(!$table->insert($data)) {
            //must be some validation here
        }
    }
    /**
     * Returns categories of the given product
     * 
     * @param object $post
     * @return array 
     */
    public function getProductTerms($post, $returnKey = 'term_id') {
        $categories = array();
        $pid = 0;
        if(is_object($post))
            $pid = $post->ID;
        elseif(is_numeric($post))
            $pid = (int)$post;
        if($pid) {
            $terms = wp_get_post_terms($pid, 'products_categories', array());
            $categories[] = 0;
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $categories[] = $term->$returnKey;
                }
            }
        }
        return $categories;
    }
    /**
     * Get data for the product extra fields block
     * 
     * @param object $d
     * @return array 
     */
    public function getExtraContent($d = array()) {
        $post_id = 0;
        $where = '';
        if (is_object($d)) {
            $post_id = $d->ID;
        } else {
            return 'Invalid post';
        }
        $categories = $this->getProductTerms($d);
        $extra_fields = frame::_()->getTable('extrafields');
        $extra_values = frame::_()->getTable('extrafieldsvalue');
        $extra_options = frame::_()->getTable('extraoptions');
        $extra_values->innerJoin($extra_fields,'ef_id');
        $conditions = $extra_values->alias().'.parent_id ='. $d->ID.' AND '.$extra_values->alias().'.parent_type = "products"';
        $items = $extra_values->get('ef_id,value,label,code,htmltype_id, destination',$conditions);
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                /**
                 * Look for allowed categories to display the field
                 */
                $allowed_cats = utils::jsonDecode($item['destination']);
                // if there are no allowed categories don't display field
                if (empty($allowed_cats)) continue;
                /**
                 * Check if the product is in allowed category
                 */
                $field_cat = array_intersect($allowed_cats, $categories);
                // if product is not in allowed categories don't display field
                if (empty($field_cat)) continue;
                $result[$item['code']] = new field($item['code'],'text','text','',$item['label']);
                if (is_numeric($item['value'])) {
                    $conditions = array(
                        'ef_id' => $item['ef_id'],
                        'id'=> $item['value'],
                    );
                    $option = $extra_options->get('value',$conditions);
                    if (!empty($option)) {
                        $result[$item['code']]->value = $option[0]['value'];
                    } else {
                        if ($item['htmltype_id'] == 4) {
                            if ($item['value'] == '') {
                                $result[$item['code']]->value = lang::_('No');
                            } else {
                                $result[$item['code']]->value = lang::_('Yes');
                            }
                        } else {
                            $result[$item['code']]->value = $item['value'];
                        }
                    }
                } else {
                    $result[$item['code']]->value = $item['value'];
                }
            }
        }
        return $result;

    }
    public function getCategories($d = array()) {
        $pid = 0;
        if(is_numeric($d)) 
            $pid = $d;
        elseif(isset($d['pid']))
            $pid = $d['pid'];
        elseif(isset($d['post_id']))
            $pid = $d['post_id'];
        if($pid) {
            return $this->_cattsForPost($pid, products::CATEGORIES);
        }
        else
            return false;
    }
    public function getBrands($d = array()) {
        $pid = 0;
        if(is_numeric($d)) 
            $pid = $d;
        elseif(isset($d['pid']))
            $pid = $d['pid'];
        elseif(isset($d['post_id']))
            $pid = $d['post_id'];
        if($pid) {
            return $this->_cattsForPost($pid, products::BRANDS);
        }
        else
            return false;
    }
    protected function _cattsForPost($pid, $type) {
        $res = array();
        $catts = db::get('SELECT tr.term_taxonomy_id, t.name FROM #__term_taxonomy tt 
            INNER JOIN #__terms t ON t.term_id = tt.term_id
            INNER JOIN #__term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.taxonomy = "'. $type. '" AND tr.object_id = "'. (int)$pid. '"');
        if(!empty($catts)) {
            foreach($catts as $c) {
                $res[$c['term_taxonomy_id']] = $c['name'];
            }
        }
        return $res;
    }
    public function postsToCategories($d = array()) {
        $res = array();
        $dbData = db::get('SELECT tr.term_taxonomy_id, t.name, tr.object_id FROM #__term_taxonomy tt 
            INNER JOIN #__terms t ON t.term_id = tt.term_id
            INNER JOIN #__term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.taxonomy = "'. products::CATEGORIES. '"');
        if(!empty($dbData)) {
            foreach($dbData as $c) {
                $res[$c['term_taxonomy_id']]['products'][] = $c['object_id'];
            }
        }
        return $res;
    }
    public function validateSave($d = array()) {
        if(!is_numeric($d['price'])) {
            $this->pushError(lang::_('Invalid Price'));
        }
        return !($this->haveErrors()); //true - if no errors, if have some errors - false
    }
	public function getImages($d = array('pid' => 0)) {
		if(!empty($d['pid'])) {
			$exclude = frame::_()->getTable('content_img')->get('img_id', array('pid' => $d['pid']), '', 'col');
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $d['pid'],
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'exclude' => $exclude
			);
			return get_posts($args);
		}
		return false;
	}
	public function getCustomFields($d = array('pid' => 0)) {
		$res = array();
		$customFields = get_post_custom($d['pid']);
		if(!empty($customFields) && is_array($customFields)) {
			foreach($customFields as $k => $v) {
				if(strpos($k, '_') === 0) continue;
				$value = $v;
				if(!is_array($value))
					$value = array($value);
				$res[ $k ] = $value;
			}
		}
		return $res;
	}
	public function getAvailableProdFields() {
		return $this->_availableProdFields;
	}
	/**
	 * Create product from provided data
	 */
	public function addProduct($d = array()) {
		if(!empty($d) && is_array($d)) {
			$postData = array();
			$parameters = array();
			$delimiters = isset($d['toeDelimiters']) ? $d['toeDelimiters'] : $this->_standardAddProdDataDelimiters;
			//$availableFields = $this->getAvailableProdFields();
			$currentUserId = frame::_()->getModule('user')->getCurrentID();
			foreach($d as $f => $val) {
				if($f == 'parameters') {
					$parameters = $val;
				} elseif($f == 'tax_input_categories') {
					$data = $val;
					if(!is_array($data))
						$data = array_map('trim', explode($delimiters['categoriesDelimiter'], $data));
					if(!empty($data)) {
						$termsIds = array();
						foreach($data as $catName) {
							$term = get_term_by('name', $catName, frame::_()->getModule('products')->getConstant('CATEGORIES'));
							if($term) {
								$termsIds[] = $term->term_id;
							} else {
								$newCategory = wp_insert_term($catName, frame::_()->getModule('products')->getConstant('CATEGORIES'), array());
								if(!empty($newCategory['term_id']))
									$termsIds[] = $newCategory['term_id'];
							}
						}
						if(!empty($termsIds))
							$postData['tax_input'][ frame::_()->getModule('products')->getConstant('CATEGORIES') ] = $termsIds;
					}
				} elseif($f == 'tax_input_brands') {
					$data = $val;
					if(!is_array($data))
						$data = array_map('trim', explode($delimiters['brandsDelimiter'], $data));
					if(!empty($data)) {
						$termsIds = array();
						foreach($data as $catName) {
							$term = get_term_by('name', $catName, frame::_()->getModule('products')->getConstant('BRANDS'));
							if($term) {
								$termsIds[] = $term->term_id;
							} else {
								$newCategory = wp_insert_term($catName, frame::_()->getModule('products')->getConstant('BRANDS'), array());
								if(!empty($newCategory['term_id']))
									$termsIds[] = $newCategory['term_id'];
							}
						}
						if(!empty($termsIds))
							$postData['tax_input'][ frame::_()->getModule('products')->getConstant('BRANDS') ] = $termsIds;
					}
				} elseif($f == 'tags_input') {
					$data = $val;
					if(!empty($data)) {
						$data = trim(str_replace($delimiters['tagsDelimiter'], ',', $data));
						$postData[$f] = $data;
					}
				} else 
					$postData[$f] = $val;
			}
			if(!empty($postData)) {
				$postData['post_type'] = S_PRODUCT;
				if(empty($postData['post_status']))
					$postData['post_status'] = 'publish';
				if(empty($postData['post_author']))
					$postData['post_author'] = $currentUserId;
				$pid = wp_insert_post($postData);
				if($pid) {
					if(!empty($parameters)) {
						foreach($parameters as $pDataFromInput) {
							$pData = $pDataFromInput;
							if(!empty($pData)) {
								if(is_numeric($pData['htmltype'])) {
									$pData['htmltype_id'] = (int) $pData['htmltype'];
								} else {
									$pData['htmltype_id'] = (int) frame::_()->getTable('htmltype')->get('id', array('label' => $pData['htmltype']), '', 'one');
								}
								$parameterData = frame::_()->getModule('options')->getModel('productfields')->get(array(), 
										frame::_()->getTable('extrafields')->alias(). '.label = "'. $pData['label']. '" AND '. frame::_()->getTable('extrafields')->alias(). '.htmltype_id = '. $pData['htmltype_id']. ' AND ');
								if(empty($parameterData)) {
									if(!isset($pData['code']))
										$pData['code'] = '';	// It will be generated later, @see function prepareParams()
									$pData['ignoreOptionsInsert'] = true;	// To not insert EMPTY value, @see extrafieldsModel::post()
									$res = frame::_()->getModule('options')->getModel('productfields')->post($pData);
									// Select just created Product Field
									$parameterData = frame::_()->getModule('options')->getModel('productfields')->get(array(), 
										frame::_()->getTable('extrafields')->alias(). '.label = "'. $pData['label']. '" AND '. frame::_()->getTable('extrafields')->alias(). '.htmltype_id = '. (int)$pData['htmltype']. ' AND ');
								}
								if(!empty($parameterData)) {
									$parameterData = end($parameterData);     //Only one param must be here
									$options = $pData['options'];
									/*for($j = 0; $j < count($options); $j++) {
										//$options[$j] = explode($csvParams['optsValuesDelimiter'], $options[$j]);
										$options[$j] = array(
											'value' => $options[$j][0],
											'price' => isset($options[$j][1]) ? $options[$j][1] : 0,
											'absolute' => isset($options[$j][2]) ? $options[$j][2] : 0,
										);
									}*/
									$updateOptsForProducts = array();
									foreach($options as $opt) {
										$optFound = false;
										$optId = 0;
										foreach($parameterData['opt_values'] as $optI => $definedOpt) {
											if($definedOpt['value'] == $opt['value']) {
												if(!empty($definedOpt['data']['onlyForPids']) && is_array($definedOpt['data']['onlyForPids'])) {
													$definedOpt[ $optI ]['data']['onlyForPids'][] = $pid;
													$optId = $definedOpt['id'];
													frame::_()->getTable('extraoptions')->update(array('data' => utils::jsonEncode($definedOpt[ $optI ]['data'])), $definedOpt[ $optI ]['id']);
													$parameterData['opt_values'][$optI]['doNotDisableMe'] = true;
												}
												$optFound = true;
											}
										}
										if(!$optFound) {
											$newOptId = frame::_()->getTable('extraoptions')->insert(array(
												'ef_id' => $parameterData['id'],
												'value' => $opt['value'],
												'data' => utils::jsonEncode(array('onlyForPids' => array($pid)))));
											if($newOptId) {
												$optId = $newOptId;
												$parameterData['opt_values'][$newOptId] = array(
													'id' => $newOptId,
													'ef_id' => $parameterData['id'],
													'value' => $opt['value'],
													'data' => array('onlyForPids' => array($pid)),
													'doNotDisableMe' => true,
												);
												$optFound = true;
											}
										}
										if($optFound) {
											$updateOptsForProducts[ $optId ] = array('optId' => $optId, 'price' => $opt['price'], 'absolute' => $opt['absolute']);
										}
									}
									$postDataForProd = array('post_ID' => $pid);
									foreach($parameterData['opt_values'] as $optId => $opt) {
										if(isset($updateOptsForProducts[ $optId ])) {
											$postDataForProd['exVal'][ $optId ] = array(
												'price' => $updateOptsForProducts[ $optId ]['price'],
												'price_absolute' => (int) $updateOptsForProducts[ $optId ]['absolute'],
												'disabled' => 0,
											);
										} else {
											$postDataForProd['exVal'][ $optId ] = array(
												'price' => 0,
												'price_absolute' => 0,
												'disabled' => 1,
											);
										}
										$postDataForProd['exValuesToFields'][ $optId ] = $parameterData['id'];
									}
									frame::_()->getModule('products')->getController()->saveProductExtraField($postDataForProd);

									if(empty($parameterData['destination']['pids'])) 
										$parameterData['destination']['pids'] = array();
									$parameterData['destination']['pids'][] = $pid;
									db::query("UPDATE ". frame::_()->getTable('extrafields')->getTable(). " SET destination = '". utils::jsonEncode($parameterData['destination']). "' WHERE id = '". (int)$parameterData['id']. "' LIMIT 1");
								}
							}
						}
					}
					if(isset($d['imgs'])) {
						if(!is_array($d['imgs'])) {
							$d['imgs'] = array($d['imgs']);
						}
						foreach($d['imgs'] as $imgInputData) {
							$imgData = $imgInputData;
							if(!isset($imgData['pid']))
								$imgData['pid'] = $pid;
							$this->addImg($imgData);
						}
					}
					return $pid;
				}
			} else 
				$this->pushError (lang::_('Not enought data to create product'));
		} else
			$this->pushError (lang::_('Invalid data provided'));
	}
	public function addImg($d = array()) {
		if(isset($d['filename']) 
			&& !empty($d['filename']) 
			&& file_exists($d['filename']) 
			&& isset($d['pid'])
		) {
			$baseFileName = basename($d['filename']);
			$filename = $d['filename'];
			$wp_filetype = wp_check_filetype(basename($d['filename']), null );
			$wp_upload_dir = wp_upload_dir();
			if(strpos($wp_upload_dir['basedir'], $d['filename']) === false) {	// If image is not in uploads dir - copy it there
				$filename = $wp_upload_dir['path']. '/'. $baseFileName;
				copy($d['filename'], $filename);
			}
			remove_action('add_attachment', array(frame::_()->getModule('products'), 'addContentImg'));
			$attach_id = wp_insert_attachment(array(
					'guid' => $wp_upload_dir['url'] . '/' . $baseFileName, 
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', $baseFileName),
					'post_content' => '',
					'post_status' => 'inherit'
				 ), $filename, $d['pid']);
			if($attach_id) {
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = @wp_generate_attachment_metadata( $attach_id, $filename );
				@wp_update_attachment_metadata( $attach_id, $attach_data );
				return $attach_id;
			}
		}
		return false;
	}
	public function removeProduct($d = array()) {
		if(isset($d['pid']) && is_numeric($d['pid'])) {
			$attachments = get_posts(array(
				'post_type' => 'attachment', 
				'numberposts' => -1, 
				'post_status' =>'any', 
				'post_parent' => $d['pid'],
			));
			if(!empty($attachments) && is_array($attachments)) {
				foreach($attachments as $attach) {
					$this->removeAttachment(array('pid' => $attach->ID));
				}
			}
			wp_delete_post($d['pid'], true);
			return true;
		}
		return false;
	}
	public function removeAttachment($d = array()) {		
		if(isset($d['pid']) && is_numeric($d['pid'])) {
			wp_delete_attachment($d['pid'], true);
			return true;
		}
		return false;
	}
	/**
	 * For Categories or brands
	 */
	public function addTax($d = array()) {
		$catId = 0;
		$d['name'] = isset($d['name']) ? $d['name'] : '';
		$d['taxonomy'] = isset($d['taxonomy']) ? $d['taxonomy'] : '';
		$res = wp_insert_term($d['name'], $d['taxonomy'], $d);
		if(is_wp_error($res)) {
			if(isset($res->errors))
				$this->pushError($res->errors);
			if(isset($res->error_data) 
				&& isset($res->error_data['term_exists']) 
				&& !empty($res->error_data['term_exists'])
			) {
				$catId = $res->error_data['term_exists'];
			}
		}
		
		if(is_array($res) && isset($res['term_id']))
			$catId = $res['term_id'];
		
		if($catId) {
			// In usual updating of category this is event listener, but here let's call it permanently
			switch($d['taxonomy']) {
				case frame::_()->getModule('products')->getConstant('CATEGORIES'):
					// In usual updating of category this is event listener, but here let's call it permanently
					frame::_()->getModule('products')->toe_saveProductsCategories($catId, 0, $d);
					break;
				case frame::_()->getModule('products')->getConstant('BRANDS'):
					// In usual updating of brand this is event listener, but here let's call it permanently
					frame::_()->getModule('products')->toe_saveProductsBrands($catId, 0, $d);
					break;
			}
		}
		return $catId;
	}
	public function getAllProducts(){
		return  frame::_()->getModule('products')->getProductsPosts();
	}
	public function removeTax($d = array()) {
		$res = wp_delete_term($d['term_id'], $d['taxonomy'], $d);
		if(is_wp_error($res))
			return false;
		return true;
	}
}
