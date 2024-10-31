<?php
class optionsModel extends model {
    protected $_allOptions = array();
    public function get($d = array()) {
        $this->_loadOptions();
        $code = false;
        if(is_string($d))
            $code = $d;
        elseif(is_array($d) && isset($d['code']))
            $code = $d['code'];
        if($code) {
            $opt = $this->_getByCode($code);
            if(isset($d['what']) && isset($opt[$d['what']]))
                return $opt[$d['what']];
            else
                return $opt['value'];
        } else {
            return $this->_allOptions;
        }
    }
	public function getByCategories() {
		$this->_loadOptions();
		$categories = array();
		foreach($this->_allOptions as $opt) {
			if(empty($categories[ (int)$opt['cat_id'] ]))
				$categories[ (int)$opt['cat_id'] ] = array('cat_id' => $opt['cat_id'], 'cat_label' => $opt['cat_label'], 'opts' => array());
			$categories[ (int)$opt['cat_id'] ]['opts'][] = $opt;
		}
		ksort($categories);
		return $categories;
	}
	public function getByCode($d = array()) {
		$res = array();
		$codeData = $this->get($d);
		if(empty($d)) {
			// Sort by code
			foreach($codeData as $opt) {
				$res[ $opt['code'] ] = $opt;
			}
		} else
			$res = $codeData;
		return $res;
	}
    /**
     * Load all options data into protected array
     */
    protected function _loadOptions() {
        if(empty($this->_allOptions)) {
            $options = frame::_()->getTable('options');
            $htmltype = frame::_()->getTable('htmltype');
			$optionsCategories = frame::_()->getTable('options_categories');
            $this->_allOptions = $options->innerJoin($htmltype, 'htmltype_id')
					->leftJoin($optionsCategories, 'cat_id')
					->orderBy(array('cat_id', 'sort_order'))
                    ->getAll($options->alias(). '.*, '. $htmltype->alias(). '.label AS htmltype, '. $optionsCategories->alias(). '.label AS cat_label');
            foreach($this->_allOptions as $i => $opt) {
                if(!empty($this->_allOptions[$i]['params'])) {
                    $this->_allOptions[$i]['params'] = utils::unserialize($this->_allOptions[$i]['params']);
                }
				if(empty($this->_allOptions[$i]['cat_id'])) {	// Move all options that have no category - to Other
					$this->_allOptions[$i]['cat_id'] = 6;
					$this->_allOptions[$i]['cat_label'] = 'Other';
				}
            }
        }
    }
    /**
     * Returns option data by it's code
     * @param string $code option's code
     * @return array option's data
     */
    protected function _getByCode($code) {
        $this->_loadOptions();
        if(!empty($this->_allOptions)) {
            foreach($this->_allOptions as $opt) {
                if($opt['code'] == $code)
                    return $opt;
            }
        }
        return false;
    } 
    public function put($d = array()) {
        $res = new response();
        $id = isset($d['id']) ? intval($d['id']) : NULL;
        if(!$id && isset($d['code'])) {
            /*if($d['code'] == 'default_theme') {
                $selectedTpl = frame::_()->getModule('templates')->getModel()->get(array('code' => $d['value']));
                if(is_object($selectedTpl) && ((isset($selectedTpl->type) && $selectedTpl->type == 'Paid') ||
                        in_array($d['value'], array('startcommerce')))) {
                    $allWpTemplates = get_themes();
                    $selectThemeCode = (string) $selectedTpl->related_wp_theme;
                    if(!isset($allWpTemplates[ $selectThemeCode ])) {
                        $res->pushError(lang::_('This is part of the commercial WordPress theme, wich you do not have. Check <a href="http://readyshoppingcart.com/order/" target="_blank">our website</a> for available themes for WP.'));
                        return $res;
                    }
                }
            }*/
            $d['what'] = 'id';
            $id = $this->get($d);
            $id = intval($id);
        }
        if($id) {
            if(frame::_()->getTable('options')->update($d, array('id' => $id))) {
                $res->addMessage(lang::_('Option update Successful'));
                $res->addData('value', $d['value']);
                $res->code = $id;
            } else
                $res->addError(lang::_('Options update Failed'));
        } else {
            $res->addError(lang::_('Invalid option ID'));
        }
        return $res;
    }

}
?>
