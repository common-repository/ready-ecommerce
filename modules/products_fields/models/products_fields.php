<?php
class products_fieldsModel extends model {
	public function saveProductField($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : '';
		$htmlTypeId = isset($d['htmltype_id']) ? (int) $d['htmltype_id'] : 0;
		if(!empty($d['label'])) {
			$id = isset($d['id']) ? (int) $d['id'] : 0;
			$pid = isset($d['pid']) ? (int) $d['pid'] : 0;
			$originalId = isset($d['original_id']) ? (int) $d['original_id'] : 0;
			if($pid && !$originalId && $id) {	// Duplicate it for product
				$originalId = $id;
				$id = $this->duplicateField($originalId, $pid, $d);
				$newFieldData = $this->getById($id);
				$newFieldData['default_value'] = isset($d['default_value']) ? $d['default_value'] : $newFieldData['default_value'];
				$d = $newFieldData;
			}
			if($pid) {
				$d['products'] = array($pid);
			}
			$update = $id ? true : false;
			$defaultValue = isset($d['default_value']) ? $d['default_value'] : '';
			$saveData = array(
				'label'			=> $d['label'],
				'htmltype_id'	=> $htmlTypeId,
				'default_value' => $defaultValue,
				'mandatory'		=> (isset($d['mandatory']) ? (int) $d['mandatory'] : 0),
				'active'		=> (isset($d['active']) ? (int) $d['active'] : 0),
				'original_id'	=> $originalId,
				'sort_order'	=> (isset($d['sort_order']) ? (int) $d['sort_order'] : 0),
			);
			if($update)
				frame::_()->getTable('products_fields')->update($saveData, array('id' => $id));
			else
				$id = frame::_()->getTable('products_fields')->insert($saveData);
			if($id) {
				$this->updateProductFieldToCategoriesProducts($id, $d);
				$this->updateProductFieldOptions($id, $d);
				return $id;
			} else {
				$this->pushError (lang::_('Database error'));
				$this->pushError(frame::_('products_fields')->getTable('products_fields')->getErrors());
			}
		} else
			$this->pushError (lang::_('Please enter Label'));
		return false;
	}
	public function updateProductFieldOptions($fieldId, $d) {
		if(in_array($d['htmltype_id'], array(1, 17))) {	// Text, Textarea
			frame::_()->getTable('products_fields_options')->delete(array('products_field_id' => $fieldId));
		} else {
			if(isset($d['options']) && is_array($d['options']) && !empty($d['options'])) {
				// Array to save options ID and Labels to update default value for field then
				$optsLabelsIds = array();
				$updatedOptIds = array();
				$oldOptIds = $this->getOptionsForField($fieldId);
				foreach($d['options'] as $opt) {
					$optLabel = trim($opt['label']);
					if(!empty($optLabel)) {
						$optId = isset($opt['id']) ? (int) $opt['id'] : 0;
						$update = $optId ? true : false;
						$saveData = array(
							'products_field_id' => $fieldId,
							'label'				=> $optLabel,
							'price'				=> (isset($opt['price']) ? (double) $opt['price'] : 0),
							'absolute'			=> (isset($opt['absolute']) ? (int) $opt['absolute'] : 0),
							'sort_order'		=> (isset($opt['sort_order']) ? (int) $opt['sort_order'] : 0),
						);
						if($update) {
							frame::_()->getTable('products_fields_options')->update($saveData, array('id' => $optId));
							$updatedOptIds[] = $optId;
						} else
							$optId = frame::_()->getTable('products_fields_options')->insert($saveData);
						$optsLabelsIds[$optLabel] = $optId;
					}
				}
				if(isset($d['default_value']) && !empty($d['default_value']) && !empty($optsLabelsIds)) {
					$newDefaultValue = '';
					if(is_array($d['default_value'])) {
						$newDefaultValueArr = array();
						foreach($d['default_value'] as $defaultValueLabel) {
							if(isset($optsLabelsIds[ $defaultValueLabel ]))
								$newDefaultValueArr[] = $optsLabelsIds[ $defaultValueLabel ];
						}
						$newDefaultValue = utils::serialize($newDefaultValueArr);
					} elseif(isset($optsLabelsIds[ $d['default_value'] ])) {
						$newDefaultValue = $optsLabelsIds[ $d['default_value'] ];
					}
					frame::_()->getTable('products_fields')->update(array(
						'default_value' => $newDefaultValue,
					), array('id' => $fieldId));
				}
				if(!empty($oldOptIds)) {
					foreach($oldOptIds as $oldOpt) {
						if(!in_array($oldOpt['id'], $updatedOptIds)) {	// Option was not in request data - it was removed, let's remove it from db
							frame::_()->getTable('products_fields_options')->delete(array('id' => $oldOpt['id']));
						}
					}
				}
			} else
				frame::_()->getTable('products_fields_options')->delete(array('products_field_id' => $fieldId));	// Remove all options from this field if they was not present in request
		}
	}
	public function updateProductFieldToCategoriesProducts($fieldId, $d = array()) {
		// Clear at first
		frame::_()->getTable('products_fields_to_categories')->delete($fieldId);
		frame::_()->getTable('products_fields_to_products')->delete($fieldId);
		// Then - save
		if(isset($d['categories']) && !empty($d['categories'])) {
			if(!is_array($d['categories']))
				$d['categories'] = array($d['categories']);
			
			$valuesQueryArr = array();
			foreach($d['categories'] as $cid) {
				$valuesQueryArr[] = '('. $fieldId. ', '. $cid. ')';
			}
			$query = 'INSERT INTO @__products_fields_to_categories (products_field_id, category_id) VALUES '. implode(', ', $valuesQueryArr);
			db::query($query);
		}
		if(isset($d['products']) && !empty($d['products'])) {
			if(!is_array($d['products']))
				$d['products'] = array($d['products']);
			
			$valuesQueryArr = array();
			foreach($d['products'] as $pid) {
				$valuesQueryArr[] = '('. $fieldId. ', '. $pid. ')';
			}
			$query = 'INSERT INTO @__products_fields_to_products (products_field_id, product_id) VALUES '. implode(', ', $valuesQueryArr);
			db::query($query);
		}
	}
	public function getById($id) {
		$listData = $this->getList(array('id' => $id));
		if(!empty($listData))
			return array_shift($listData);
		return false;
	} 
	public function getList($d = array()) {
		$prodFields = array();
		$where = '';
		$whereArr = array();
		if(!empty($d)) {
			if(isset($d['id']))
				$whereArr[] = '@__products_fields.id = "'. (int)$d['id']. '"';
			if(isset($d['pid'])) { 
				$productCond = '@__products_fields_to_products.product_id = "'. (int)$d['pid']. '"';
				if(isset($d['categories'])) 
					$productCond .= ' OR @__products_fields_to_categories.category_id IN ('. implode(', ', $d['categories']). ')';
				$whereArr[] = '('. $productCond. ')';
				
				/*$whereArr[] = 'IF(
					EXISTS(SELECT id FROM @__products_fields check_pf WHERE check_pf.original_id = @__products_fields.id AND @__products_fields.original_id = 0)
					AND 
					(
						EXISTS(SELECT products_field_id FROM @__products_fields_to_products check_pf_to_p WHERE check_pf_to_p.products_field_id = @__products_fields_to_products.products_field_id) OR
						EXISTS(SELECT products_field_id FROM @__products_fields_to_categories check_pf_to_c WHERE check_pf_to_c.products_field_id = @__products_fields_to_categories.products_field_id)
					), 
					0, 
					1)';*/
			} elseif(isset($d['categories'])) 
				$whereArr[] = '@__products_fields_to_categories.category_id IN ('. implode(', ', $d['categories']). ')';
			if(isset($d['original_id']))
				$whereArr[] = '@__products_fields.original_id = "'. (int)$d['original_id']. '"';
			if(isset($d['active']))
				$whereArr[] = '@__products_fields.active = "'. (int)$d['active']. '"';
		}
		if(!empty($whereArr))
			$where = ' WHERE '. implode(' AND ', $whereArr). ' ';
		$order = ' ORDER BY @__products_fields.sort_order, @__products_fields.id';
		$query = 'SELECT 
				@__products_fields.*,
				@__products_fields_to_categories.category_id,
				@__products_fields_to_products.product_id,
				@__htmltype.label AS htmltype
			FROM @__products_fields 
			LEFT JOIN @__products_fields_to_categories ON @__products_fields_to_categories.products_field_id = @__products_fields.id
			LEFT JOIN @__products_fields_to_products ON @__products_fields_to_products.products_field_id = @__products_fields.id
			INNER JOIN @__htmltype ON @__htmltype.id = @__products_fields.htmltype_id'. $where. $order;
		$prodFieldsDb = db::get($query);
/*var_dump(mysql_error());
var_dump(db::$query);*/
		if(!empty($prodFieldsDb)) {
			$optsAlreadySet = array();
			$iters = array();
			$i = 0;
			$originalIds = array();
			foreach($prodFieldsDb as $f) {
				$id = $f['id'];
				if(!isset($iters[ $id ])) {
					$iters[ $id ] = $i++;
				}
				$iter = $iters[ $id ];
				if(!isset($prodFields[ $iter ])) {
					$prodFields[ $iter ] = $f;
				}
				if(!isset($prodFields[ $iter ]['categories']))
					$prodFields[ $iter ]['categories'] = array();
				if(!isset($prodFields[ $iter ]['products']))
					$prodFields[ $iter ]['products'] = array();
				if(isset($f['category_id']) && !empty($f['category_id']) && !in_array($f['category_id'], $prodFields[ $iter ]['categories']))
					$prodFields[ $iter ]['categories'][] = $f['category_id'];
				if(isset($f['product_id']) && !empty($f['product_id']) && !in_array($f['product_id'], $prodFields[ $iter ]['products']))
					$prodFields[ $iter ]['products'][] = $f['product_id'];
				if(!isset($optsAlreadySet[ $iter ])) {
					if($this->htmlTypeOptioned($prodFields[ $iter ]['htmltype_id'])) {
						$prodFields[ $iter ]['options'] = $this->getOptionsForField($id);
						if(in_array((int)$prodFields[ $iter ]['htmltype_id'], array(5/*Checkboxes*/, 12/*Select List*/))) {
							$prodFields[ $iter ]['default_value'] = utils::unserialize($prodFields[ $iter ]['default_value']);
						}
					}
					$prodFields[ $iter ]['mandatory'] = (int) $prodFields[ $iter ]['mandatory'];
					$prodFields[ $iter ]['active'] = (int) $prodFields[ $iter ]['active'];
					$optsAlreadySet[ $iter ] = 1;
					$originalIds[ $prodFields[ $iter ]['original_id'] ] = 1;
				}
			}
			if(!empty($prodFields) && isset($d['pid']) && count($originalIds) > 1) {
				$newProdFields = array();
				foreach($prodFields as $prodField) {
					if(!isset($originalIds[ $prodField['id'] ])) {
						$newProdFields[] = $prodField;
		}
				}
				$prodFields = $newProdFields;
			}
		}
		return $prodFields;
	}
	public function htmlTypeOptioned($typeId) {
		return in_array((int)$typeId, array(5, 9, 10, 12));
	}
	public function getOptionsForField($fieldId) {
		$options = array();
		$optionsFromDb = frame::_()->getTable('products_fields_options')->orderBy(array('sort_order', 'id'))->get('*', array('products_field_id' => $fieldId));
		if(!empty($optionsFromDb)) {
			$options = $optionsFromDb;
		}
		return $options;
	}
	public function removeProductField($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			frame::_()->getTable('products_fields')->delete(array('id' => $id));
			frame::_()->getTable('products_fields_options')->delete(array('products_field_id' => $id));
			frame::_()->getTable('products_fields_to_categories')->delete(array('products_field_id' => $id));
			frame::_()->getTable('products_fields_to_products')->delete(array('products_field_id' => $id));
			// Remove all linked IDs
			$linkedFields = frame::_()->getTable('products_fields')->get('*', array('original_id' => $id));
			if(!empty($linkedFields)) {
				foreach($linkedFields as $f) {
					$this->removeProductField(array('id' => $f['id']));
				}
			}
		} else
			$this->pushError (lang::_('Invalid Product Field ID'));
		return false;
	}
	public function getForProduct($d = array()) {
		$pid = isset($d['pid']) ? (int) $d['pid'] : 0;
		if($pid) {
			$conditions = array('pid' => $pid);
			$categories = frame::_()->getModule('products')->getCategories(array('pid' => $pid));
			if(!empty($categories)) {
				$conditions['categories'] = array();
				foreach($categories as $c) {
					$conditions['categories'][] = $c->term_taxonomy_id;
				}
			}
			$conditions = array_merge($d, $conditions);
			return $this->getList($conditions);
		}
		return false;
	}
	public function duplicateFromProduct($originalPid, $destPid) {
		$originalFields = $this->getForProduct(array('pid' => $originalPid));
		if(!empty($originalFields)) {
			foreach($originalFields as $field) {
				$this->duplicateField($field['id'], $destPid);
			}
		}
	}
	public function duplicateField($originalId, $pid = 0, $d = array()) {
		$field = $this->getById($originalId);
		unset($field['id']);
		$field['options'] = isset($d['options']) ? $d['options'] : $field['options'];
		if(!empty($field['options'])) {
			foreach($field['options'] as $i => $opt) {
				unset($field['options'][$i]['id']);
			}
		}
		if(isset($field['categories']))
			unset($field['categories']);
		if(isset($field['products']))
			unset($field['products']);
		
		if($pid)
			$field['products'] = $pid;
		$field['original_id'] = $originalId;
		return $this->saveProductField($field);
	}
	public function getProductFieldsFromRequest($d = array()) {
		$selectedFields = array();
		if(isset($d['options']) && !empty($d['options'])) {
			foreach($d['options'] as $fId => $value) {
				$selectedFields[ $fId ] = $this->getById($fId);
				$selectedFields[ $fId ]['selected'] = $value;
				switch((int) $selectedFields[ $fId ]['htmltype_id']) {
                    case 9:		// selectbox
					case 10:    // radiobuttons
						if(isset($selectedFields[ $fId ]['options']) && !empty($selectedFields[ $fId ]['options'])) {
							foreach($selectedFields[ $fId ]['options'] as $opt) {
								if($opt['id'] == $value) {
									$selectedFields[ $fId ]['displayValue'] = $opt['label'];
									break;
								}
							}
						}
                        break;
                    case 5:		//checkboxlist
					case 12:	//selectlist
                        $displayValue = array();
						if(!empty($value) && is_array($value) && isset($selectedFields[ $fId ]['options']) && !empty($selectedFields[ $fId ]['options'])) {
							foreach($selectedFields[ $fId ]['options'] as $opt) {
								if(in_array($opt['id'], $value)) {
									$displayValue[] = $opt['label'];
								}
							}
						}
                        $selectedFields[ $fId ]['displayValue'] = $displayValue;
                        break;
                    default:
                        $selectedFields[ $fId ]['displayValue'] = $value;
                        break;
                }
			}
		}
		return $selectedFields;
	}
}
 