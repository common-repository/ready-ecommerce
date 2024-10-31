<?php
class extrafieldsModel extends model {
    public function get($d = array(), $where = '', $parent = '') {
        parent::get($d);
        $res = array();
        $extrafields = frame::_()->getTable('extrafields');
        $htmltype = frame::_()->getTable('htmltype');
        $extraoptions = frame::_()->getTable('extraoptions');
        $extrafieldsvalue = frame::_()->getTable('extrafieldsvalue');
        if(isset($d['id']) && is_numeric($d['id'])) {
            if($d['id']) {
                $extrafields->fillFromDB($d['id'], $where);
            }
            $fields = $extrafields->getFields();
            $fields['mandatory']->addHtmlParam('checked', (bool)$fields['mandatory']->value);
            $fields['types'] = array();
            $fields['type_labels'] = array();
            if ($parent == '') {
                $parent = $fields['parent']->value;
            }
            $types = $this->getHtmlTypes($parent);
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
                $fields['type_labels'][$t['id']->value] = $t['description']->value;
            }
            $res = $fields;
        } else {
            
            $extrafields->innerJoin($htmltype, 'htmltype_id');
            $extrafields->orderBy($extrafields->alias(). '.ordering ASC');
            $extrafields->leftJoin($extraoptions, 'ef_id');
            $extrafields->arbitraryJoin('LEFT JOIN '. $extrafieldsvalue->getTable(). ' AS '. $extrafieldsvalue->alias(). ' ON '. $extrafieldsvalue->alias(). '.opt_id = '. $extraoptions->alias(). '.id');
            $fields = $extrafields->get($extrafields->alias(). '.*, '.
                                        $htmltype->alias(). '.label as `type`, '. 
                                        $htmltype->alias(). '.description as type_label, '.
                                        
										$extraoptions->alias(). '.id as opt_id, '. 
                                        $extraoptions->alias(). '.value as opt_value, '.
                                        $extraoptions->alias(). '.data as opt_data, '.
										$extraoptions->alias(). '.price as opt_main_price, '.
										$extraoptions->alias(). '.price_absolute as opt_main_price_absolute, '.
										$extraoptions->alias(). '.sort_order as opt_main_sort_order, '.
                                        
										$extrafieldsvalue->alias(). '.price as opt_price, '.
                                        $extrafieldsvalue->alias(). '.price_absolute as opt_price_absolute, '.
                                        $extrafieldsvalue->alias(). '.disabled as opt_disabled, '.
                                        $extrafieldsvalue->alias(). '.parent_id as opt_parent_id, '.
                                        $extrafieldsvalue->alias(). '.sort_order as opt_sort_order, '.
										$extrafieldsvalue->alias(). '.id as opt_val_id ',
                                        
										$where);
			$removeOptions = array();
			$skipFields = array();
			// Check for product only fields.
			foreach($fields as $f) {
				$isForCurrentParrent = (int)$parent === (int)$f['opt_parent_id'];
				if(($isForCurrentParrent)&&(!empty($f['opt_val_id'])))
					$skipFields[$f['id']] = true;
			}
            foreach($fields as $f) {
				if((int)$f['opt_parent_id'] && (int)$parent && (int)$parent !== (int)$f['opt_parent_id'])
					continue;
                if(!isset($res[$f['id']]))
                    $res[$f['id']] = $f;
				// Skip fields for other products and if not empty (LEFT JOIN).
				$isForCurrentParrent = (int)$parent === (int)$f['opt_parent_id'];
				
				if((!$isForCurrentParrent)&&(!empty($f['opt_val_id'])) &&(isset($skipFields[$f['id']]))&&($skipFields[$f['id']]))
					continue;
				
                $res[$f['id']]['opt_values'][$f['opt_id']]['id'] = $f['opt_id'];
                $res[$f['id']]['opt_values'][$f['opt_id']]['value'] = $f['opt_value'];
                $res[$f['id']]['opt_values'][$f['opt_id']]['data'] = utils::jsonDecode($f['opt_data']);
				$forceUseForThisParent = (int)$parent && isset($d['forceIncludeData']) && $d['forceIncludeData'];
                if((($isForCurrentParrent || empty($f['opt_parent_id'])) && (int)$parent)
					|| $forceUseForThisParent
				) {
                    if((empty($f['opt_disabled']) || (!$isForCurrentParrent && $forceUseForThisParent)) || isset($d['includeDisabled'])) {
                        $res[$f['id']]['opt_values'][$f['opt_id']]['price'] = (is_null($f['opt_price']) || !$isForCurrentParrent) ? $f['opt_main_price'] : $f['opt_price'];

                        $res[$f['id']]['opt_values'][$f['opt_id']]['sort_order'] = (is_null($f['opt_sort_order']) || !$isForCurrentParrent) ? $f['opt_main_sort_order'] : $f['opt_sort_order'];
                        $res[$f['id']]['opt_values'][$f['opt_id']]['price_absolute'] = (is_null($f['opt_price_absolute']) || !$isForCurrentParrent) ? $f['opt_main_price_absolute'] : $f['opt_price_absolute'];
                        $res[$f['id']]['opt_values'][$f['opt_id']]['disabled'] = $isForCurrentParrent ? $f['opt_disabled'] : false;
                        $res[$f['id']]['opt_values'][$f['opt_id']]['parent_id'] = $isForCurrentParrent ? $f['opt_parent_id'] : $parent;
						// opt_val_id null if not our product. So we can add them instead of updating.
						$res[$f['id']]['opt_values'][$f['opt_id']]['opt_val_id'] = $isForCurrentParrent ? $f['opt_val_id'] : "";
                    } else {
						if(!isset($removeOptions[$f['id']]))
							$removeOptions[$f['id']] = array();
						$removeOptions[$f['id']][] = $f['opt_id'];
                    }
                }
            }
			// Remove disabled options
			// We didn't simple unset it earlier as disabled option can be included into responce from db more than one time
			if(!empty($removeOptions) && !empty($res)) {
				foreach($removeOptions as $fId => $remove) {
					if(isset($res[$fId]) && isset($res[$fId]['opt_values']) && !empty($res[$fId]['opt_values'])) {
						foreach($remove as $rId) {
							if(isset($res[$fId]['opt_values'][$rId]))
								unset($res[$fId]['opt_values'][$rId]);
						}
					}
				}
			}
        }
        return $res;
    }
    /**
     * Validate user input according to existing rules in database
     */
    public function validate($d = array()) {
        if(isset($d['allFields']))
            $all = $d['allFields'];
        else
            $all = $this->get();
        $errors = array();
        foreach($all as $f) {
            //if(isset($d[$f['code']])) {
                $validate = array();
                if($f['mandatory'])
                    $validate[] = 'notEmpty';
                if($f['validate'])
                    $validate[] = $f['validate'];
                if($validate) {
                    $field = new field($f['code'], $f['type'], '', $f['default_value'], $f['label'], 0, array(), $validate);
                    $field->setValue($d[$f['code']]);
                    if($e = validator::_($field)) {
                        $errors = array_merge($errors, $e);
                    }
                }
            //}
        }
        return $errors;
    }
    public function put($d = array(),$is_variation = false) {
        $nameForRes = $d['parent'] == 'user' ? 'Userfield' : 'Extrafield';
        $res = new response();
        $id = $d['id'];
        $options = array();
        if(!empty($d['params']) && is_array($d['params'])) {
            $d['params'] = db::prepareHtml($d['params']);
        }
        $d = prepareParams($d, $options);
        if(is_numeric($id)) {
            if(!isset($d['ignore']))
                $d['ignore'] = array();
			if(!in_array('mandatory', $d['ignore'])) {
				$d['mandatory'] = isset($d['mandatory']) ? 1 : 0;
			}
            if ((!isset($d['ordering']) || !is_numeric($d['ordering'])) && !in_array('ordering', $d['ignore'])) {
                $d['ordering'] = 0;
            }
			if(is_array($d['default_value']))
				$d['default_value'] = utils::serialize ($d['default_value']);
            if(frame::_()->getTable('extrafields')->update($d, array('id' => $id))) {
				if(!$is_variation)
					frame::_()->getModule('options')->getModel('extraoptions')->saveOptions($options, $id, $d);                
                $res->messages[] = lang::_($nameForRes. ' Updated');
                $exFields = frame::_()->getTable('extrafields')->getById($id);
                $newType = frame::_()->getTable('htmltype')->getById($exFields['htmltype_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $exFields['label'], 
                    'code' => $exFields['code'], 
                    'type' => $newType,
                    'active' => $d['active'],
                );
            } else {
                if($tableErrors = frame::_()->getTable('extrafields')->getErrors()) 
                    $res->errors = array_merge($res->errors, $tableErrors);
                else
                    $res->errors[] = lang::_($nameForRes. ' Update Failed');
            }
        } else {
            $res->errors[] = lang::_('Error '. $nameForRes. ' ID');
        }
        
        return $res;
    }
    public function post($d = array()) {
        $nameForRes = $d['parent'] == 'user' ? 'Userfield' : 'Extrafield';
        $res = new response();
        $options = array();
        $d = prepareParams($d, $options);
		if(is_array($d['default_value']))
			$d['default_value'] = utils::serialize ($d['default_value']);
        if($id = frame::_()->getTable('extrafields')->insert($d)) {
			if(!isset($d['ignoreOptionsInsert']) || !$d['ignoreOptionsInsert'])
				frame::_()->getModule('options')->getModel('extraoptions')->saveOptions($options, $id);    
            $res->messages[] = lang::_($nameForRes. ' Added');
            $newType = frame::_()->getTable('htmltype')->getById($d['htmltype_id'], 'label');
            $newType = $newType['label'];
            $res->data = array(
                'id' => $id, 
                'label' => $d['label'], 
                'code' => $d['code'], 
                'type' => $newType
            );
        } else
            $res->errors[] = lang::_($nameForRes. ' Insert Failed');
        return $res;
    }
    public function getHtmlTypes($parent) {
        static $types;
        if(!$types)
            $types = frame::_()->getTable('htmltype')->fillFromDB();
        foreach ($types as $key => $value) {
            switch ($parent) {
                /**
                 * @deprecated moved to productfieldsModel
                 */
                /*case 'products':
                       if (!in_array($types[$key]['label']->value, array('text', 'radiobuttons', 'checkboxlist', 'selectbox', 'selectlist'))) {
                           unset($types[$key]);
                       }
                    break;*/
                case 'user':
                    if (!in_array($types[$key]['label']->value, array('text', 'radiobuttons', 'countryList', 'password', 'checkboxlist', 'selectbox', 'selectlist', 'statesList', 'textFieldsDynamicTable', 'datepicker'))) {
                           unset($types[$key]);
                       }
                    break;
                default: 
                    break;
            }
        }
        return $types;
    }
    /**
     * Delete Extra Field
     * 
     * @param array $d
     * @return response 
     */
    public function delete($d = array()) {
        $nameForRes = $d['parent'] == 'user' ? 'Userfield' : 'Extrafield';
        $res = new response();
        $id = $d['id'];
        if(is_numeric($id)) {
            if(frame::_()->getTable('extrafields')->delete($d, array('id' => $id))) {
                frame::_()->getTable('extraoptions')->delete(array('ef_id'=> $id));
                frame::_()->getTable('extrafieldsvalue')->delete(array('ef_id'=> $id));
                $res->messages[] = lang::_($nameForRes. ' Deleted');
            } else
                $res->errors[] = lang::_($nameForRes. ' Delete Failed');
        } else 
            $res->errors[] = lang::_('Error '. $nameForRes. ' ID');
        return $res;
    }
       
}
?>