<?php
// we need to use extrafields model
require_once 'extrafields.php';

class productfieldsModel extends extrafieldsModel {
    /**
     * Function to get all product extra fields
     * @param array $d
     * @return array 
     */
    public function get($d = array(), $where = '') {
        if(!isset($d['pid']))
            $d['pid'] = 0;
        $items = parent::get($d, $where. " parent = '". S_PRODUCT. "'", $d['pid']);
        if (isset($d['id']) && is_numeric($d['id'])) {
            $items['destination']->adapt['html'] = 'productFieldCategories';
            $items['destination']->setHtml('selectlist');
            $items = $this->_itemFromDb($items);
        } else {    //If extracted more than 1 item
            $res = array();
            foreach($items as $key => $val) {
                $res[$key] = $this->_itemFromDb($val);
            }
            $items = $res;
        }
        return $items;
    }
    public function getHtmlTypes($parent) {
        $types = parent::getHtmlTypes($parent);
        foreach ($types as $key => $value) {
            if (!in_array($types[$key]['label']->value, array('text', 'checkbox', 'checkboxlist', 'datepicker', 'selectbox', 'radiobuttons', 'countryList', 'selectlist', 'countryListMultiple', 'statesList', 'textarea'))) {
                unset($types[$key]);
            }
        }
        return $types;
    }
    /**
     * Convert item after extract it from DB
     */
    protected function _itemFromDb($item) {
		$item['params'] = isset($item['params']) ? $item['params'] : '';
		$item['destination'] = isset($item['destination']) ? $item['destination'] : '';
        if(is_string($item['params']))
            $item['params'] = utils::jsonDecode($item['params']);
        if(is_string($item['destination']))
            $item['destination'] = utils::jsonDecode($item['destination']);
        unset($item['opt_data']);   //this is on opt_data key
		// Try to unserrialize default value - for case if it is array
		$defaultValueArray = utils::unserialize(is_object($item['default_value']) ? $item['default_value']->getValue() : $item['default_value']);
		if($defaultValueArray !== false) {
			if(is_object($item['default_value'])) {
				$item['default_value']->setValue($defaultValueArray);
				$item['default_value']->setHtml('selectlist');
			} else
				$item['default_value'] = $defaultValueArray;
		}
        return $item;
    }
    /**
     * Function to update product extra fields
     * @param array $d
     * @return array 
     */
    public function put($d = array(),$is_variation = false) {
        $d['parent'] = S_PRODUCT;
        $d['cost'] = isset($d['price']) ? $d['price'] : 0;
		if(!isset($d['active']))
			$d['active'] = 0;
		$d['destination'] = isset($d['destination']) ? $d['destination'] : '';
        $res = parent::put($d,$is_variation);
		if(!$res->error() && isset($res->data) && isset($res->data['id'])) {
			$res->setHtml( $this->getModule()->getController()->getView('productfields')->productfieldsForm(array('id' => $res->data['id'])) );
		}
		return $res;
    }
    /**
     * Function to store product extra fields
     * @param array $d
     * @return array 
     */
    public function post($d = array()) {
        $d['parent'] = S_PRODUCT;
        $d['ordering'] = 0;
        $d['cost'] = $d['price'];
        $res = parent::post($d);
        if (isset($d['show_field']) && is_numeric($d['show_field'])) {
            if ($d['show_field']) {
                $field = toeCreateObj('field', array($res->data['code'], $res->data['type'],'other','',$res->data['label']));
                $field->id = $res->data['id'];
                $output = '<label for="'.$field->name.'">'.$field->label.':</label>';
                $res->field = $output.'<div class="product_field">'.$field->viewField($res->data['code']).'</div><br clear="all" />';
            }
        }
		if(!$res->error() && isset($res->data) && isset($res->data['id'])) {
			$res->setHtml( $this->getModule()->getController()->getView('productfields')->productfieldsForm(array('id' => $res->data['id'])) );
		}
        return $res;
    }
    /**
     * Returns the array of all product extra fields
     * @param bool $params['unsetEmptyFields'] - unset or not empty extra fields from result
     * @return array of fields
     */
    public function getProductExtraField($post, $params = array('unsetEmptyFields' => false, 'getForCategory' => array(), 'where' => '')) {
        $pid = 0;
        if(is_object($post))
            $pid = $post->ID;
        elseif(is_numeric($post))
            $pid = (int) $post;
        if(empty($params['getForCategory']) && !empty($post))
            $categories = frame::_()->getModule('products')->getModel('products')->getProductTerms($pid, 'term_taxonomy_id');
        else
            $categories = $params['getForCategory'];
		if(!isset($params['where']))
			$params['where'] = '';
        $allFields = $this->get(array('pid' => $pid), $params['where']);
        $result = array();
        if($allFields) {
            $fieldsToUnset = array();
            foreach($allFields as $f) {
				$emptyCat = false;
				$emptyProduct = false;
                $destination = is_array($f['destination']) ? $f['destination'] : fieldAdapter::userFieldDestFromDB($f['destination']);
				if(empty($destination))
					continue;
				if((isset($destination['categories']) && !is_array($destination['categories'])) || !isset($destination['categories']))
					$destination['categories'] = array();
				if($destination['categories'] != array(0) /*All categories selected*/) {
					$field_cat = array_intersect($destination['categories'], $categories);
					if (empty($field_cat) && !empty($categories))
						$emptyCat = true;
				}
                if(!isset($destination['pids'])||(is_array($destination['pids']) && !in_array($pid, $destination['pids'])) && ($destination['pids'] != array(0) /*All products selected*/)) {    //Check for product
                    $emptyProduct = true;    
                }
				if($emptyCat && $emptyProduct)
					continue;
                $result[$f['code']] = toeCreateObj('field', array($f['code'], $f['type'], 'other', '', $f['label']));
                $result[$f['code']]->id = $f['id'];
                $result[$f['code']]->mandatory = $f['mandatory'];
                $result[$f['code']]->destination = $destination;
                $result[$f['code']]->htmlParams = utils::jsonDecode($f['params']);
                $result[$f['code']]->default_value = $f['default_value'];
                $result[$f['code']]->data = $f;
                //$result[$f['code']]->adapt['html'] = 'productFieldCategories';    //Why? - I don't know....
                if(isset($f['opt_values'])) {
                    $htmlOptions = array();
                    foreach($f['opt_values'] as $k => $v) {
                        /*if(empty($params['getForCategory']) && in_array($pid, $v['excludePids'])) {
                            if((int) $f['htmltype_id'] == 1) //text
                                $fieldsToUnset[] = $f['code'];
                            continue;
                        }*/
                        switch($f['htmltype_id']) {
                            case 5:  //checkboxlist
                                $htmlOptions[$k] = array('id' => $v['id'], 'text' => $v['value'], 'checked' => false);     // @see extrafieldsModel::get()
                                break;
                            default:
                                $htmlOptions[$k] = $v['value'];     // @see extrafieldsModel::get()
                                break;
                        }
                    }
                    if(empty($htmlOptions))
                        $fieldsToUnset[] = $f['code'];
                    $result[$f['code']]->addHtmlParam('options', $htmlOptions);
                }
            }
            if($params['unsetEmptyFields'] && !empty($fieldsToUnset)) {
                foreach($fieldsToUnset as $code) {
                    unset($result[$code]);
                }
            }
        }
        return $result;
    }
    /**
     * Returns the values of product extra fields
     * 
     * @param object $post
     * @return array $items
     */
    public function getProductExtraFieldValue($post) {
        $extra_values = frame::_()->getTable('extrafieldsvalue');
        $extra_fields = frame::_()->getTable('extrafields');
        $extra_values->innerJoin($extra_fields, 'ef_id');
        $conditions = $extra_values->alias().'.parent_id ='. $post->ID.' AND '.$extra_values->alias().'.parent_type = "'. S_PRODUCT. '"';
        $items = $extra_values->get('ef_id,value',$conditions);
        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[$item['ef_id']] = $item['value'];
            }
        }
        return $result;
    }
    public function getFieldsDesc($d = array()) {
        $where = '';
        $optionsFromDb = array();
        $options = array();
        $selectIds = array();
        if(isset($d['options']) && is_array($d['options']) && !empty($d['options'])) {
            foreach($d['options'] as $id => $o) {
                if(!$this->isValEmpty($o)) {  //!empty() can't be used here because value can be "0"
                    $selectIds[$id] = $o;
                }
            }
            $where = frame::_()->getTable('extrafields')->alias().'.id IN ("'. implode('", "', array_keys($selectIds)). '") AND ';
            if(!isset($d['pid']))
                $d['pid'] = 0;
			$d['forceIncludeData'] = true;
            $optionsFromDb = $this->get($d, $where);
        }
        if(!empty($optionsFromDb) && is_array($optionsFromDb)) {
            foreach($optionsFromDb as $oId => $o) {
                $options[$oId] = $o;
                $options[$oId]['selected'] = $selectIds[ $o['id'] ];
                switch((int) $o['htmltype_id']) {
                    case 4: //checkbox
                        $options[$oId]['displayValue'] = empty($options[$oId]['selected']) ? lang::_('No') : lang::_('Yes');
                        break;
                    case 11:        //countryList
                        $options[$oId]['displayValue'] = fieldAdapter::displayCountry($options[$oId]['selected']);
                        break;
                    case 15:        //statesList
                        $options[$oId]['displayValue'] = fieldAdapter::displayState($options[$oId]['selected']);
                        break;
                    case 9: case 10:    //selectbox, radiobuttons
                         $options[$oId]['displayValue'] = $o['opt_values'][ $options[$oId]['selected'] ]['value'];
                        break;
                    case 5: case 12:   //checkboxlist, selectlist
                        $displayValue = array();
                        if(!empty($options[$oId]['selected'])) {
                            if(is_array($options[$oId]['selected'])) {
                                foreach($options[$oId]['selected'] as $selectedValue) 
                                    $displayValue[] = $o['opt_values'][ $selectedValue ]['value'];
                            } else {
                                $displayValue[] = $options[$oId]['selected'];
                            }
                        }
                        $options[$oId]['displayValue'] = $displayValue;
                        break;
                    case 13:    //countryListMultiple
                        if(!empty($options[$oId]['selected']) && is_array($options[$oId]['selected'])) {
                            $options[$oId]['displayValue'] = array_map(array('fieldAdapter', 'displayCountry'), $options[$oId]['selected']);
                        } else {
                            $options[$oId]['displayValue'] = array();
                        }
                        break;
                    default:
                        $options[$oId]['displayValue'] = $options[$oId]['selected'];
                        break;
                }
            }
        }
        return $options;
    }
    /**
     * Check if value is empty, need separate function for this because empty() will give for "0" value true
     * @param mixed $val - values to check for
     * @return bool - true if option is empty, and false - if not
     */
    public function isValEmpty($val) {
		$val = is_array($val) ? toeMultArrayMap('trim', $val) : trim($val);
        return ($val === '' || $val === NULL || $val == array());
    }
	public function useTextBlock($htmlType) {
		return in_array($htmlType, array('text', 'textarea'));
	}
	public function extractTextVal($f) {
		$textValue = $f->default_value;
		if(isset($f->data['opt_values']) && is_array($f->data['opt_values'])) {
			$firstOpt = array_shift($f->data['opt_values']);
			if(!empty($firstOpt) && is_array($firstOpt) && isset($firstOpt['value']) && $firstOpt['value'] != 'EMPTY') {
				$textValue = $firstOpt['value'];
			}
		}
		return $textValue;
	}
}
?>