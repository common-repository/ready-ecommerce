<?php
class taxesModel extends model {
    public function get($d = array()) {
        parent::get($d);
        $fields = NULL;
        if(isset($d['id']) && is_numeric($d['id'])) {
            if($d['id']) {
                frame::_()->getTable('taxes')->fillFromDB($d['id']);
            }
            $fields = frame::_()->getTable('taxes')->getFields();
        } elseif(!empty($d) && is_array($d)) {
            $fields = frame::_()->getTable('taxes')->get('*', $d);
        } else {
            $fields = frame::_()->getTable('taxes')->getAll();
            if(!empty($fields)) {
                for($i = 0; $i < count($fields); $i++) {
                    $fields[$i]['data'] = fieldAdapter::_($fields[$i]['data'], 'userFieldDestFromDB', fieldAdapter::STR);
                }
            }
        }
        return $fields;
    }
    public function post($d = array()) {
        $res = new response();
        $d = prepareParams($d);
		if(isset($d['data']) 
			&& isset($d['data']['dest']) 
			&& is_array($d['data']['dest']) 
			&& !empty($d['data']['dest'])
		) {
			if($id = frame::_()->getTable('taxes')->insert($d)) {
				$res->messages[] = lang::_('Tax Added');
				$res->data = array(
					'id' => $id, 
					'label' => $d['label'],
					'rate' => $d['data']['rate']
				);
			} else {
				if($tableErrors = frame::_()->getTable('taxes')->getErrors())
					$res->pushError($tableErrors);
				else
					$res->pushError(lang::_('Failled to add tax'));
			}
		} else 
			$res->pushError(lang::_('Select "Apply Tax" address destination'), 'data[dest][]');
        return $res;
    }
    public function put($d = array()) {
        $res = new response();
        $id = $d['id'];
        if(is_numeric($id)) {
			if(isset($d['data']) 
				&& isset($d['data']['dest']) 
				&& is_array($d['data']['dest']) 
				&& !empty($d['data']['dest'])
			) {
				if(frame::_()->getTable('taxes')->update($d, array('id' => $id))) {
					$res->messages[] = lang::_('Tax Updated');
					$res->data = array(
						'id' => $id, 
						'label' => $d['label'],
						'rate' => $d['data']['rate']
					);
				} else {
					if($tableErrors = frame::_()->getTable('taxes')->getErrors())
						$res->pushError($tableErrors);
					else
						$res->pushError(lang::_('Failled to update tax'));
				}
			} else 
				$res->pushError(lang::_('Select "Apply Tax" address destination'), 'data[dest][]');
        } else 
            $res->pushError(lang::_('Error tax ID'));

        return $res;
    }
    public function delete($d = array()) {
        $res = new response();
        $id = $d['id'];
        if(is_numeric($id)) {
            if(frame::_()->getTable('taxes')->delete($d, array('id' => $id))) {
                $res->messages[] = lang::_('Tax Deleted');
            } else
                $res->errors[] = lang::_('Tax Delete Failed');
        } else 
            $res->errors[] = lang::_('Error Tax ID');
        return $res;
    }
}
?>
