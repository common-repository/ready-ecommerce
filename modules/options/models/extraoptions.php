<?php
class extraoptionsModel extends model {
    /**
     * Deletes extra option
     * 
     * @param array $d
     * @return string 
     */
    public function deleteOption($d = array()) {
        $res = new response();
        $id = isset($d['id']) ? $d['id'] : 0;
        if ($id && is_numeric($id)) {
            $extra_option = frame::_()->getTable('extraoptions')->get('ef_id', array('id'=>$id));
            $delete = frame::_()->getTable('extraoptions')->delete(array('id'=>$id));
            if ($delete) {
                frame::_()->getTable('extrafieldsvalue')->delete(array('value'=>$id, 'ef_id' =>$extra_option[0]['ef_id']));
                return '1';
            } else {
                return lang::_('There was an error while deleting the option. Please try again');
            }
        } else {
            frame::_()->getTable('extraoptions')->delete($d);
        }
        return lang::_('Invalid Extra Field Option');
    }
    /**
     * Saves extra field options 
     * @param array $options
     * @param int $id 
     */
    public function saveOptions($options, $id, $d = array()) {
		$existingOpts = $this->get(array(
			'ef_id' => $id
		));
		$existingOptsIds = array();
		if(!empty($existingOpts)) {
			foreach($existingOpts as $opt) {
				$existingOptsIds[] = $opt['id'];
			}
		}

		$options['sort_order'] = isset($options['sort_order']) ? $options['sort_order'] : array();
		$options['price'] = isset($options['price']) ? $options['price'] : array();
		$options['price_absolute'] = isset($options['price_absolute']) ? $options['price_absolute'] : array();

		$options['sort_order'] = array_map('intval', $options['sort_order']);
		$options['price'] = array_map('floatval', $options['price']);

        $options['data'] = isset($options['data']) ? $options['data'] : array();
		$options['data']['price'] = isset($options['data']['price']) ? $options['data']['price'] : array();
		$options['data']['weight'] = isset($options['data']['weight']) ? $options['data']['weight'] : array();
		$options['data']['onlyForPids'] = isset($options['data']['onlyForPids']) ? $options['data']['onlyForPids'] : array();
		
        if (!empty($options)) {
			foreach($options['value'] as $i => $opt) {
                $options['value'][$i] = trim($options['value'][$i]);
                if(empty($options['value'][$i]))    continue;
                $dbData = array(
                    'ef_id'=> $id,
                    'value' => $options['value'][$i], 
                    'data' => utils::jsonEncode(array(
                        'price' => (isset($options['data']['price'][$i]) ? $options['data']['price'][$i] : 0), 
                        'weight' => (isset($options['data']['weight'][$i]) ? $options['data']['weight'][$i] : 0),
                        'onlyForPids' => (isset($options['data']['onlyForPids'][$i]) ? $options['data']['onlyForPids'][$i] : array()),
                    )),
					'price' => (isset($options['price'][$i]) ? $options['price'][$i] : 0),
					'sort_order' => (isset($options['sort_order'][$i]) ? $options['sort_order'][$i] : 0),
					'price_absolute' => (isset($options['price_absolute'][$i]) ? 1 : 0),	// This is checkbox
                );
				if($i && in_array($i, $existingOptsIds)) {
					frame::_()->getTable('extraoptions')->update($dbData, $i);
				} else {
					frame::_()->getTable('extraoptions')->insert($dbData);
				}
            }
			if(!empty($existingOptsIds)) {	// Remove unused options
				foreach($existingOptsIds as $optId) {
					if(!isset($options['value'][$optId])) {
						$this->deleteOption(array('id' => $optId));
					}
				}
			}
        }
    }
    protected function _itemFromDb($item) {
        if(is_string($item['data']))
            $item['data'] = utils::jsonDecode($item['data']);
        return $item;
    }
    public function get($d = array()) {
        $items = array();
        $fromDb = frame::_()->getTable('extraoptions')->get('*', $d);
        foreach($fromDb as $k => $v) {
            $items[$k] = $this->_itemFromDb($v);
        }
        return $items;
    }
}
?>
